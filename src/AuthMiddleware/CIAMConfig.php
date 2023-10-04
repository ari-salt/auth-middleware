<?php

namespace AriSALT\AuthMiddleware;

class CIAMConfig
{
	private $algorithm;
	private $cacheExpirationHours;
	private $clientID;
	private $eligibleAudiences;
	private $eligibleISS;
	private $host;
	private $httpTimeout;

	public function __construct(
		string $algorithm,
		int $cacheExpirationHours,
		string $clientID,
		array $eligibleAudiences,
		array $eligibleISS,
		string $host,
		int $httpTimeout
	) {
		$this->algorithm = $algorithm;
		$this->cacheExpirationHours = $cacheExpirationHours;
        $this->clientID = $clientID;
        $this->eligibleAudiences = $eligibleAudiences;
        $this->eligibleISS = $eligibleISS;
        $this->host = $host;
		$this->httpTimeout = $httpTimeout;
	}

	public function algorithm(): string {
		return $this->algorithm;
	}

	public function cacheExpirationHours(): int {
		return $this->cacheExpirationHours;
	}

	public function clientID(): string {
        return $this->clientID;
    }

	public function eligibleAudiences(): array {
        return $this->eligibleAudiences;
    }

	public function eligibleISS(): array {
        return $this->eligibleISS;
    }

	public function host(): string {
        return $this->host;
    }

	public function httpTimeout(): int {
        return $this->httpTimeout;
    }

	public function isEmpty(): bool {
		if (
			empty($this->algorithm) ||
			empty($this->cacheExpirationHours) ||
            empty($this->clientID) ||
            empty($this->eligibleAudiences) ||
            empty($this->eligibleISS) ||
            empty($this->host) ||
			empty($this->httpTimeout)
		) {
			return true;
		}
		return false;
	}
}