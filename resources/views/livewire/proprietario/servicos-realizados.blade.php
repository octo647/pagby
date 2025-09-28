<div>
    {{-- Serviços Realizados - Sistema Moderno de Gestão --}}
    
    {{-- Filtros Avançados --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Filtros de Pesquisa</h2>
            <p class="text-sm text-gray-600">Filtre os serviços realizados por funcionário, filial e período</p>
        </div>

        {{-- Filtros Principais - Layout Responsivo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Funcionário --}}
            <div>
                <label for="employeeFilter" class="block text-sm font-medium text-gray-700 mb-1">Funcionário:</label>
                <select id="employeeFilter" wire:model.live="selectedEmployee" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os funcionários</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filial --}}
            <div>
                <label for="branchFilter" class="block text-sm font-medium text-gray-700 mb-1">Filial:</label>
                <select id="branchFilter" wire:model.live="selectedBranch" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas as filiais</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo de Filtro Temporal --}}
            @if($showMonthFilter == false && $showDateFilter == false)
            <div>
                <label for="timeFilter" class="block text-sm font-medium text-gray-700 mb-1">Período:</label>
                <select id="timeFilter" wire:model.live="selectedTime" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sem filtro de período</option>
                    <option value="mes_ano">Por mês/ano</option>
                    <option value="data">Por data específica</option>
                </select>
            </div>
            @endif

            {{-- Botão Limpar Filtros --}}
            <div class="flex items-end">
                <button wire:click="resetFilters" 
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-sm font-medium">
                    <span class="hidden sm:inline">Limpar Filtros</span>
                    <span class="sm:hidden">Limpar</span>
                </button>
            </div>
        </div>

        {{-- Filtros de Mês/Ano --}}
        @if($showMonthFilter == true)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div>
                <label for="monthFilter" class="block text-sm font-medium text-gray-700 mb-1">Mês:</label>
                <select id="monthFilter" wire:model.live="selectedMonth" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os meses</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}">
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="yearFilter" class="block text-sm font-medium text-gray-700 mb-1">Ano:</label>
                <select id="yearFilter" wire:model.live="selectedYear" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os anos</option>
                    @foreach(range(date('Y')-5, date('Y')) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Filtro de Data Específica --}}
        @if($showDateFilter == true)
        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="max-w-md">
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Data específica:</label>
                <input type="date" id="dateFilter" wire:model.live="selectedDate" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        @endif
    </div>
    {{-- Lista de Serviços Realizados --}}
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Serviços Realizados</h3>
                    <p class="text-sm text-gray-600">
                        @if($agendamentos && count($agendamentos) > 0)
                            {{ count($agendamentos) }} serviço(s) encontrado(s)
                        @else
                            Nenhum resultado encontrado
                        @endif
                    </p>
                </div>
                @if($agendamentos && count($agendamentos) > 0)
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total Geral:</div>
                    <div class="text-xl font-bold text-green-600">
                        R$ {{ number_format($agendamentos->sum('total'), 2, ',', '.') }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($agendamentos && count($agendamentos) > 0)
            {{-- Versão Desktop - Tabela --}}
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
                        @foreach($agendamentos as $agendamento)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="#" wire:click.prevent="showEmployeeDetails({{ $agendamento->employee->id }})" 
                                       class="text-blue-600 hover:underline font-medium">
                                        {{ $agendamento->employee->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $agendamento->branch->branch_name ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $agendamento->customer->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700">
                                        @foreach(array_filter(preg_split('/\s*[,\/]+\s*/', $agendamento->services)) as $item)
                                            <div>{{ trim($item) }}</div>
                                        @endforeach
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    {{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-mono">
                                    {{ \Carbon\Carbon::parse($agendamento->start_time)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-green-600">
                                    R$ {{ number_format($agendamento->total, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Versão Mobile/Tablet - Cards --}}
            <div class="lg:hidden p-4 space-y-4">
                @foreach($agendamentos as $agendamento)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        {{-- Cabeçalho do Card --}}
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900">{{ $agendamento->customer->name ?? '-' }}</div>
                                <div class="text-sm text-gray-600">{{ $agendamento->branch->branch_name ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">
                                    R$ {{ number_format($agendamento->total, 2, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }} • 
                                    {{ \Carbon\Carbon::parse($agendamento->start_time)->format('H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Funcionário --}}
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-600">Funcionário:</span>
                            <a href="#" wire:click.prevent="showEmployeeDetails({{ $agendamento->employee->id }})" 
                               class="text-blue-600 hover:underline font-medium ml-1">
                                {{ $agendamento->employee->name ?? '-' }}
                            </a>
                        </div>

                        {{-- Serviços --}}
                        <div class="mt-2 p-3 bg-white rounded border-l-4 border-blue-500">
                            <div class="text-sm font-medium text-gray-600 mb-1">Serviços:</div>
                            <div class="text-sm text-gray-800">
                                @foreach(array_filter(preg_split('/\s*[,\/]+\s*/', $agendamento->services)) as $item)
                                    <div>{{ trim($item) }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Estado Vazio --}}
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
        @endif
    </div>
    
    @if(method_exists($agendamentos, 'links'))
    <div class="mt-4">
    {{ $agendamentos->links() }}
    </div>
    @endif
    {{-- Modal Detalhes do Funcionário --}}
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
        <div class="bg-white rounded-lg shadow-xl z-10 max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
            {{-- Header do Modal --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">{{ $employeeDetails['nome'] }}</h2>
                        <p class="text-blue-100 text-sm">Detalhes completos do funcionário</p>
                    </div>
                    <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo do Modal --}}
            <div class="p-6 max-h-96 overflow-y-auto">
                {{-- Informações Básicas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email:</label>
                            <p class="text-gray-900">{{ $employeeDetails['email'] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Filiais:</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($employeeDetails['filiais'] as $filial)
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $filial }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Serviços Realizados:</label>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $employeeDetails['agendamentos'] }}
                                <span class="text-sm font-normal text-gray-600">
                                    em {{ $employeeDetails['meses_trabalhados'] }} 
                                    {{ $employeeDetails['meses_trabalhados'] > 1 ? 'meses' : 'mês' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Faturamento Total:</label>
                            <div class="text-2xl font-bold text-green-600">{{ $employeeDetails['faturamento_total'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- Agendamentos Futuros --}}
                <div class="border-t pt-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Agendamentos Futuros</h3>
                        @if($employeeDetails['tem_agendamentos'])
                            <span class="ml-3 inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Possui agendamentos
                            </span>
                        @else
                            <span class="ml-3 inline-flex px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Sem agendamentos
                            </span>
                        @endif
                    </div>

                    @if($employeeDetails['tem_agendamentos'])
                        <div class="space-y-3">
                            @foreach($employeeDetails['datas_agendamentos'] as $data)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($data['appointment_date'])->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Horário: {{ \Carbon\Carbon::parse($data['start_time'])->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Agendado
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📅</div>
                            <p class="text-sm">Este funcionário não possui agendamentos futuros.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer do Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button wire:click="closeModal" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    @endif



</div>
</div>
