@extends('layouts.app')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (!auth()->user()->two_factor_enabled)
                <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 text-sm sm:text-base sm:px-6 sm:py-4 mb-4" role="alert">
                    <div class="flex items-center justify-between">
            <span>
                🔒 <strong>Twoje konto nie ma włączonej weryfikacji dwuetapowej.</strong>
                <span class="ml-1">Dla zwiększenia bezpieczeństwa, zalecamy włączenie 2FA.</span>
            </span>
                        <a href="{{ route('2fa.setup') }}"
                           class="ml-4 inline-block px-4 py-1 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded transition">
                            Włącz teraz
                        </a>
                    </div>
                </div>
            @endif

            {{-- Update profile info --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update password --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete user --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            {{-- Two-Factor Authentication --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.two-factor-authentication')
                </div>
            </div>

        </div>
    </div>
@endsection
