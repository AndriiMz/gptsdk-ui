<?php

namespace App\Guard;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Logto\Sdk\LogtoClient;

class LogtoGuard implements Guard
{
    private ?Authenticatable $user = null;

    public function __construct(
        private readonly LogtoClient $client,
    ) {}

    public function check()
    {
        return $this->user || $this->client->isAuthenticated();
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if (!$this->check()) {
            return null;
        }

        if ($this->user) {
            return $this->user;
        }

        $tokenClaims = $this->client->getIdTokenClaims();

        return User::where('external_id', $tokenClaims->sub)->first();
    }

    public function id()
    {
        return $this->user()?->id;
    }

    public function validate(array $credentials = [])
    {
        return false;
    }

    public function hasUser()
    {
        return $this->check();
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }
}
