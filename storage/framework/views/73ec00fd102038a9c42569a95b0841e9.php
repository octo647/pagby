<div>
    
    <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Filtros de Pesquisa</h2>
            <p class="text-sm text-gray-600">Filtre os serviços realizados por funcionário, filial e período</p>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            
            <div>
                <label for="employeeFilter" class="block text-sm font-medium text-gray-700 mb-1">Funcionário:</label>
                <select id="employeeFilter" wire:model.live="selectedEmployee" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os funcionários</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label for="branchFilter" class="block text-sm font-medium text-gray-700 mb-1">Filial:</label>
                <select id="branchFilter" wire:model.live="selectedBranch" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas as filiais</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->branch_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <?php if($showMonthFilter == false && $showDateFilter == false): ?>
            <div>
                <label for="timeFilter" class="block text-sm font-medium text-gray-700 mb-1">Período:</label>
                <select id="timeFilter" wire:model.live="selectedTime" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sem filtro de período</option>
                    <option value="mes_ano">Por mês/ano</option>
                    <option value="data">Por data específica</option>
                </select>
            </div>
            <?php endif; ?>

            
            <div class="flex items-end">
                <button wire:click="resetFilters" 
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-sm font-medium">
                    <span class="hidden sm:inline">Limpar Filtros</span>
                    <span class="sm:hidden">Limpar</span>
                </button>
            </div>
        </div>

        
        <?php if($showMonthFilter == true): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div>
                <label for="monthFilter" class="block text-sm font-medium text-gray-700 mb-1">Mês:</label>
                <select id="monthFilter" wire:model.live="selectedMonth" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os meses</option>
                    <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(sprintf('%02d', $m)); ?>">
                            <?php echo e(DateTime::createFromFormat('!m', $m)->format('F')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="yearFilter" class="block text-sm font-medium text-gray-700 mb-1">Ano:</label>
                <select id="yearFilter" wire:model.live="selectedYear" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os anos</option>
                    <?php $__currentLoopData = range(date('Y')-5, date('Y')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($showDateFilter == true): ?>
        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="max-w-md">
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Data específica:</label>
                <input type="date" id="dateFilter" wire:model.live="selectedDate" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Serviços Realizados</h3>
                    <p class="text-sm text-gray-600">
                        <?php if($agendamentos && count($agendamentos) > 0): ?>
                            <?php echo e(count($agendamentos)); ?> serviço(s) encontrado(s)
                        <?php else: ?>
                            Nenhum resultado encontrado
                        <?php endif; ?>
                    </p>
                </div>
                <?php if($agendamentos && count($agendamentos) > 0): ?>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total Geral:</div>
                    <div class="text-xl font-bold text-green-600">
                        R$ <?php echo e(number_format($agendamentos->sum('total'), 2, ',', '.')); ?>

                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if($agendamentos && count($agendamentos) > 0): ?>
            
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Funcionário</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Filial</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Serviços</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Data</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Horário</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $agendamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="#" wire:click.prevent="showEmployeeDetails(<?php echo e($agendamento->employee->id); ?>)" 
                                       class="text-blue-600 hover:underline font-medium">
                                        <?php echo e($agendamento->employee->name ?? '-'); ?>

                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e($agendamento->branch->branch_name ?? '-'); ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($agendamento->customer->name ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700">
                                        <?php $__currentLoopData = array_filter(preg_split('/\s*[,\/]+\s*/', $agendamento->services)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e(trim($item)); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    <?php echo e(\Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y')); ?>

                                </td>
                                <td class="px-4 py-3 text-center text-sm font-mono">
                                    <?php echo e(\Carbon\Carbon::parse($agendamento->start_time)->format('H:i')); ?>

                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-green-600">
                                    R$ <?php echo e(number_format($agendamento->total, 2, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <div class="lg:hidden p-4 space-y-4">
                <?php $__currentLoopData = $agendamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900"><?php echo e($agendamento->customer->name ?? '-'); ?></div>
                                <div class="text-sm text-gray-600"><?php echo e($agendamento->branch->branch_name ?? '-'); ?></div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">
                                    R$ <?php echo e(number_format($agendamento->total, 2, ',', '.')); ?>

                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y')); ?> • 
                                    <?php echo e(\Carbon\Carbon::parse($agendamento->start_time)->format('H:i')); ?>

                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-600">Funcionário:</span>
                            <a href="#" wire:click.prevent="showEmployeeDetails(<?php echo e($agendamento->employee->id); ?>)" 
                               class="text-blue-600 hover:underline font-medium ml-1">
                                <?php echo e($agendamento->employee->name ?? '-'); ?>

                            </a>
                        </div>

                        
                        <div class="mt-2 p-3 bg-white rounded border-l-4 border-blue-500">
                            <div class="text-sm font-medium text-gray-600 mb-1">Serviços:</div>
                            <div class="text-sm text-gray-800">
                                <?php $__currentLoopData = array_filter(preg_split('/\s*[,\/]+\s*/', $agendamento->services)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e(trim($item)); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">💼</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum serviço encontrado</h3>
                <p class="text-gray-600 mb-6">
                    Não foram encontrados serviços realizados com os filtros aplicados.
                </p>
                <button wire:click="resetFilters" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Remover Filtros
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if(method_exists($agendamentos, 'links')): ?>
    <div class="mt-4">
    <?php echo e($agendamentos->links()); ?>

    </div>
    <?php endif; ?>
    
    <?php if($showModal): ?>
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
        <div class="bg-white rounded-lg shadow-xl z-10 max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
            
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold"><?php echo e($employeeDetails['nome']); ?></h2>
                        <p class="text-blue-100 text-sm">Detalhes completos do funcionário</p>
                    </div>
                    <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            
            <div class="p-6 max-h-96 overflow-y-auto">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email:</label>
                            <p class="text-gray-900"><?php echo e($employeeDetails['email']); ?></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Filiais:</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $employeeDetails['filiais']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($filial); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Serviços Realizados:</label>
                            <div class="text-2xl font-bold text-blue-600">
                                <?php echo e($employeeDetails['agendamentos']); ?>

                                <span class="text-sm font-normal text-gray-600">
                                    em <?php echo e($employeeDetails['meses_trabalhados']); ?> 
                                    <?php echo e($employeeDetails['meses_trabalhados'] > 1 ? 'meses' : 'mês'); ?>

                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Faturamento Total:</label>
                            <div class="text-2xl font-bold text-green-600"><?php echo e($employeeDetails['faturamento_total']); ?></div>
                        </div>
                    </div>
                </div>

                
                <div class="border-t pt-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Agendamentos Futuros</h3>
                        <?php if($employeeDetails['tem_agendamentos']): ?>
                            <span class="ml-3 inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Possui agendamentos
                            </span>
                        <?php else: ?>
                            <span class="ml-3 inline-flex px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Sem agendamentos
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if($employeeDetails['tem_agendamentos']): ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $employeeDetails['datas_agendamentos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <?php echo e(\Carbon\Carbon::parse($data['appointment_date'])->format('d/m/Y')); ?>

                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Horário: <?php echo e(\Carbon\Carbon::parse($data['start_time'])->format('H:i')); ?>

                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Agendado
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📅</div>
                            <p class="text-sm">Este funcionário não possui agendamentos futuros.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button wire:click="closeModal" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>



</div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/servicos-realizados.blade.php ENDPATH**/ ?>