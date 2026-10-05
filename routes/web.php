<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\AiAssistantController;

Route::middleware('auth')->group(function () {
    Route::get('/home', [ProductController::class, 'index'])->name('home');
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::resource('products', ProductController::class)->except(['index', 'show', 'create', 'edit']);

    Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');
    Route::post('/sales/process', [BillingController::class, 'process'])->name('sales.process');
    Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');
    Route::get('/api/products-grid', [ProductController::class, 'productsGrid'])->name('api.products-grid');
    Route::get('/api/customers/{document}', function ($document) {
        $customer = \App\Models\Customer::where('document_number', $document)->first();
        if ($customer) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }
        return response()->json(['success' => false]);
    });
});
