<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <?php echo e(__('Pix')); ?>

        </h2>


     <?php $__env->endSlot(); ?>
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Pagamento via Pix</h2>
    <p class="mb-2">Total: <strong>R$ <?php echo e(number_format($total, 2, ',', '.')); ?></strong></p>
    <p class="mb-2">Escaneie o QR Code abaixo no seu app bancário:</p>
    <div class="my-4">
        
        <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo e(urlencode($qrCode)); ?>&amp;size=200x200" alt="QR Code Pix">
    </div>
    <p class="break-all text-xs bg-gray-100 p-2 rounded"><?php echo e($qrCode); ?></p>
    <a href="<?php echo e(route('dashboard')); ?>" class="mt-4 inline-block text-blue-600">Voltar ao painel</a>
</div>


    

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/pagby/resources/views/payment_pix.blade.php ENDPATH**/ ?>