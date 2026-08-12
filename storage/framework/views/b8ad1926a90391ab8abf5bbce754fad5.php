<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
            <!-- Flatpickr CSS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
     
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <style>[x-cloak] { display: none !important; }</style>
         
       
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="font-sans antialiased">
            <!-- Flatpickr JS -->
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <div class="min-h-screen bg-gray-100">
           <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Page Heading -->
            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main>
                <?php echo e($slot); ?>

            </main>
        </div>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
        document.addEventListener('livewire:init', function () {
            window.addEventListener('confirm-cancel', event => {
                if (confirm('Tem certeza que deseja cancelar este agendamento?')) {
                    window.Livewire.dispatch('atualizarStatus', { id: event.detail.id, status: 'Cancelado' });
                }
            });
        });
        </script>
    </body>
</html>
<?php /**PATH /var/www/pagby/resources/views/layouts/app.blade.php ENDPATH**/ ?>