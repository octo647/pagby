<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['disabled' => false, 'id', 'name', 'label', 'placeholder' => '', 'autocomplete' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['disabled' => false, 'id', 'name', 'label', 'placeholder' => '', 'autocomplete' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div>
    <?php if($label): ?>
        <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => ''.e($id).'','value' => $label]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => ''.e($id).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
    <?php endif; ?>
    
    <div class="relative">
        <input 
            <?php echo e($disabled ? 'disabled' : ''); ?> 
            <?php echo $attributes->merge([
                'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full pr-10',
                'type' => 'password',
                'id' => $id,
                'name' => $name,
                'placeholder' => $placeholder,
                'autocomplete' => $autocomplete
            ]); ?> 
        />
        
        <button 
            type="button" 
            class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors" 
            onclick="togglePasswordVisibility('<?php echo e($id); ?>')"
            title="Mostrar/Ocultar senha"
            tabindex="-1"
        >
            <svg id="eye-open-<?php echo e($id); ?>" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <svg id="eye-closed-<?php echo e($id); ?>" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
            </svg>
        </button>
    </div>
    
    <?php echo e($slot); ?>

</div>

<script>
window.togglePasswordVisibility = function(fieldId) {
    const field = document.getElementById(fieldId);
    const isPassword = field.type === 'password';
    
    // Toggle field type
    field.type = isPassword ? 'text' : 'password';
    
    const openEye = document.getElementById('eye-open-' + fieldId);
    const closedEye = document.getElementById('eye-closed-' + fieldId);
    
    // Toggle eye icons
    if (isPassword) {
        openEye.classList.add('hidden');
        closedEye.classList.remove('hidden');
    } else {
        openEye.classList.remove('hidden');
        closedEye.classList.add('hidden');
    }
}
</script>
<?php /**PATH /var/www/pagby/resources/views/components/password-input.blade.php ENDPATH**/ ?>