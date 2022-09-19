<?php

declare(strict_types=1);

namespace BprooDev\Subscriptions\Providers;

use BprooDev\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use BprooDev\Subscriptions\Models\PlanFeature;
use BprooDev\Subscriptions\Models\PlanSubscription;
use BprooDev\Subscriptions\Models\PlanSubscriptionUsage;
use BprooDev\Subscriptions\Console\Commands\MigrateCommand;
use BprooDev\Subscriptions\Console\Commands\PublishCommand;
use BprooDev\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    // use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class,
        PublishCommand::class,
        RollbackCommand::class
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'bproodev.subscriptions');

        // Register console commands
        $this->commands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerModelBindings();

        // Publish Resources
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('subscriptions.php'),
        ], 'subscriptions-config');
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'subscriptions-migrations');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['subscriptions.models'];

        if (! $config) {
            return;
        }

        $this->app->bind($config['plan']);
        $this->app->bind($config['plan_feature']);
        $this->app->bind($config['plan_subscription']);
        $this->app->bind($config['plan_subscription_usage']);
    }
}
