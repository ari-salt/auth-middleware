<?php

namespace AriSALT\AuthMiddleware;

use Exception;
use AriSALT\AuthMiddleware\Exception as CIAMException;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use CoderCat\JWKToPEM\JWKConverter;
use Illuminate\Support\Arr;
use Firebase\JWT\JWT;

class CIAMAuthorizationService implements AuthorizationService
{
	private $userID;
	private $pemPublicKey;
	private $http;
	private $ciamConfig;
	private $jwkConverter;

	public function __construct(
		string $pemPublicKey,
		CIAMConfig $ciamConfig
	) {
		if (empty($pemPublicKey)) {
			throw new Exception('PEM public key is required');
		}
		if ($ciamConfig->isEmpty()) {
			throw new Exception('CIAM configuration is required');
		}

		$this->pemPublicKey = $pemPublicKey;
		$this->http = new Client([
			'base_uri' => $ciamConfig->host(),
			'timeout' => $ciamConfig->httpTimeout()
		]);
		$this->ciamConfig = $ciamConfig;
		$this->jwkConverter = new JWKConverter();
	}

	public function decode(string $token)
	{
		if (empty($token)) {
			throw new CIAMException('token is required');
		}

		$jwk = $this->jwk();
		$jwkKey = Arr::first($jwk->keys, function ($value) {
			return $value->alg === $this->ciamConfig->algorithm();
		});
		$pem = $this->jwkConverter->toPEM((array) $jwkKey);
		$decoded = JWT::decode($token, $pem, [$this->ciamConfig->algorithm()]);
		if (!in_array($decoded->iss, $this->ciamConfig->eligibleIIS())) {
			throw new CIAMException('IIS not eligible');
		}
		if (!in_array($decoded->aud, $this->ciamConfig->eligibleAudiences())) {
			throw new CIAMException('audience not allowed');
		}

		$this->userID = $decoded->sub;

		return $this;
	}

	public function jwk()
	{
		$jwk = Cache::get($this->pemPublicKey);
		if (! empty($jwk)) {
			return $jwk;
		}

		$response = $this->http->get(
			"/iam/v1/oauth2/realms/tsel/connect/jwk_uri",
			[
				'query' => [
					'client_id' => $this->ciamConfig->clientID(),
				]
			]
		);
		$jwk = json_decode($response->getBody()->getContents());

		Cache::set(
			$this->pemPublicKey,
			$jwk,
			Carbon::now()->addHours($this->ciamConfig->cacheExpirationHours())
		);

		return $jwk;
	}

	public function userID(): string
	{
		return $this->userID;
	}

	public function verify(string $token)
	{
		if (empty($token)) {
			throw new CIAMException('token is required');
		}

		$cacheKey = "CIAM_PROFILE_{$this->decode($token)->userID()}";
		$me = Cache::get($cacheKey);
		if (! empty($me)) {
			$this->userID = $me->_id;

			return $this;
		}

		$response = $this->http->get(
			"/iam/v1/profiles/me",
			[
				'headers' => [
					'Authorization' => "Bearer {$token}"
				]
			]
		);
		$me = json_decode($response->getBody()->getContents());

		Cache::set(
			$cacheKey,
			$me,
			Carbon::now()->addHours($this->ciamConfig->cacheExpirationHours())
		);

		$this->userID = $me->_id;

		return $this;
	}
}