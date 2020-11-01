<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // Passport::tokensExpireIn(now()->addDays(15));

        // Passport::refreshTokensExpireIn(now()->addDays(30));

        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::tokensExpireIn(now()->addMinutes(30));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        //http://127.0.0.1:8000/oauth/authorize/?client_id=10&redirect_uri=http://localhost&response_type=code

        Passport::enableImplicitGrant();
        //http://127.0.0.1:8000/oauth/authorize/?client_id=10&redirect_uri=http://localhost&response_type=token

        Passport::tokensCan([
            'manage-application' => 'Will use your full name, facebook and contact number'
        ]);
    }
}
