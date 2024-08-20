<?php

use Illuminate\Support\Facades\Route;
use Luminix\Admin\Http\Controllers\CmsController;

Route::group([
    'middleware' => config('luminix.admin.middleware', []),
    'prefix' => config('luminix.admin.url', 'admin'),
], function () {
    Route::get('/', [CmsController::class, 'render']);
});
