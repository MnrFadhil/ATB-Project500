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

        $this->redirectIntended(
            default: route('dashboard', absolute: false),
            navigate: true
        );
    }

}; ?>

<div>

    {{-- Session flash status (e.g. "password reset successful") --}}
    @if (session('status'))
        <div class="atb-alert atb-alert--success" role="status">
            <span class="atb-alert-dot"></span>
            {{ session('status') }}
        </div>
    @endif

    <form class="atb-login-form" wire:submit="login" novalidate>

        {{-- ── Header ─────────────────────────────────────────────── --}}
        <div class="atb-form-head">
            <span class="atb-eyebrow">Selamat datang kembali</span>
            <h1>Masuk ke akun Anda</h1>
            <p class="atb-lede">Sistem Monitoring Adaro Tirta Brayan</p>
        </div>

        {{-- ── Fields ─────────────────────────────────────────────── --}}
        <div class="atb-fields">

            {{-- Username --}}
            <label
                class="atb-field"
                for="username"
                x-data="{ focused: false, val: '{{ old('username', '') }}' }"
                :class="{ 'is-floated': focused || val }">

                <span class="atb-field-label">Username</span>

                <div class="atb-field-row">
                    <span class="atb-field-icon" aria-hidden="true">
                        {{-- user icon --}}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="3.6"/>
                            <path d="M4.5 20c1.4-3.6 4.4-5.4 7.5-5.4s6.1 1.8 7.5 5.4"/>
                        </svg>
                    </span>
                    <input
                        id="username"
                        type="text"
                        wire:model="form.username"
                        x-model="val"
                        autocomplete="username"
                        spellcheck="false"
                        @focus="focused = true"
                        @blur="focused = false">
                </div>

                <span class="atb-field-line"></span>

                @error('form.username')
                    <span class="atb-field-error">{{ $message }}</span>
                @enderror

            </label>

            {{-- Password --}}
            <label
                class="atb-field"
                for="password"
                x-data="{ focused: false, val: '', reveal: false }"
                :class="{ 'is-floated': focused || val }">

                <span class="atb-field-label">Password</span>

                <div class="atb-field-row">
                    <span class="atb-field-icon" aria-hidden="true">
                        {{-- lock icon --}}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/>
                            <path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
                        </svg>
                    </span>
                    <input
                        id="password"
                        :type="reveal ? 'text' : 'password'"
                        wire:model="form.password"
                        x-model="val"
                        autocomplete="current-password"
                        spellcheck="false"
                        @focus="focused = true"
                        @blur="focused = false">
                    <button
                        type="button"
                        class="atb-reveal"
                        @click="reveal = !reveal"
                        :aria-label="reveal ? 'Sembunyikan password' : 'Tampilkan password'"
                        tabindex="-1">
                        <span x-text="reveal ? 'Sembunyikan' : 'Lihat'">Lihat</span>
                    </button>
                </div>

                <span class="atb-field-line"></span>

                @error('form.password')
                    <span class="atb-field-error">{{ $message }}</span>
                @enderror

            </label>

        </div>

        {{-- ── Remember me ─────────────────────────────────────────── --}}
        <div class="atb-row-between">
            <label class="atb-checkbox" for="remember">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    wire:model="form.remember">
                <span class="atb-box" aria-hidden="true">
                    <svg viewBox="0 0 16 16" width="10" height="10">
                        <path d="M2.5 8.5l3.2 3.2L13.5 4" fill="none" stroke="currentColor"
                              stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="atb-lbl">Ingat saya di perangkat ini</span>
            </label>
        </div>

        {{-- ── Validation errors ───────────────────────────────────── --}}
        @if ($errors->any())
            <div class="atb-alert" role="alert">
                <span class="atb-alert-dot"></span>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- ── Submit ──────────────────────────────────────────────── --}}
        <button
            type="submit"
            class="atb-btn-primary"
            wire:loading.class="is-loading"
            wire:target="login"
            wire:loading.attr="disabled">

            <span class="atb-btn-label">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memverifikasi&hellip;</span>
            </span>

            <span class="atb-btn-arrow" aria-hidden="true">
                {{-- Arrow right --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M13 5l7 7-7 7"/>
                </svg>
            </span>

        </button>

    </form>

</div>
