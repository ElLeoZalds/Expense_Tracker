<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Observers\AuditObserver;
use App\Policies\BudgetPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiters();
        $this->registerObservers();
    }

    /**
     * Registra los policies de la aplicación.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Budget::class, BudgetPolicy::class);
    }

    /**
     * Registra los observers para auditoría.
     */
    protected function registerObservers(): void
    {
        Expense::observe(AuditObserver::class);
        Category::observe(AuditObserver::class);
    }

    /**
     * Configura los limitadores de tasa para rutas sensibles.
     */
    protected function configureRateLimiters(): void
    {
        // Rate limiter por defecto para usuarios autenticados
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // Rate limiter más estricto para operaciones sensibles (creación/eliminación)
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(20)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function ($request, $headers) {
                return response()->json([
                    'error' => 'Demasiadas solicitudes',
                    'message' => 'Has excedido el límite de solicitudes. Por favor intenta más tarde.',
                    'retry_after' => 60,
                ], 429, $headers);
            });
        });

        // Rate limiter específico para eliminación de recursos
        RateLimiter::for('delete-operations', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function ($request, $headers) {
                return response()->json([
                    'error' => 'Demasiadas solicitudes de eliminación',
                    'message' => 'Has excedido el límite de eliminaciones. Por favor intenta más tarde.',
                    'retry_after' => 60,
                ], 429, $headers);
            });
        });

        // Rate limiter para autenticación (prevención de fuerza bruta)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}