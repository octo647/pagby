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
        <h2>TESTE 2 - Perfil</h2>
     <?php $__env->endSlot(); ?>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: blue; margin-bottom: 20px;">CARD 1 - Dados Pessoais</h1>
        <p>Conteúdo do card 1</p>
    </div>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: red; margin-bottom: 20px;">CARD 2 - Alterar Senha</h1>
        
        <form method="post" action="<?php echo e(route('password.update')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('put'); ?>

            <div style="margin-bottom: 15px;">
                <label>Senha Atual</label><br>
                <input name="current_password" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <div style="margin-bottom: 15px;">
                <label>Nova Senha</label><br>
                <input name="password" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <div style="margin-bottom: 15px;">
                <label>Confirmar Senha</label><br>
                <input name="password_confirmation" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <button type="submit" style="background: #333; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                ATUALIZAR SENHA
            </button>
        </form>
    </div>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: green; margin-bottom: 20px;">CARD 3 - Deletar Conta</h1>
        <p>Conteúdo do card 3</p>
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
<?php /**PATH /var/www/pagby/resources/views/profile/edit-test2.blade.php ENDPATH**/ ?>