<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureDatabaseFromEnvironment();
    }

    private function configureDatabaseFromEnvironment(): void
    {
        $url = env('DATABASE_URL') ?: env('DB_URL');

        if ($url) {
            config([
                'database.default' => env('DB_CONNECTION', 'pgsql'),
                'database.connections.pgsql.url' => $url,
            ]);

            return;
        }

        $host = env('PGHOST');

        if ($host && ! in_array($host, ['127.0.0.1', 'localhost'], true)) {
            config([
                'database.default' => env('DB_CONNECTION', 'pgsql'),
                'database.connections.pgsql.url' => null,
                'database.connections.pgsql.host' => $host,
                'database.connections.pgsql.port' => env('PGPORT') ?: env('DB_PORT', '5432'),
                'database.connections.pgsql.database' => env('PGDATABASE') ?: env('DB_DATABASE', 'laravel'),
                'database.connections.pgsql.username' => env('PGUSER') ?: env('DB_USERNAME', 'root'),
                'database.connections.pgsql.password' => env('PGPASSWORD') ?: env('DB_PASSWORD', ''),
                'database.connections.pgsql.sslmode' => env('DB_SSLMODE', 'require'),
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment('production')) {
            return;
        }

        $appUrl = config('app.url');

        if (blank($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            return;
        }

        URL::forceRootUrl(rtrim($appUrl, '/'));
        URL::forceScheme('https');
    }
}
