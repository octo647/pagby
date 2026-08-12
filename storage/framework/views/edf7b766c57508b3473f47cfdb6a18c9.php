
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
    <!-- ... (cabeçalho e outros elementos permanecem iguais) ... -->

    <div class="py-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <?php echo $__env->make('includes.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <form method="POST" action="<?php echo e(route('plans.update', $plan)); ?>"
                x-data='{
                    openServices: false,
                    openAdicionais: false,
                    selectedServices: <?php echo json_encode(old("services", $plan->services ?? []), 512) ?>,
                    selectedAdicionais: [], // Será preenchido no init()
                    allServices: <?php echo json_encode($services->map(fn($s) => ["id" => $s->id, "name" => $s->service]), 512) ?>,
                    descontos: {},
                    init() {
                        // Processa os serviços adicionais
                        const rawAdicionais = <?php echo json_encode(old("additional_services", $plan->additional_services ?? []), 512) ?>;
                        
                        // Converte para o formato correto {id, desconto}
                        this.selectedAdicionais = Array.isArray(rawAdicionais)
                            ? rawAdicionais.map(item => {
                                if (typeof item === "object" && item !== null) {
                                    return { id: item.id, desconto: item.desconto || "" };
                                }
                                return { id: item, desconto: "" };
                            })
                            : [];
                            
                        // Remove duplicatas
                        const uniqueIds = [];
                        this.selectedAdicionais = this.selectedAdicionais.filter(item => {
                            if (!uniqueIds.includes(item.id)) {
                                uniqueIds.push(item.id);
                                return true;
                            }
                            return false;
                        });
                        
                        // Inicializa o objeto de descontos
                        this.descontos = {};
                        this.selectedAdicionais.forEach(s => {
                            this.descontos[s.id] = s.desconto || "";
                        });
                    },
                    handleAdditionalServiceChange(serviceId, isChecked) {
                        if (isChecked) {
                            if (!this.selectedAdicionais.some(s => s.id == serviceId)) {
                                this.selectedAdicionais.push({
                                    id: serviceId,
                                    desconto: this.descontos[serviceId] || ""
                                });
                            }
                        } else {
                            this.selectedAdicionais = this.selectedAdicionais.filter(s => s.id != serviceId);
                            this.descontos[serviceId] = "";
                        }
                    },
                    updateDesconto(serviceId) {
                        const item = this.selectedAdicionais.find(s => s.id == serviceId);
                        if (item) {
                            item.desconto = this.descontos[serviceId] || "";
                        }
                    }
                }'
                x-init="init()"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                

                <!-- Campos do plano -->
                <div class="mb-4">
                    <label class="block font-bold">Nome do Plano</label>
                    <input type="text" name="name" class="border rounded w-full" required value="<?php echo e(old('name', $plan->name)); ?>">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Mensalidade</label>
                    <input type="number" step="0.01" name="price" class="border rounded w-full" required value="<?php echo e(old('price', $plan->price)); ?>">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Duração (dias)</label>
                    <input type="number" name="duration_days" class="border rounded w-full" required value="<?php echo e(old('duration_days', $plan->duration_days)); ?>">
                </div>

                <!-- Campo para seleção de serviços -->
                <div class="mb-4">
                    <button type="button" @click="openServices = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
                        Selecionar Serviços
                    </button>
                    <div class="mb-2">
                        <span class="font-bold">Serviços selecionados:</span>
                        <span x-text="selectedServices.join(', ')"></span>
                    </div>
                    <input type="hidden" name="services" :value="JSON.stringify(selectedServices)">
                </div>
                <!-- Modal de seleção de serviços -->
                <div x-show="openServices" x-cloak wire:ignore class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
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
                            <button type="button" @click="openServices = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>
                </div>
                <!-- Fim do campo para serviços -->
            <!-- Bloco de serviços adicionais -->
                <div class="mb-4">
                    <button type="button" @click="openAdicionais = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
                        Selecionar Serviços Adicionais
                    </button>

                    <div class="mb-2">
                        <span class="font-bold">Serviços adicionais selecionados:</span>
                        <template x-for="s in selectedAdicionais" :key="s.id">
                            <span class="inline-block bg-gray-200 rounded px-2 py-1 mx-1"
                                x-text="allServices.find(as => as.id == s.id)?.name + (descontos[s.id] ? ' ('+descontos[s.id]+'%)' : '')"></span>
                        </template>
                    </div>
                    <input type="hidden" name="additional_services" :value="JSON.stringify(selectedAdicionais)">
                </div>
  
    <!-- Modal de serviços adicionais -->
    <div x-show="openAdicionais" x-cloak wire:ignore class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-96">
        <h3 class="text-lg font-bold mb-2">Selecione os Serviços Adicionais</h3>
        <div class="max-h-60 overflow-y-auto">
            <template x-for="service in allServices" :key="service.id">
                <div class="flex items-center mb-2">
                    <input type="checkbox"
                        :value="service.id"
                        :id="'addserv-'+service.id"
                        @change="
                            if($event.target.checked) {
                                if (!selectedAdicionais.some(s => s.id == service.id)) {
                                    selectedAdicionais.push({id: service.id, desconto:descontos[service.id] || ''});
                                }
                            } else {
                                selectedAdicionais = selectedAdicionais.filter(s => s.id != service.id);
                                descontos[service.id] = '';
                            }
                        "
                        :checked="selectedAdicionais.some(s => s.id == service.id)"
                    >
                    <label class="ml-2" :for="'addserv-'+service.id" x-text="service.name"></label>
                    <input type="number" min="0" max="100" step="1"
                        class="ml-4 border rounded w-20"
                        placeholder="Desconto %"
                        x-model="descontos[service.id]"
                        :disabled="!selectedAdicionais.some(s => s.id == service.id)"
                    >
                </div>
            </template>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" @click="openAdicionais = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
        </div>
    </div>
</div>
                <!-- Fim do bloco de serviços adicionais -->

                <!-- Outros campos -->
                <div class="mb-4">
                    <label class="block font-bold">Recursos (features, JSON)</label>
                    <input type="text" name="features" class="border rounded w-full" value="<?php echo e(old('features', json_encode($plan->features ?? []))); ?>">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Ativo?</label>
                    <select name="active" class="border rounded w-full">
                        <option value="1" <?php echo e($plan->active ? 'selected' : ''); ?>>Sim</option>
                        <option value="0" <?php echo e(!$plan->active ? 'selected' : ''); ?>>Não</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Filial (branch_id)</label>
                    <input type="number" name="branch_id" class="border rounded w-full" required value="<?php echo e(old('branch_id', $plan->branch_id)); ?>">
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
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/plans/edit.blade.php ENDPATH**/ ?>