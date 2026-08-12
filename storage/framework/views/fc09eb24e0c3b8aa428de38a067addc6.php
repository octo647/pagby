<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e($title ?? 'Agendamento Online'); ?> - <?php echo e(config('app.name')); ?></title>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Agendamento Online - <?php echo e(tenant()->id ?? 'Salão'); ?>">
    <meta property="og:description" content="Agende seu horário de forma rápida e prática!">
    <meta property="og:image" content="<?php echo e(tenant_asset('logo.png')); ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Agendamento Online - <?php echo e(tenant()->id ?? 'Salão'); ?>">
    <meta property="twitter:description" content="Agende seu horário de forma rápida e prática!">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <?php if(tenant() && tenant()->logo): ?>
                            <img src="<?php echo e(tenant_asset(tenant()->logo)); ?>" alt="Logo" class="h-12 w-auto mr-4">
                        <?php endif; ?>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <?php echo e(tenant()->id ?? 'Salão'); ?>

                        </h1>
                    </div>
                    <a href="/" class="text-sm text-gray-600 hover:text-gray-900">
                        Já sou cliente
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php echo e($slot); ?>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-600 text-sm">
                <p>Powered by <a href="https://pagby.com.br" class="text-blue-600 hover:text-blue-800 font-semibold">Pagby</a></p>
            </div>
        </footer>
    </div>
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /var/www/pagby/resources/views/layouts/public.blade.php ENDPATH**/ ?>