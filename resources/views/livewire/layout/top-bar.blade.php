<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>


<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-lg">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <span class="mr-2 inline text-gray-600 small">{{ auth()->user()->name }}</span>
                {{-- <img class="img-profile rounded-circle"
                    src="img/undraw_profile.svg"> --}}
            </a>
            <!-- Dropdown - User Information -->
            <div style="background-color: #00664A;" class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <button wire:click="logout()"
                    style="color: white;  display: block;
                            width: 100%;
                            padding: 0.25rem 1.5rem;
                            clear: both;
                            font-weight: 400;
                            color: white;
                            text-align: inherit;
                            white-space: nowrap;
                            background-color: transparent;
                            border: 0;">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 "></i>
                    Logout
                </button>
            </div>
        </li>
    </ul>
</nav>
