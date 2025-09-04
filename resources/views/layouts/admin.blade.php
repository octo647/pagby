<!-- filepath: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Pixby</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">
    <nav class="bg-indigo-900 text-white p-4 mb-6">
        <span class="font-bold">Pixby Admin</span>
        <!-- Adicione links de navegação aqui -->
    </nav>
    <div class="container mx-auto px-4">
        @yield('content')
    </div>
</body>
</html>