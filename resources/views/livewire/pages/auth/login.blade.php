<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form wire:submit="login">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
            </div>

            <div class="form-group">
                <input  wire:model="form.email"  name="email" required  id="email" type="email" class="form-control form-control-user"
                    placeholder="Enter Email Address...">
            </div>
            <div class="form-group">
                <input type="password" class="form-control form-control-user"
                    placeholder="Password"  name="password" wire:model="form.password">
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" wire:model="form.remember" name="remember" id="remember">
                    <label class="custom-control-label" style=" line-height: 1.5rem" for="remember" >Remember
                        Me</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-user btn-block">
                Login
            </button>
        </div>
    </form>
</div>
