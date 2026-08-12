<div>
    
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtros de Assiduidade</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente</label>
                <input id="search" type="text" wire:model.live="search" placeholder="Nome ou e-mail" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="minAppointments" class="block text-sm font-medium text-gray-700 mb-1">Min. Agendamentos</label>
                <input id="minAppointments" type="number" wire:model.live="minAppointments" min="1" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="periodStart" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                <input id="periodStart" type="date" wire:model.live="periodStart" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="periodEnd" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                <input id="periodEnd" type="date" wire:model.live="periodEnd" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="daysSinceLast" class="block text-sm font-medium text-gray-700 mb-1">Dias sem Agendar</label>
                <input id="daysSinceLast" type="number" wire:model.live="daysSinceLast" min="1" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    
    <div class="hidden lg:block bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Análise de Assiduidade dos Clientes</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr class="text-left">
                        <th class="px-4 py-3 text-sm font-medium text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700">Contato</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Agendamentos</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Primeiro</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Último</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Dias Ausente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $primeiro = $cliente->clientAppointments->sortBy('appointment_date')->first();
                            $ultimo = $cliente->clientAppointments->sortByDesc('appointment_date')->first();
                            
                            // Calcular dias ausente: só conta se o último agendamento foi no passado
                            $diasAbs = 0;
                            if ($ultimo) {
                                $ultimaData = \Carbon\Carbon::parse($ultimo->appointment_date);
                                if ($ultimaData->isPast()) {
                                    // Só conta dias ausente se o último agendamento foi no passado
                                    $diasAbs = (int) $ultimaData->diffInDays(now());
                                } else {
                                    // Se o último agendamento é futuro, considera 0 dias ausente
                                    $diasAbs = 0;
                                }
                            }
                        ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?php echo e($cliente->name); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <a href="mailto:<?php echo e($cliente->email); ?>" class="text-blue-600 hover:underline block truncate">
                                        <?php echo e($cliente->email); ?>

                                    </a>
                                    <?php if($cliente->whatsapp): ?>
                                        <a href="https://wa.me/<?php echo e($cliente->phone); ?>" target="_blank" class="text-green-600 hover:underline text-xs">
                                            📱 <?php echo e($cliente->phone); ?>

                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($cliente->client_appointments_count); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                <?php echo e($primeiro ? \Carbon\Carbon::parse($primeiro->appointment_date)->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                <?php echo e($ultimo ? \Carbon\Carbon::parse($ultimo->appointment_date)->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Agendado
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?php if($diasAbs == 0): ?> bg-blue-100 text-blue-800
                                        <?php elseif($diasAbs <= 30): ?> bg-green-100 text-green-800
                                        <?php elseif($diasAbs <= 60): ?> bg-yellow-100 text-yellow-800
                                        <?php else: ?> bg-red-100 text-red-800
                                        <?php endif; ?>">
                                        <?php echo e($diasAbs); ?> dias
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Nenhum cliente encontrado para os critérios selecionados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="lg:hidden space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $primeiro = $cliente->clientAppointments->sortBy('appointment_date')->first();
                $ultimo = $cliente->clientAppointments->sortByDesc('appointment_date')->first();
                
                // Calcular dias ausente: só conta se o último agendamento foi no passado
                $diasAbs = 0;
                if ($ultimo) {
                    $ultimaData = \Carbon\Carbon::parse($ultimo->appointment_date);
                    if ($ultimaData->isPast()) {
                        // Só conta dias ausente se o último agendamento foi no passado
                        $diasAbs = (int) $ultimaData->diffInDays(now());
                    } else {
                        // Se o último agendamento é futuro, considera 0 dias ausente
                        $diasAbs = 0;
                    }
                }
            ?>
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900"><?php echo e($cliente->name); ?></div>
                        <a href="mailto:<?php echo e($cliente->email); ?>" class="text-sm text-blue-600 hover:underline truncate block">
                            <?php echo e($cliente->email); ?>

                        </a>
                        <?php if($cliente->whatsapp): ?>
                            <a href="https://wa.me/<?php echo e($cliente->phone); ?>" target="_blank" class="text-xs text-green-600 hover:underline">
                                📱 <?php echo e($cliente->phone); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="ml-3 flex-shrink-0 text-center">
                        <?php if($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Agendado
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php if($diasAbs == 0): ?> bg-blue-100 text-blue-800
                                <?php elseif($diasAbs <= 30): ?> bg-green-100 text-green-800
                                <?php elseif($diasAbs <= 60): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-red-100 text-red-800
                                <?php endif; ?>">
                                <?php echo e($diasAbs); ?> dias ausente
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-3 gap-4 pt-3 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">Total</div>
                        <div class="text-lg font-bold text-blue-600">
                            <?php echo e($cliente->client_appointments_count); ?>

                        </div>
                        <div class="text-xs text-gray-500">agendamentos</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">Primeiro</div>
                        <div class="text-sm font-semibold text-gray-800">
                            <?php echo e($primeiro ? \Carbon\Carbon::parse($primeiro->appointment_date)->format('d/m/Y') : '-'); ?>

                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">Último</div>
                        <div class="text-sm font-semibold text-gray-800">
                            <?php echo e($ultimo ? \Carbon\Carbon::parse($ultimo->appointment_date)->format('d/m/Y') : '-'); ?>

                        </div>
                    </div>
                </div>

                
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Status de Assiduidade</span>
                        <span class="font-medium 
                            <?php if($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()): ?> text-blue-600
                            <?php elseif($diasAbs == 0): ?> text-blue-600
                            <?php elseif($diasAbs <= 30): ?> text-green-600
                            <?php elseif($diasAbs <= 60): ?> text-yellow-600
                            <?php else: ?> text-red-600
                            <?php endif; ?>">
                            <?php if($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()): ?>
                                🔵 Agendado
                            <?php elseif($diasAbs == 0): ?>
                                🔵 Atual
                            <?php elseif($diasAbs <= 30): ?>
                                🟢 Ativo
                            <?php elseif($diasAbs <= 60): ?>
                                🟡 Moderado
                            <?php else: ?>
                                🔴 Ausente
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="mt-1 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full
                            <?php if($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()): ?> bg-gradient-to-r from-blue-400 to-blue-600
                            <?php elseif($diasAbs == 0): ?> bg-gradient-to-r from-blue-400 to-blue-600
                            <?php elseif($diasAbs <= 30): ?> bg-gradient-to-r from-green-400 to-green-600
                            <?php elseif($diasAbs <= 60): ?> bg-gradient-to-r from-yellow-400 to-yellow-600
                            <?php else: ?> bg-gradient-to-r from-red-400 to-red-600
                            <?php endif; ?>" 
                             style="width: <?php echo e(($ultimo && \Carbon\Carbon::parse($ultimo->appointment_date)->isFuture()) ? 100 : ($diasAbs == 0 ? 95 : ($diasAbs <= 30 ? 80 : ($diasAbs <= 60 ? 50 : 20)))); ?>%"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">👥</div>
                <p class="text-lg font-medium">Nenhum cliente encontrado</p>
                <p class="text-sm">Ajuste os filtros para ver os resultados.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-6 bg-white p-4 rounded-lg shadow-sm border">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <label class="flex items-center space-x-2 text-sm">
                <input type="checkbox" wire:model.live="showAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="text-gray-700">Mostrar todos os resultados em uma página</span>
            </label>
            
            <?php if(!$showAll): ?>   
                <div class="flex justify-center sm:justify-end">
                    <?php echo e($clientes->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>   

</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/assiduidade.blade.php ENDPATH**/ ?>