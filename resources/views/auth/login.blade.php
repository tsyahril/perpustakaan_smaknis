<x-guest-layout>
    <!-- Header Card -->
    <div class="mb-6 -mx-6 -mt-6 p-6 bg-gradient-to-r from-blue-700 to-blue-600 rounded-t-xl shadow-sm text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/10 backdrop-blur-md rounded-xl text-white mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-white tracking-wide">Perpustakaan SMAKNIS</h1>
        <p class="text-xs text-blue-100/80 mt-1">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Status Session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form Login -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Field -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold" />
            <div class="relative mt-1.5 rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <x-text-input id="email" 
                              class="block w-full pl-10 pr-4 py-2.5 border-slate-200 focus:border-blue-600 focus:ring-blue-600/20 rounded-lg text-sm text-slate-800 transition duration-200" 
                              type="email" 
                              name="email" 
                              :value="old('email')" 
                              placeholder="...@email.com"
                              required 
                              autofocus 
                              autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <!-- Password Field -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold" />
            <div class="relative mt-1.5 rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <x-text-input id="password" 
                              class="block w-full pl-10 pr-4 py-2.5 border-slate-200 focus:border-blue-600 focus:ring-blue-600/20 rounded-lg text-sm text-slate-800 transition duration-200" 
                              type="password" 
                              name="password" 
                              placeholder="••••••••"
                              required 
                              autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-2 focus:ring-blue-500/20" 
                       name="remember">
                <span class="ms-2 text-xs font-medium text-slate-600 hover:text-slate-800 transition duration-150">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition duration-150 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button class="w-full py-2.5 justify-center bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-2 focus:ring-blue-500/30 font-semibold text-sm rounded-lg shadow-md transition duration-200">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>