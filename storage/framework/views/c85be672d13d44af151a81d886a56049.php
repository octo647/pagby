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
            <?php echo e(__('Pagamento')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Pagamento do Agendamento</h2>
   
    <p class="mb-2">Serviços: <strong>
    <?php if(is_array($services)): ?>
        <?php echo e(implode(', ', $services)); ?>

    <?php else: ?>
        <?php echo e($services); ?>

    <?php endif; ?>
    </strong></p>
    <p class="mb-2">Total a pagar: <strong>R$ <?php echo e(number_format($total, 2, ',', '.')); ?></strong></p>

    <form method="POST" action="<?php echo e(route('payment.process')); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="mb-4">
            <label class="block font-semibold mb-1">Forma de pagamento:</label>
            <select name="payment_method" class="border rounded p-2 w-full" required>
                <option value="pix">Pix</option>
                <option value="cartao">Cartão de Crédito</option>
                <option value="presencial">Pagar no salão</option>
            </select>
        </div>

        

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Confirmar Pagamento
        </button>
    </form>
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
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/payment.blade.php ENDPATH**/ ?>