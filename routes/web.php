<?php

use Illuminate\Support\Facades\Route;
use Luminix\Admin\Http\Controllers\CmsController;

Route::group([
    'middleware' => config('luminix.admin.middleware', ['web', 'auth', 'can:view-admin-panel']),
    'prefix' => config('luminix.admin.url', '/admin'),
], function () {
    Route::get('/{splat?}', [CmsController::class, 'render'])->where('splat', '.+');
});
