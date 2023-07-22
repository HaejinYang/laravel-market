<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);

        User::created(function ($user) {
            Mail::to($user->email)->send(new UserCreated($user));
        });

        User::updated(function ($user) {
            if ($user->isDirty('email')) {
                Mail::to($user->email)->send(new UserMailChanged($user));
            }
        });

        // Product가 상속하는 모델 클래스의 HasEvents에 정의되어있음.
        Product::updated(function (Product $product) {
            if ($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });
    }
}
