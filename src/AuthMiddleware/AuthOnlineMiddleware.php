<?php

namespace AriSALT\AuthMiddleware;

use AriSALT\AuthMiddleware\CIAMAuthorizationService;
use AriSALT\AuthMiddleware\CIAMConfig;
use AriSALT\AuthMiddleware\Exception as CIAMException;
use AriSALT\Logger\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;
use Exception;

class AuthOnlineMiddleware
{
	public function handle(
		Request $request,
		Closure $next,
		string $varName,
		string $logTitle,
		string $logFilename
	) {
		if (empty($request->bearerToken())) {
			return response()->json([
				'status' => false,
				'code' => 'AUTH401',
				'message' => null,
				'errorMessage' => [
					'token' => [__('message.required')]
				],
				'data' => null
			], Response::HTTP_UNAUTHORIZED);
		}

		$userID = '';

		try {
			$authorizationService = new CIAMAuthorizationService(
				env('PEM_PUBLIC_KEY'),
				new CIAMConfig(
					env('CIAM_ALGORITHM'), 
					(int) env('CIAM_CACHE_EXPIRATION_HOURS'),
					env('CIAM_CLIENT_ID'),
					explode(',', env('CIAM_AUDIENCES')),
					explode(',', env('CIAM_ISS')),
					env('CIAM_HOST'),
					(int) env('CIAM_HTTP_TIMEOUT')
				)
			);
			$userID = $authorizationService->verify($request->bearerToken())->userID();
		} catch (CIAMException $e) {
			Logger::logging($logTitle, $logFilename, $e->getMessage());
			return response()->json([
			   'status' => false,
				'code' => 'AUTH401',
			   'message' => null,
				'errorMessage' => [
					'token' => [$e->getMessage()],
				],
				'data' => null
			], Response::HTTP_UNAUTHORIZED);
		} catch (Exception $e) {
			Logger::logging($logTitle, $logFilename, $e->getMessage());
			return response()->json([
				'status' => false,
				'code' => 'AUTH500',
				'message' => null,
				'errorMessage' => [
					'token' => [$e->getMessage()]
				],
				'data' => null
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		$request->request->add([
			$varName => $userID
		]);

		return $next($request);
	}
}