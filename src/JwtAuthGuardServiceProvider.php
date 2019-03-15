<?php

namespace Zoran\JwtAuthGuard;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class JwtAuthGuardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        app('auth')->provider('repository', function (Container $app, array $config) {
            return new JWTAuthUserFromRepositoryProvider($app, $config, $app['hash']);
        });

        app('auth')->extend('jwt-auth', function (Container $app, string $name, array $config) {
            $guard = new JwtAuthGuard(
                app('tymon.jwt.auth'),
                app('auth')->createUserProvider($config['provider']),
                $app['request']
            );
            $app[$name] = $guard;
            app()->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
