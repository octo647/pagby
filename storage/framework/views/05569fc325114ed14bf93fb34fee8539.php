<?php $__env->startSection('content'); ?>
<div class="container mx-auto max-w-2xl py-10">
    <h1 class="text-3xl font-bold mb-6 text-center">Aceite do Contrato de Prestação de Serviços</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <?php echo $__env->make('tenant.subscription.contract-template', ['tenant' => tenant()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <form method="POST" action="<?php echo e(route('tenant.contract.accept')); ?>">
        <?php echo csrf_field(); ?>
        <div class="flex items-center justify-center mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg text-lg shadow">Aceitar e Prosseguir</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/pagby/resources/views/tenant/subscription/accept-contract.blade.php ENDPATH**/ ?>