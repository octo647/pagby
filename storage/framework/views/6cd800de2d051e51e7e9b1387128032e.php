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
            <?php echo e(__('Novo agendamento')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
     <?php if(session()->has('message') || session()->has('warning')): ?>
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium"><?php echo e(session('message')); ?></span>
                <span class="font-medium"><?php echo e(session('warning')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div 
        x-data="{
            branchCount: 0,
            chosenBranch: null,
            professionalChosen: false,
            serviceChosen: false,
            showAgenda: false
        }"
        x-init="
            window.addEventListener('branchesCount', e => { branchCount = e.detail.count });
            window.addEventListener('branchChosen', e => { chosenBranch = e.detail.branch });
            window.addEventListener('professionalChosen', e => { professionalChosen = true });
            window.addEventListener('serviceChosen', e => { serviceChosen = true });
            window.addEventListener('showAgenda', e => { showAgenda = true });
        "
        @booking-data-restored.window="
            professionalChosen = true;
            serviceChosen = true;
            showAgenda = true;
        "
        class="p-4 mx-auto max-w-4xl flex flex-col gap-4"
    >
        <!-- Card Filial -->
        <div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('branches');

$__html = app('livewire')->mount($__name, $__params, 'lw-2341916660-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <!-- Card Profissional -->
        <div id="choose-employee" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto" x-cloak
            x-show="!showAgenda"
            x-transition>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.choose-employee');

$__html = app('livewire')->mount($__name, $__params, 'lw-2341916660-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <!-- Card Serviço -->
        <div id="choose-service" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto" x-cloak
            x-show="professionalChosen && !showAgenda"
            x-transition>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.choose-service');

$__html = app('livewire')->mount($__name, $__params, 'lw-2341916660-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <!-- Card Horário -->
        <div id="agenda" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto"
            x-show="professionalChosen && serviceChosen"
            x-transition
            x-init="$watch('professionalChosen', v => { if(v && serviceChosen) showAgenda = true }); $watch('serviceChosen', v => { if(v && professionalChosen) showAgenda = true });"
        >
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.make-appointment');

$__html = app('livewire')->mount($__name, $__params, 'lw-2341916660-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
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
<?php endif; ?><?php /**PATH /home/helder/projetos/pagby/resources/views/cliente/agendamento.blade.php ENDPATH**/ ?>