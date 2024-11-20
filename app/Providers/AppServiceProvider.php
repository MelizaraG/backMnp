<?php

namespace App\Providers;

use App\Models\Sortie;
use App\Policies\SortiePolicy;
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
    }

    protected $policies = [
        Sortie::class => SortiePolicy::class,
    ];
    
}
