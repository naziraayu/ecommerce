<?php

use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\AuthenticationController;

// =============================
// 🔓 Public Routes
// =============================

Route::get('/', function () {
    return view('welcome');
});

// =============================
// 🔐 Authenticated Routes
// =============================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categories
    Route::get('/download/category-template', function () {
        $file = public_path('templates/category_template.xlsx');
        if (file_exists($file)) {
            return response()->download($file);
        }
        abort(404);
    })->name('categories.downloadTemplate');

    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::resource('categories', CategoryController::class);

    // Product Images
    Route::delete('/product-images/{id}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/export/user', [UserController::class, 'export'])->name('export.user');

    // Admins
    Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('admins/data', [AdminController::class, 'show'])->name('admins.data');
    Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('admins/{id}', [AdminController::class, 'edit'])->name('admins.edit');
    Route::delete('admins/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
    Route::get('/search-role', [AdminController::class, 'search'])->name('search.role');
    Route::put('admins/{id}', [AdminController::class, 'update'])->name('admins.update');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/export/order', [OrderController::class, 'export'])->name('export.order');

    // Settings
    Route::get('settings', [AuthenticationController::class, 'settingView'])->name('settings.index');
    Route::post('settings/email', [AuthenticationController::class, 'emailChange'])->name('settings.store');

    // Payment
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{order}/failed', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');


    // =============================
    // 🛡️ SUPERADMIN ONLY
    // =============================
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // =============================
    // 🎯 Role-Based Permissions
    // =============================
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('products', ProductController::class);
    });

});

// =============================
// 🌐 Language Switcher
// =============================

Route::get('/set-language/{lang}', function ($lang) {
    Session::put('locale', $lang);
    App::setLocale($lang);
    return redirect()->back();
})->name('set.language');

// =============================
// 🔐 Auth Routes (Login, Register)
// =============================

require __DIR__.'/auth.php';
