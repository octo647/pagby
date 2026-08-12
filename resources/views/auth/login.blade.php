<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
   

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot Password aligned -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Lembre-se de mim') }}</span>
            </label>
            @if (tenant())
                @php
                    // Detecta se está em subdomínio (ex: dumont.localhost) ou path (localhost/dumont)
                    $host = request()->getHost();
                    $isSubdomain = false;
                    if (tenant()->id && (
                        str_starts_with($host, tenant()->id . '.') ||
                        str_contains($host, tenant()->id . '.')
                    )) {
                        $isSubdomain = true;
                    }
                @endphp
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-4" href="{{ $isSubdomain ? '/forgot-password' : '/' . tenant()->id . '/forgot-password' }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
            @else
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-4" href="{{ route('password.request') }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
            @endif
        </div>
        <div class="mt-4 flex justify-between items-center">


            <a href="/register" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Não tem uma conta? Registre-se') }}
        </div>

        <div class="flex justify-center mt-4">
            

            <x-primary-button class="w-full py-2 px-4 text-base font-semibold rounded-lg">
               <span class="block w-full text-center "> {{ __('Entrar') }}</span>
            </x-primary-button>
            
        </div>
    </form>
     
    <!-- Login Social Centralizado -->
    @if(tenant())
    <div class="social-login mt-8">
        <div class="divider mb-4 text-center text-gray-500">Ou entre com</div>
        <a href="{{ config('app.central_url', 'https://pagby.com.br') . '/auth/google?tenant=' . tenant()->id }}"
           class="py-2 px-4 flex justify-center items-center bg-red-600 hover:bg-red-700 focus:ring-red-500 focus:ring-offset-red-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg">
            <svg width="20" height="20" fill="currentColor" class="mr-2" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M896 786h725q12 67 12 128 0 217-91 387.5t-259.5 266.5-386.5 96q-157 0-299-60.5t-245-163.5-163.5-245-60.5-299 60.5-299 163.5-245 245-163.5 299-60.5q300 0 515 201l-209 201q-123-119-306-119-129 0-238.5 65t-173.5 176.5-64 243.5 64 243.5 173.5 176.5 238.5 65q87 0 160-24t120-60 82-82 51.5-87 22.5-78h-436v-264z"></path>
            </svg>
            Entrar ou registrar com Google
        </a>
    </div>
    @endif
    
    
    

</x-guest-layout>
