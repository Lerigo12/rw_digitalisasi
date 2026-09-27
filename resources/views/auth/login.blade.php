<x-guest-layout>
    <!-- Glassmorphic Card Container with entrance animation -->
    <div class="relative bg-slate-900/80 backdrop-blur-xl border border-slate-800 shadow-2xl rounded-[20px] p-8 sm:p-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        
        <!-- Logo and Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 mb-4 ring-1 ring-white/20">
                <a href="/">
                    <x-application-logo class="w-8 h-8 fill-current text-white" />
                </a>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">RW DIGITALISASI</h2>
            <p class="text-sm text-slate-400 mt-1">Please enter your details to sign in</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6 p-3 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-sm" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Email')" class="text-slate-300 font-medium text-xs uppercase tracking-wider" />
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 start-0 ps-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <x-text-input id="email" class="block w-full ps-10 pe-4 py-3 bg-slate-950/50 border-slate-800 text-white placeholder-slate-500 rounded-xl transition-all duration-200 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hover:border-slate-700 text-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-rose-400 text-xs" />
            </div>

            <!-- Password -->
            <div class="space-y-1.5" x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Password')" class="text-slate-300 font-medium text-xs uppercase tracking-wider" />
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 start-0 ps-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <x-text-input id="password" class="block w-full ps-10 pe-10 py-3 bg-slate-950/50 border-slate-800 text-white placeholder-slate-500 rounded-xl transition-all duration-200 focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hover:border-slate-700 text-sm"
                                    ::type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required autocomplete="current-password" 
                                    placeholder="••••••••" />
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 end-0 pe-3.5 flex items-center text-slate-400 hover:text-slate-200 transition-colors focus:outline-none">
                        <svg x-show="!showPassword" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="showPassword" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-rose-400 text-xs" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm py-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none group">
                    <input id="remember_me" type="checkbox" class="rounded bg-slate-950 border-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-slate-900 transition-transform active:scale-95" name="remember">
                    <span class="ms-2 text-slate-400 group-hover:text-slate-200 transition-colors text-xs">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors focus:outline-none focus:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button with Loading State -->
            <button type="submit" class="w-full relative inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg shadow-indigo-600/20 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 hover:scale-[1.01] active:scale-[0.98] disabled:opacity-75 disabled:cursor-not-allowed" :disabled="loading">
                <span class="absolute inset-0 flex items-center justify-center" x-show="loading" style="display: none;">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span :class="{ 'invisible': loading }">{{ __('LOG IN') }}</span>
            </button>
        </form>
    </div>
</x-guest-layout>
