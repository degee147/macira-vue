<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AdminProfileInformationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/login', [UserController::class, 'loginForm'])->name('login');
// Route::post('/login', [UserController::class, 'store'])->name('login');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin_login');
Route::post('/admin/login', [AdminController::class, 'store'])->name('admin_login_post');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/admin/users', [AdminController::class, 'registered_users'])->name('registered_users');
    Route::get('/admin/csv-import', [AdminController::class, 'admin_csv_import'])->name('admin_csv_import');
    Route::post('/admin/csv-upload', [AdminController::class, 'uploadCSV'])->name('admin_csv_upload');
    Route::get('/admin/users-data', [AdminController::class, 'admin_users_data'])->name('admin_users_data');
    Route::get('/admin/csv-data', [AdminController::class, 'csvData'])->name('admin_csv_data');
    Route::get('/admin/csv-sample', [AdminController::class, 'admin_csv_sample'])->name('admin_csv_sample');
    Route::get('/admin/api-data', [AdminController::class, 'admin_api_data'])->name('admin_api_data');

    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin_profile');
    Route::put('/admin/profile/update', [AdminProfileInformationController::class, 'update'])->name('admin_profile_update');


});
