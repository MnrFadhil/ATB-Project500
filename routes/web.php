<?php

use App\Livewire\Dashboard;
use App\Livewire\FormMonitoring;
use App\Livewire\MonitoringDetail;
use App\Livewire\MonitoringIndex;
use App\Livewire\UserList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('user-list', UserList::class)->name('user-list');
    Route::get('monitoring-index', MonitoringIndex::class)->name('monitoring-index');
    Route::get('monitoring/create', FormMonitoring::class)->name('monitoring-form');
    Route::get('monitoring/{id}', MonitoringDetail::class)->name('monitoring-detail');
});

require __DIR__ . '/auth.php';
