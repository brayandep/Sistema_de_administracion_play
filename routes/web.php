<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\SnackSaleController;
use App\Http\Controllers\SnackSalesReportController;
use App\Http\Controllers\ReservationEditController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        LoginController::class,
        'showLoginForm',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login',
    ])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/usuarios/ingresos', [
    UserActivityController::class,
    'index',
])->name('users.activity');

    Route::get('/productos', [
        ProductController::class,
        'index',
    ])->name('products.index');

    Route::get('/productos/registrar', [
        ProductController::class,
        'create',
    ])->name('products.create');

    Route::post('/productos', [
        ProductController::class,
        'store',
    ])->name('products.store');

    Route::post('/logout', [
        LoginController::class,
        'logout',
    ])->name('logout');

    Route::get('/reportes/ventas', [
    SalesReportController::class,
    'index',
])->name('reports.sales.index');

Route::get('/reportes/ventas/pdf', [
    SalesReportController::class,
    'pdf',
])->name('reports.sales.pdf');
});

Route::get('/reservas/{reservation}/editar', [
    ReservationEditController::class,
    'edit',
])
    ->middleware('role:super_admin')
    ->name('reservations.edit');

Route::put('/reservas/{reservation}', [
    ReservationEditController::class,
    'update',
])
    ->middleware('role:super_admin')
    ->name('reservations.update');
Route::get('/ventas/snacks', [
    SnackSaleController::class,
    'index',
])->name('snack-sales.index');

Route::post('/ventas/snacks', [
    SnackSaleController::class,
    'store',
])->name('snack-sales.store');

Route::get('/reportes/snacks', [
    SnackSalesReportController::class,
    'index',
])->name('reports.snacks.index');

Route::get('/reportes/snacks/pdf', [
    SnackSalesReportController::class,
    'pdf',
])->name('reports.snacks.pdf');


Route::get('/reservas', [
    ReservationController::class,
    'index',
])->name('reservations.index');

Route::post('/reservas', [
    ReservationController::class,
    'store',
])->name('reservations.store');

Route::post('/reservas/{reservation}/finalizar', [
    ReservationController::class,
    'finish',
])->name('reservations.finish');

Route::get('/clientes/buscar', [
    CustomerController::class,
    'search',
])->name('customers.search');

Route::post('/clientes/rapido', [
    CustomerController::class,
    'quickStore',
])->name('customers.quickStore');
Route::get('/productos/{product}/editar', [
    ProductController::class,
    'edit',
])
    ->middleware('role:super_admin')
    ->name('products.edit');

Route::put('/productos/{product}', [
    ProductController::class,
    'update',
])
    ->middleware('role:super_admin')
    ->name('products.update');

Route::get('/productos/{product}/stock', [
    ProductController::class,
    'addStockForm',
])
    ->middleware('role:super_admin,administrador,cajero')
    ->name('products.addStockForm');

Route::patch('/productos/{product}/stock', [
    ProductController::class,
    'addStock',
])
    ->middleware('role:super_admin,administrador,cajero')
    ->name('products.addStock');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/productos', [
        ProductController::class,
        'index',
    ])->name('products.index');

    Route::get('/productos/registrar', [
        ProductController::class,
        'create',
    ])->name('products.create');

    Route::post('/productos', [
        ProductController::class,
        'store',
    ])->name('products.store');

    Route::get('/reservas', [
        ReservationController::class,
        'index',
    ])->name('reservations.index');

    Route::post('/reservas', [
        ReservationController::class,
        'store',
    ])->name('reservations.store');

    Route::post('/reservas/{reservation}/finalizar', [
        ReservationController::class,
        'finish',
    ])->name('reservations.finish');

    Route::get('/clientes/buscar', [
        CustomerController::class,
        'search',
    ])->name('customers.search');

    Route::post('/clientes/rapido', [
        CustomerController::class,
        'quickStore',
    ])->name('customers.quickStore');

    Route::post('/logout', [
        LoginController::class,
        'logout',
    ])->name('logout');
});


