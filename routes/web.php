<?php

use App\Http\Controllers\Admin\ApiProjectController;
use App\Http\Controllers\Admin\ApiRouteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManifestImportController;
use App\Http\Controllers\Admin\PersonalProjectController;
use App\Http\Controllers\Admin\PostmanController;
use App\Http\Controllers\Api\ManifestController;
use App\Http\Controllers\Api\PersonalProjectApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/api/manifest', ManifestController::class)->name('api.manifest');
Route::get('/api/personal-projects', [PersonalProjectApiController::class, 'index'])->name('api.personal-projects.index');
Route::get('/api/personal-projects/{project:slug}', [PersonalProjectApiController::class, 'show'])->name('api.personal-projects.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('apis', ApiProjectController::class);
    Route::post('apis/{api}/import', ManifestImportController::class)->name('apis.import');
    Route::resource('personal-projects', PersonalProjectController::class);

    Route::get('apis/{api}/routes/create', [ApiRouteController::class, 'create'])->name('apis.routes.create');
    Route::post('apis/{api}/routes', [ApiRouteController::class, 'store'])->name('apis.routes.store');
    Route::get('apis/{api}/routes/{route}/edit', [ApiRouteController::class, 'edit'])->name('apis.routes.edit');
    Route::put('apis/{api}/routes/{route}', [ApiRouteController::class, 'update'])->name('apis.routes.update');
    Route::delete('apis/{api}/routes/{route}', [ApiRouteController::class, 'destroy'])->name('apis.routes.destroy');

    Route::get('postman', [PostmanController::class, 'index'])->name('postman.index');
    Route::post('postman/send', [PostmanController::class, 'send'])->name('postman.send');
});
