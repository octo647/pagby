<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pagby') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
   <body class="font-sans text-gray-900 antialiased" style="margin:0; padding:0;">
    <div class="min-h-screen" style="display:flex; flex-direction:column;">
        <!-- Header -->
        <header class="w-full py-6 bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700" style="margin:0; padding:1.5rem 0;">
            <!-- conteúdo do header igual -->
             <div class="container mx-auto flex items-center justify-between px-4">
                    <div class="flex items-center gap-2">     
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Pagby" class="h-8 w-auto">          
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="funcionalidades" class="bg-white text-pink-700 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-100 transition">Funcionalidades</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('register-tenant') }}" class="bg-white text-pink-700 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-100 transition">Registrar</a>
                    </div>
                </div>
        </header>

        <!-- Main sem espaçamento -->
        <main style="flex:1; margin:0; padding:0; background:#f3f4f6;">
            <div class="w-full bg-white" style="margin:0; padding:0;">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700 text-center text-white text-sm py-4">
            <!-- conteúdo do footer igual -->
             &copy; {{ date('Y') }} <span class="font-bold">PagBy</span> . Todos os direitos reservados. <span class="ml-2">PagBy.tech</span>
            </footer>
        
    </div>
</body>
</html>