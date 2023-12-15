<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    public function index()
    {
        $userLoggedIn = !empty(Auth::guard('web')->user()) ? true : false;
        $adminUserLoggedIn = !empty(Auth::guard('admin')->user()) ? true : false;
        return Inertia::render('Welcome', [
            'userLoggedIn' => $userLoggedIn,
            'adminUserLoggedIn' => $adminUserLoggedIn,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'canAdminLogin' => Route::has('adminlogin'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
