<?php

namespace NsTechNs\JazzCMS;

use Illuminate\Support\ServiceProvider;

class JazzCMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/jazz-cms.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/jazz-cms.php';
        $this->mergeConfigFrom($configPath, 'jazz-cms');

        $this->app->bind(JazzCMS::class, function ($app) {
            return new JazzCMS;
        });

        $this->app->alias(JazzCMS::class, 'JazzCMS');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('jazz-cms.php');
    }

    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this->publishes([$configPath => config_path('jazz-cms.php')], 'config');
    }
}
