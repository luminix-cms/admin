<?php

namespace Luminix\Admin;

use Illuminate\Support\ServiceProvider;
use Luminix\Admin\Console\Commands\UiCommand;
use Luminix\Backend\Services\ModelFilter;
use Luminix\Frontend\Services\BootService;

class AdminServiceProvider extends ServiceProvider
{

    const CMS_VERSION = '1.0.0';

    const PEER_DEPENDENCIES = [
        '@emotion/react'      => '^11.13.0',
        '@emotion/styled'     => '^11.13.0',
        '@fontsource/roboto'  => '^5.0.12',
        '@luminix/core'       => '^1.0.0',
        '@luminix/react'      => '^1.0.0',
        '@luminix/support'    => '^1.0.1',
        '@mui/icons-material' => '^5.16.5',
        '@mui/material'       => '^5.16.5',
        'i18next'             => '^23.12.2',
        'react'               => '^18.3.1',
        'react-dom'           => '^18.3.1',
        'react-i18next'       => '^15.0.1',
        'react-router-dom'    => '6.25.1',
    ];


    public function register()
    {
        $this->commands([
            UiCommand::class,
        ]);

        $this->wireConfiguration();

        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'luminix.admin');

        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('luminix/admin.php'),
        ], 'luminix-config');

        $this->publishes([
            __DIR__ . '/../skeleton/views' => resource_path('views/vendor/admin'),
            __DIR__ . '/../skeleton/js' => resource_path('js'),
        ], 'luminix-ui');
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
                        'brand' => [
                            'name' => config('luminix.admin.brand.name') ?: config('app.name'),
                            'logo' => config('luminix.admin.brand.logo'),
                            'logo_dark' => config('luminix.admin.brand.logo_dark'),
                        ],
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
