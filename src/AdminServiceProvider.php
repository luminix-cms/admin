<?php

namespace Luminix\Admin;

use Illuminate\Support\ServiceProvider;
use Luminix\Backend\Services\ModelFilter;
use Luminix\Frontend\Services\BootService;

class AdminServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->wireConfiguration();

        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'luminix.admin');

        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('luminix/admin.php'),
        ], 'luminix-config');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');

        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');


    }



    public function wireConfiguration()
    {
        BootService::reducer('wireConfig', function ($config) {
            return [
                ...$config,
                'trans' => __('*', [], config('app.locale', 'en')),
                'luminix' => [
                    ...$config['luminix'] ?? [],
                    'admin' => [
                        'url' => config('luminix.admin.url', 'admin'),
                        'locales' => config('luminix.admin.locales', ['en', 'pt-BR']),
                        'filter' => [
                            'operators' => ModelFilter::operators(),
                            'exclude' => config('luminix.backend.api.filter.exclude', [])
                        ],
                    ]
                ]
            ];
        });
    }
    
    
}
