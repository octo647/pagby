<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100">
    {{-- Header Principal --}}
    <div class="bg-white shadow-sm border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <span class="text-gray-500 text-sm">Proprietário</span>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-indigo-600 text-sm font-medium">Serviços dos Funcionários</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-7 h-7 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Gestão de Serviços por Funcionário
                    </h1>
                    <p class="text-gray-600 mt-1">Configure quais serviços cada funcionário pode executar e personalize os tempos de atendimento</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Painel de Seleção de Funcionários --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-4">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-4">
                        <h3 class="text-lg font-medium text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Funcionários
                        </h3>
                        <p class="text-indigo-100 text-sm mt-1">Selecione um funcionário para editar</p>
                    </div>
                    
                    <div class="p-4 max-h-96 overflow-y-auto">
                        @if(count($employees) > 0)
                            <div class="space-y-3">
                                @foreach($employees as $employee)
                                <button wire:click="selectEmployee({{ $employee['id'] }})" 
                                        class="w-full text-left p-3 rounded-lg border transition-all duration-200 hover:shadow-md
                                               {{ $selectedEmployeeId == $employee['id'] ? 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-300 shadow-md' : 'bg-white border-gray-200 hover:border-indigo-300' }}">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img class="w-10 h-10 rounded-full object-cover border-2 
                                                       {{ $selectedEmployeeId == $employee['id'] ? 'border-indigo-400' : 'border-gray-200' }}" 
                                                 src="{{ tenant_asset($employee['photo']) }}" 
                                                 alt="{{ $employee['name'] }}"
                                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee['name']) }}&background=6366f1&color=ffffff'">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium {{ $selectedEmployeeId == $employee['id'] ? 'text-indigo-800' : 'text-gray-900' }} truncate">
                                                    {{ $employee['name'] }}
                                                </p>
                                                @if($employee['status'] === 'Ativo')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Ativo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Inativo
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs {{ $selectedEmployeeId == $employee['id'] ? 'text-indigo-600' : 'text-gray-500' }}">
                                                {{ count($employee['services']) }} serviços configurados
                                            </p>
                                        </div>
                                        @if($selectedEmployeeId == $employee['id'])
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 mb-2">Nenhum funcionário encontrado</p>
                                <p class="text-xs text-gray-400">Cadastre funcionários primeiro</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Painel Principal de Edição --}}
            <div class="lg:col-span-3">
                @if($selectedEmployeeId)
                    @php
                        $employee = collect($employees)->firstWhere('id', $selectedEmployeeId);
                    @endphp
                    
                    @if($employee)
                        {{-- Header do Funcionário Selecionado --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6">
                                <div class="flex items-center space-x-4">
                                    <img class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-lg" 
                                         src="{{ tenant_asset($employee['photo']) }}" 
                                         alt="{{ $employee['name'] }}"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee['name']) }}&background=ffffff&color=6366f1&size=128'">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white">{{ $employee['name'] }}</h2>
                                        <p class="text-indigo-100 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H8a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2"></path>
                                            </svg>
                                            Configure os serviços que este funcionário pode executar
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <p class="text-gray-500">{{ __('Funcionário não encontrado') }}</p>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Estado Inicial - Nenhum funcionário selecionado --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">Selecione um Funcionário</h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-4">
                                Escolha um funcionário no painel à esquerda para configurar os serviços que ele pode executar e personalizar os tempos de atendimento.
                            </p>
                            
                            
                        </div>
                    </div>
                @endif
    
                @if($selectedEmployeeId && $employee)
                    {{-- Lista de Serviços --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        Configuração de Serviços
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">Marque os serviços que {{ $employee['name'] }} pode executar</p>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ count(array_filter($services, fn($s) => in_array($s['id'], $employee['services']))) }}/{{ count($services) }} serviços ativos
                                </div>
                            </div>
                        </div>

                        {{-- Versão Desktop --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Executa?</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo Padrão</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo Personalizado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($services as $service)
                                        @php
                                            $serviceModel = App\Models\Service::find($service['id']);
                                            $customDuration = null;
                                            $isActive = in_array($service['id'], $employee['services']);
                                            
                                            
                                            if ($isActive) {
                                                $employeeModel = App\Models\User::find($employee['id']);
                                                $userService = $employeeModel->services()->where('service_id', $service['id'])->first();
                                                $customDuration = $userService?->pivot?->custom_duration_minutes;
                                            }
                                        @endphp
                                        <tr class="hover:bg-gray-200 transition-colors {{ $isActive ? 'bg-blue-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-2 h-2 rounded-full mr-3 {{ $isActive ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $service['service'] }}</div>
                                                        @if($customDuration)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-200 text-blue-800 mt-1">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Tempo personalizado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                           wire:click="changeService({{ $service['id'] }}, {{ $employee['id'] }})"
                                                           {{ $isActive ? 'checked' : '' }}
                                                           class="sr-only peer">
                                                    <div class="relative w-11 h-6 bg-gray-400 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                </label>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $serviceModel?->time ?? 30 }} min
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($isActive)
                                                    <div class="flex flex-col items-center space-y-2">
                                                        <div class="relative">
                                                            <input type="number" 
                                                                min="1" 
                                                                max="300"
                                                                placeholder="{{ $serviceModel?->time ?? 30 }}"
                                                                value="{{ $customDuration }}"
                                                                wire:blur="updateCustomDuration({{ $service['id'] }}, {{ $employee['id'] }}, $event.target.value)"
                                                                class="w-20 px-4 py-3 text-base border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center bg-white"
                                                                style="-webkit-appearance: auto; -moz-appearance: textfield;">
                                                            <span class="absolute right-1 top-1/2 transform -translate-y-1/2 text-sm text-gray-400">min</span>
                                                        </div>
                                                        @if($customDuration)
                                                            <span class="text-blue-600 font-medium bg-blue-50 px-2 py-1 rounded">
                                                                {{ $customDuration }} min configurado
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-gray-500">usando padrão ({{ $serviceModel?->time ?? 30 }}min)</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Versão Mobile/Tablet --}}
                        <div class="block lg:hidden p-4 space-y-4">
                            @foreach($services as $service)
                                @php
                                    $serviceModel = App\Models\Service::find($service['id']);
                                    $customDuration = null;
                                    $isActive = in_array($service['id'], $employee['services']);
                                    
                                    if ($isActive) {
                                        $employeeModel = App\Models\User::find($employee['id']);
                                        $userService = $employeeModel->services()->where('service_id', $service['id'])->first();
                                        $customDuration = $userService?->pivot?->custom_duration_minutes;
                                    }
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 {{ $isActive ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200' : 'bg-white' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full {{ $isActive ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                            <h4 class="font-medium text-gray-900">{{ $service['service'] }}</h4>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   wire:click="changeService({{ $service['id'] }}, {{ $employee['id'] }})"
                                                   {{ $isActive ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-400 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                            <p class="text-xs font-medium text-gray-600 mb-1">Tempo Padrão</p>
                                            <p class="text-sm font-bold text-gray-900">{{ $serviceModel?->time ?? 30 }} min</p>
                                        </div>
                                        
                                        @if($isActive)
                                        <div class="text-center">
                                            <p class="text-xs font-medium text-gray-600 mb-2">Tempo Personalizado</p>
                                            <div class="relative">
                                                <input type="number" 
                                                    min="1" 
                                                    max="300"
                                                    placeholder="{{ $serviceModel?->time ?? 30 }}"
                                                    value="{{ $customDuration }}"
                                                    wire:blur="updateCustomDuration({{ $service['id'] }}, {{ $employee['id'] }}, $event.target.value)"
                                                    class="w-20 px-4 py-3 text-base border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                                                    style="-webkit-appearance: auto; -moz-appearance: textfield;">
                                            </div>
                                            @if($customDuration)
                                                <p class="text-xs text-blue-600 font-medium mt-1">{{ $customDuration }} min</p>
                                            @endif
                                        </div>
                                        @else
                                        <div class="text-center p-3 bg-gray-50 rounded-lg opacity-50">
                                            <p class="text-xs text-gray-500">Inativo</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($customDuration)
                                        <div class="mt-3 p-2 bg-blue-100 rounded-lg">
                                            <p class="text-xs text-blue-800 text-center font-medium">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Tempo personalizado configurado
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            @if(count($services) == 0)
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Nenhum serviço cadastrado</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

