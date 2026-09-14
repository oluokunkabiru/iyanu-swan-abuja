<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    protected function actingAsApi(User $user): static
    {
        /** @var string $accessToken */
        $accessToken = Auth::guard('api')->login($user);

        return $this->withToken($accessToken);
    }
}
