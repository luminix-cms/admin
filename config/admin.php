<?php

/**
 * 
 * This is the configuration file for the luminix/admin package.
 * Here you can setup the admin panel.
 * 
 */
return [

    /**
     * 
     * This is the base url of the admin panel.
     * You can change this in your .env file by setting the LUMINIX_ADMIN_URL
     * environment variable.
     * 
     */
    'url' => env('LUMINIX_ADMIN_URL', '/admin'),

    /**
     * 
     * This is the middleware used by the admin web interface. To configure
     * middlewares for the underlying API, refer to the luminix/backend
     * package configuration.
     * 
     */
    'middleware' => ['web', 'auth', 'can:view-admin-panel'],

    /**
     * 
     * This is the list of locales that the admin web interface supports.
     * If you add translations to your application, adding them here will
     * allow them to be used in the admin web interface.
     * 
     */
    'locales' => ['en', 'pt-BR'],

    /**
     *
     * Identity of the product that hosts the panel.
     *
     * Without this, the panel shows the Luminix mark to the end user of every
     * application built on it — and each application has to override the logo
     * component just to stop shipping someone else's brand.
     *
     * `logo` and `logo_dark` are URLs served by the application; `name` is the
     * accessible text and falls back to `app.name`. Leaving all of them unset
     * keeps the Luminix mark, so nothing changes for who does not care.
     *
     */
    'brand' => [
        'name' => env('LUMINIX_ADMIN_BRAND_NAME'),
        'logo' => env('LUMINIX_ADMIN_BRAND_LOGO'),
        'logo_dark' => env('LUMINIX_ADMIN_BRAND_LOGO_DARK'),
    ],

];
