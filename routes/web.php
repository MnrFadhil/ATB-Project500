<?php

use App\Livewire\Dashboard;
use App\Livewire\Data;
use App\Livewire\UserList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('user-list', UserList::class)->name('user-list');
    Route::get('data', Data::class)->name('data');
});

require __DIR__ . '/auth.php';
