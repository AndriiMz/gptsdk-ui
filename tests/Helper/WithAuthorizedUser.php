<?php

namespace Tests\Helper;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;

trait WithAuthorizedUser
{
    use InteractsWithAuthentication;

    protected User $user;

    public function withAuthorizedUser(): User
    {
        if (isset($this->user)) {
            return $this->user;
        }

        $user = User::factory()->create([
            'email_verified' => 1,
            'auth0' => 1,
            'name' => 'Andrew M',
            'email' => 'moroz@gpt-sdk.com'
        ]);

        $this->actingAs($user, 'auth0-session');

        $this->user = $user;

        return $user;
    }
}
