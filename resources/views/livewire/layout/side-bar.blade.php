<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public $isAdmin;
    public $sidebarOpen = false;

    public function mount()
    {
        $this->isAdmin = Auth::user()->role == 'admin';
    }
}; ?>

<ul style="background-color: #00664A;" class="navbar-nav sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard" wire:navigated>
        <div class="my-3 text-center">
            <img id="logo" style="object-fit: cover" src="{{ asset('assets/sidebar-open.png') }}">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Heading -->
    <div class="sidebar-heading mt-4">
        Main Menu
    </div>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="/dashboard" wire:navigated>
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Nav Item - User -->
    @if ($isAdmin)
        <li class="nav-item  {{ request()->is('user-list*') ? 'active' : '' }}">
            <a class="nav-link" href="/user-list" wire:navigated>
                <i class="fas fa-fw fa-users"></i>
                <span>User</span>
            </a>
        </li>
    @endif

    <!-- Nav Item - Data input -->
    <li class="nav-item {{ request()->is('monitoring*') ? 'active' : '' }}">
        <a class="nav-link" href="/monitoring-index" wire:navigated>
            <i class="fas fa-fw fa-table"></i>
            <span>Monitoring</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

@script
    <script>
        sidebarCloseImage = "{{ asset('assets/sidebar-close.svg') }}";
        sidebarOpenImage = "{{ asset('assets/sidebar-open.png') }}";

        let isOpen = !$(".sidebar").hasClass("toggled")

        if (isOpen) {
            $("#logo").attr("src", sidebarOpenImage);
        } else {
            $("#logo").attr("src", sidebarCloseImage);
        }

        $("#sidebarToggle, #sidebarToggleTop").on("click", function(e) {
            let isOpen = !$(".sidebar").hasClass("toggled")

            if (isOpen) {
                $("#logo").attr("src", sidebarOpenImage);
            } else {
                $("#logo").attr("src", sidebarCloseImage);
            }
        });
    </script>
@endscript
