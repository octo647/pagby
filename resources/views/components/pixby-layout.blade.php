<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pixby') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
     <header class="w-full py-6 bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700 shadow">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">     
                <img src="{{ asset('images/logo.png') }}" alt="Logo Pixby" class="w-12 h-12 rounded-full object-cover border-2 border-pink-400 shadow">          
                <span class="text-3xl font-extrabold text-white tracking-wide">Pixby</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-white text-pink-700 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-100 transition">Entrar</a>
                
            </div>
        </div>
    </header>
    <body class="font-sans text-gray-900 antialiased">
    
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        
            

            <div class="w-full bg-white shadow-md overflow-hidden ">
                {{ $slot }}
            </div>
        </div>
    <footer class="w-full bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700 text-center text-white text-sm shadow ">
        &copy; {{ date('Y') }} <span class="font-bold">Pixby</span> . Todos os direitos reservados. <span class="ml-2">pixby.tech</span>
    </footer>
    </body>
</html>
