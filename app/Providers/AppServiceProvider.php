<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Category;
use App\Models\Expense;
use App\Policies\CategoryPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Support\Facades\Gate;
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
    #[Override]
    public function boot(): void
    {
        $this->registerPolicies();
    }

    /**
     * Registra los policies de la aplicación.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
    }
}