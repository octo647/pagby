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

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-8 rounded-lg">
            <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            
            <h1 class="text-3xl font-bold mb-4">Plano Ativado com Sucesso!</h1>
            
            <div class="bg-white rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-2">Detalhes da Assinatura</h2>
                <p><strong>Plano:</strong> <?php echo e($tenant->current_plan); ?></p>
                <p><strong>Status:</strong> Ativo</p>
                <p><strong>Válido até:</strong> <?php echo e($tenant->subscription_ends_at->format('d/m/Y H:i')); ?></p>
            </div>
            
            <p class="text-lg mb-6">
                Seu plano <?php echo e($tenant->current_plan); ?> está ativo e você pode aproveitar todas as funcionalidades da plataforma.
            </p>
            
            <div class="space-y-3">
                <a href="<?php echo e(route('tenant.dashboard')); ?>" 
                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    Ir para o Dashboard
                </a>
                
                <br>
                
                <a href="<?php echo e(route('tenant.subscription.plans')); ?>" 
                   class="inline-block text-green-600 hover:text-green-700 font-semibold">
                    Gerenciar Assinatura
                </a>
            </div>
        </div>
    </div>
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
<?php /**PATH /var/www/pagby/resources/views/tenant/subscription/success.blade.php ENDPATH**/ ?>