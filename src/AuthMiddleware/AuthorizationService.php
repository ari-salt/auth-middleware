<?php

namespace AriSALT\AuthMiddleware;

interface AuthorizationService
{
	public function decode(string $token);
	public function jwk();
	public function userID(): string;
	public function verify(string $token);
}