<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    protected $apiRoutes = 'routes/api.php';
    protected $webRoutes = 'routes/web.php';

    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->mapRoutes();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function mapRoutes(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path($this->apiRoutes));
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path($this->webRoutes));
    }

    public function validateWebRequest(Request $request, array $rules)
    {
        return Validator::make($request->all(), $rules);
    }
}
