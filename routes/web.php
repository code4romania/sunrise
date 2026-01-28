<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Fix for the `Route [login] not defined.` error that keeps appearing in development.
Route::get('/laravel/login', fn () => redirect(route('filament.organization.auth.login')))->name('login');

Route::get('/up', fn () => 'OK');
