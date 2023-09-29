<?php

namespace AriSALT\AuthMiddleware;

class CIAMConfig
{
	private string $algorithm;
	private int $cacheExpirationHours;
	private string $clientID;
	private array $eligibleAudiences;
	private array $eligibleIIS;
	private string $host;
	private int $httpTimeout;

	public function __construct(
		string $algorithm,
		int $cacheExpirationHours,
		string $clientID,
		array $eligibleAudiences,
		array $eligibleIIS,
		string $host,
		int $httpTimeout
	) {
		$this->algorithm = $algorithm;
		$this->cacheExpirationHours = $cacheExpirationHours;
        $this->clientID = $clientID;
        $this->eligibleAudiences = $eligibleAudiences;
        $this->eligibleIIS = $eligibleIIS;
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

	public function eligibleIIS(): array {
        return $this->eligibleIIS;
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
            empty($this->eligibleIIS) ||
            empty($this->host) ||
			empty($this->httpTimeout)
		) {
			return true;
		}
		return false;
	}
}