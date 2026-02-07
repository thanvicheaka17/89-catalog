<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ \App\Models\SiteSetting::get('site_name', 'CLICKENGINE') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/logo/favicon.ico') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page-centered">
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="login-logo">
                <img src="{{ asset('images/logo/MKG-Logo-1000X288.png') }}" alt="Logo" style="width:200px">
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                @if ($errors->any())
                    <div class="login-error">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="login-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email"
                        value="{{ old('email') }}" required autofocus>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                {{-- <div class="login-options">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                </div> --}}

                <button type="submit" class="login-button">
                    Sign In
                </button>
            </form>
            @php
                $siteName =
                    \App\Models\SiteSetting::get('site_name') ?? (config('app.site_name') ?? config('app.name'));
            @endphp<p class="text-center text-gray-500 dark:text-gray-400 text-xs sm:text-sm-sm mt-6">
                Licensed by <strong>{{ $siteName }}</strong> © {{ date('Y') }}
                    <br>
                    </p>
        </div>
    </div>
</body>

</html>
