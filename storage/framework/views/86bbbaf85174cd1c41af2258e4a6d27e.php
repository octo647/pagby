
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            <?php echo e(__('Alterar Senha')); ?>

        </h2>

        <p class="mt-1 text-sm text-gray-600">
            <?php echo e(__('Mantenha sua conta segura usando uma senha forte e única.')); ?>

        </p>
    </header>

    <form method="post" action="<?php echo e(route('password.update')); ?>" class="mt-6 space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('put'); ?>

        <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['id' => 'update_password_current_password','name' => 'current_password','label' => __('Senha Atual'),'autocomplete' => 'current-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'update_password_current_password','name' => 'current_password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Senha Atual')),'autocomplete' => 'current-password']); ?>
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->updatePassword->get('current_password'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->updatePassword->get('current_password')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['id' => 'update_password_password','name' => 'password','label' => __('Nova Senha'),'autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'update_password_password','name' => 'password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nova Senha')),'autocomplete' => 'new-password']); ?>
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->updatePassword->get('password'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->updatePassword->get('password')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
            <div class="mt-1 text-xs text-gray-500 space-y-1">
                <p>Sua senha deve conter:</p>
                <ul class="list-disc list-inside pl-2 space-y-0.5">
                    <li>Pelo menos 8 caracteres</li>
                    <li>Pelo menos uma letra</li>
                    <li>Pelo menos um número</li>
                </ul>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['id' => 'update_password_password_confirmation','name' => 'password_confirmation','label' => __('Confirmar Nova Senha'),'autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'update_password_password_confirmation','name' => 'password_confirmation','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Confirmar Nova Senha')),'autocomplete' => 'new-password']); ?>
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->updatePassword->get('password_confirmation'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->updatePassword->get('password_confirmation')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>

        <div class="flex items-center gap-4">
            <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e(__('Atualizar Senha')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>

            <?php if(session('status') === 'password-updated'): ?>
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-medium"
                ><?php echo e(__('Senha atualizada com sucesso!')); ?></p>
            <?php endif; ?>
        </div>
    </form>
</section>
<?php /**PATH /var/www/pagby/resources/views/profile/partials/update-password-form-with-component.blade.php ENDPATH**/ ?>