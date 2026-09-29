<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/services', 'pages.services')->name('services');
Route::get('/services/{id}', [SiteController::class, 'service'])->name('services.show');
Route::get('/news', [SiteController::class, 'news'])->name('news');
Route::get('/news/{slug}', [SiteController::class, 'article'])->name('news.show');
Route::view('/careers', 'pages.careers')->name('careers');
Route::view('/ventures', 'pages.ventures')->name('ventures');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'submitContact'])->middleware('throttle:10,1')->name('contact.store');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/cookies', 'pages.cookies')->name('cookies');
Route::view('/sitemap', 'pages.sitemap')->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin panel (session login). Each area is limited by role, see User::AREA_ROLES.
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'auth.session'])->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\OverviewController::class)->name('overview');
        Route::get('/settings', [Admin\AuthController::class, 'settings'])->name('settings');
        Route::put('/settings/password', [Admin\AuthController::class, 'updatePassword'])->name('settings.password');
        Route::get('/export/{type}', ExportController::class)->name('export');

        Route::middleware('area:leads')->prefix('submissions')->name('leads.')->group(function () {
            Route::get('/', [Admin\LeadsController::class, 'index'])->name('index');
            Route::patch('/{contact}', [Admin\LeadsController::class, 'update'])->name('update');
            Route::post('/{contact}/activities', [Admin\LeadsController::class, 'addActivity'])->name('activities.store');
            Route::delete('/{contact}/activities/{activity}', [Admin\LeadsController::class, 'deleteActivity'])->name('activities.destroy');
            Route::post('/{contact}/convert', [Admin\LeadsController::class, 'convert'])->name('convert');
        });

        Route::middleware('area:clients')->group(function () {
            Route::resource('clients', Admin\ClientsController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        });

        Route::middleware('area:projects')->group(function () {
            Route::resource('projects', Admin\ProjectsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:quotations')->group(function () {
            Route::post('/quotations/{quotation}/invoice', [Admin\QuotationsController::class, 'toInvoice'])->name('quotations.invoice');
            Route::resource('quotations', Admin\QuotationsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:invoices')->group(function () {
            Route::post('/invoices/{invoice}/payments', [Admin\InvoicesController::class, 'addPayment'])->name('invoices.payments.store');
            Route::delete('/payments/{payment}', [Admin\InvoicesController::class, 'deletePayment'])->name('payments.destroy');
            Route::resource('invoices', Admin\InvoicesController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:finances')->prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [Admin\FinancesController::class, 'index'])->name('index');
            Route::post('/', [Admin\FinancesController::class, 'store'])->name('store');
            Route::put('/{entry}', [Admin\FinancesController::class, 'update'])->name('update');
            Route::delete('/{entry}', [Admin\FinancesController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('area:renewals')->group(function () {
            Route::post('/renewals/{renewal}/renew', [Admin\RenewalsController::class, 'renew'])->name('renewals.renew');
            Route::resource('renewals', Admin\RenewalsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:employees')->group(function () {
            Route::resource('team', Admin\EmployeesController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:timesheets')->group(function () {
            Route::resource('timesheets', Admin\TimesheetsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:tickets')->group(function () {
            Route::resource('tickets', Admin\TicketsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:content')->group(function () {
            Route::resource('news', Admin\ArticlesController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('area:users')->group(function () {
            Route::resource('users', Admin\UsersController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::get('/audit-log', [Admin\AuditLogsController::class, 'index'])->middleware('area:audit')->name('audit.index');
    });
});

// Printable invoice / quotation (no admin chrome).
Route::get('/admin-print/{kind}/{id}', Admin\PrintController::class)->middleware(['auth', 'auth.session'])->name('admin.print');
