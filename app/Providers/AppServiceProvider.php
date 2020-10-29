<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserMailChanged;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::created(function($user){
            Mail::to($user)->send(new UserCreated($user));
        });

        // User::created(function($user){
        //     retry(5, function() use ($user){
        //         Mail::to($user)->send(new UserCreated($user));
        //     }, 100);     
        // });

        User::updated(function($user){
            if($user->isDirty('email')){
                Mail::to($user)->send(new UserMailChanged($user));
            }               
        });

    }
}
