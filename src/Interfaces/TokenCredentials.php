<?php

namespace App\Interfaces;

use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

class TokenCredentials implements CredentialsInterface
{
    private ?string $token = null;
    private bool $resolved = false;
    public function __construct(#[\SensitiveParameter] string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        if (is_null($this->token)) {
            // TODO
            throw new \LogicException("TOKEN ERROR HERE");
        }

        return $this->token;
    }

    public function markResolved(): void
    {
        $this->resolved = true;
        $this->token = null;
    }

    /**
     * @inheritDoc
     */
    public function isResolved(): bool
    {
        return true;
    }
}