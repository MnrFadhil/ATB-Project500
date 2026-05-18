<?php

use App\Livewire\Dashboard;
use App\Livewire\FormMonitoring;
use App\Livewire\MonitoringDetail;
use App\Livewire\MonitoringIndex;
use App\Livewire\UserList;
use App\Livewire\HideFormMonitoring;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('user-list', UserList::class)->name('user-list');
    Route::get('monitoring-index', MonitoringIndex::class)->name('monitoring-index');
    Route::get('monitoring/create', FormMonitoring::class)->name('monitoring-form')->middleware(['admin']);
    Route::get('monitoring/{id}', MonitoringDetail::class)->name('monitoring-detail');
    Route::get('monitoring/{id}/edit', FormMonitoring::class)->name('monitoring-form')->middleware(['admin']);
    Route::get('wma-evaluation', \App\Livewire\WmaEvaluation::class)->name('wma-evaluation');
    Route::get('wma-dosis', \App\Livewire\WmaDosisPrediksi::class)->name('wma-dosis');
    Route::get('/monitoring-create', HideFormMonitoring::class)->middleware(['admin']);
    // Route::get('/monitoring/{id}/edit', HideFormMonitoring::class);
});

require __DIR__ . '/auth.php';
