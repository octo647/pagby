
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
            Criar Novo Plano
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <form method="POST" action="<?php echo e(route('plans.store')); ?>"
                  x-data="{ open: false, selectedServices: [] }">
                <?php echo csrf_field(); ?>

                <div class="mb-4">
                    <label class="block font-bold">Nome do Plano</label>
                    <input type="text" name="name" class="border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Mensalidade</label>
                    <input type="number" step="0.01" name="price" class="border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Duração (dias)</label>
                    <input type="number" name="duration_days" class="border rounded w-full" required>
                </div>
                <!-- Campo para seleção de serviços -->
                <div class="mb-4">
                    <button type="button" @click="open = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
                        Selecionar Serviços
                    </button>
                    <div class="mb-2">
                        <span class="font-bold">Serviços selecionados:</span>
                        <span x-text="selectedServices.join(', ')"></span>
                    </div>
                    <input type="hidden" name="services" :value="JSON.stringify(selectedServices)">
                </div>
                <!-- Modal de seleção de serviços -->
                <div x-show="open" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded shadow w-96">
                        <h3 class="text-lg font-bold mb-2">Selecione os Serviços</h3>
                        <div class="max-h-60 overflow-y-auto">
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center mb-1">
                                    <input type="checkbox"
                                        :value="<?php echo e($service->id); ?>"
                                        x-model="selectedServices"
                                    >
                                    <span class="ml-2"><?php echo e($service->service); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" @click="open = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>
                </div>
                <!-- Fim do campo para serviços -->

                <div class="mb-4">
                    <label class="block font-bold">Serviços Adicionais (JSON)</label>
                    <input type="text" name="additional_services" class="border rounded w-full">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Recursos (features, JSON)</label>
                    <input type="text" name="features" class="border rounded w-full">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Ativo?</label>
                    <select name="active" class="border rounded w-full">
                        <option value="1" selected>Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Filial (branch_id)</label>
                    <input type="number" name="branch_id" class="border rounded w-full" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
            </form>
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
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/plans/create.blade.php ENDPATH**/ ?>