<?php

use App\Livewire\Dashboard;
use App\Livewire\FormMonitoring;
use App\Livewire\MonitoringIndex;
use App\Livewire\UserList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('user-list', UserList::class)->name('user-list');
    Route::get('monitoring-index', MonitoringIndex::class)->name('data');
    Route::get('monitoring/create', FormMonitoring::class)->name('form-monitoring');
});

require __DIR__ . '/auth.php';
