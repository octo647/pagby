<div class="p-6">
    <h3 class="text-lg font-semibold mb-4">Funcionários x Serviços</h3>
    <p class="text-gray-600 mb-4">Selecione um funcionário para gerenciar seus serviços:</p>
    
    {{-- Checkbox para filtrar apenas funcionários ativos --}}
    <div class="mb-6">
        <label class="inline-flex items-center cursor-pointer px-4 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
            <input type="checkbox" wire:model.live="showOnlyActive" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
            <span class="ml-2 text-sm font-medium text-gray-700">Mostrar apenas funcionários ativos</span>
        </label>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @if($employees->count() > 0)
            @foreach($employees as $employee)
                @if($employee->status === 'Ativo')
                    <a href="{{ route('tenant.dashboard', ['tabelaAtiva' => 'func_serv', 'funcionario_id' => $employee->id]) }}" 
                       class="block p-4 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                            <img class="w-10 h-10 rounded-full object-cover border-2 border-gray-200" 
                                 src="{{ tenant_asset($employee->photo ?? 'default.jpg') }}" 
                                 alt="{{ $employee->name }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=6366f1&color=ffffff'">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $employee->name }}</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Ativo
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Clique para gerenciar serviços</p>
                    </a>
                @else
                    <div class="block p-4 bg-gray-50 border border-gray-300 rounded-lg shadow opacity-60 cursor-not-allowed">
                        <div class="flex items-center space-x-3 mb-2">
                            <img class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 grayscale" 
                                 src="{{ tenant_asset($employee->photo ?? 'default.jpg') }}" 
                                 alt="{{ $employee->name }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=6366f1&color=ffffff'">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-700">{{ $employee->name }}</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Inativo
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm italic">Funcionário inativo</p>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-span-3">
                <p class="text-gray-500 text-center py-8">Nenhum funcionário encontrado.</p>
            </div>
        @endif
    </div>
</div>
