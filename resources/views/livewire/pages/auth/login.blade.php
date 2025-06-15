<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
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
    <form wire:submit="login">
        <div class="pb-5 px-5 pt-3">
            <div class="mb-3 text-center">
                <img style="width: 150px; object-fit: cover" src="{{ asset('assets/adaro-login-logo.svg') }}">
            </div>

            <div class="form-group">
                <input wire:model="form.username" name="username" required id="username" type="text"
                    class="form-control form-control-user" placeholder="Enter Username">
            </div>
            <div class="form-group">
                <input type="password" class="form-control form-control-user" placeholder="Password" name="password"
                    wire:model="form.password">
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" wire:model="form.remember" name="remember"
                        id="remember">
                    <label class="custom-control-label" style=" line-height: 1.5rem" for="remember">Remember
                        Me</label>
                </div>
            </div>
            <button type="submit" class="btn btn-user btn-block"
                style="background-color: #00664A; color:white; font-weight: bold">
                Login
            </button>
            @error('form.username')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
    </form>
</div>
