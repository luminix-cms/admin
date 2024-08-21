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
    'middleware' => ['web', 'can:view-admin-panel'],

    /**
     * 
     * This is the list of locales that the admin web interface supports.
     * If you add translations to your application, adding them here will
     * allow them to be used in the admin web interface.
     * 
     */
    'locales' => ['en', 'pt-BR'],

];
