<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillingController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/home');
    }

    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas son incorrectas.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [ProductController::class, 'index'])->name('home');
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::resource('products', ProductController::class)->except(['index', 'show', 'create', 'edit']);

    Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');
    Route::post('/sales/process', [BillingController::class, 'process'])->name('sales.process');
});
