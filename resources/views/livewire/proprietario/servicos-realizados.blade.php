<div>
   {{-- Este componente exibe os serviços realizados por funcionário
    e permite filtrar por funcionário, filial, mês/ano ou data.
    Ele também exibe o total de serviços realizados e permite limpar os filtros. --}}
    <div class="flex justify-between items-center mb-4">
    
  
    <div class="flex items-center space-x-2">
        <label for="employeeFilter" class="text-sm">Funcionário:</label>
        <select id="employeeFilter" wire:model.live="selectedEmployee" class="border rounded p-2">
            <option value="">Todos</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        </select>
        <label for="branchFilter" class="text-sm">Filial:</label>
        <select id="branchFilter" wire:model.live="selectedBranch" class="border rounded p-2">
            <option value="">Todas</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
            @endforeach
        </select>
       
        @if($showMonthFilter == false && $showDateFilter == false)
        <label for="timeFilter" class="text-sm">Filtro do tempo:</label>
        <select id="timeFilter" wire:model.live="selectedTime" class="border rounded p-2">
            <option value="">Nenhum</option>
            <option value="mes_ano">Mês/ano</option>
            <option value="data">Data</option>
        </select>
        @endif
        @if($showMonthFilter== true)
        <label for="monthFilter" class="text-sm oculto">Mês:</label>
        <select id="monthFilter" wire:model.live="selectedMonth" class="border rounded p-2">
        <option value="">Todos</option>
            @foreach(range(1,12) as $m)
                <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
            @endforeach
        </select>
        <label for="yearFilter" class="text-sm oculto">Ano:</label>
        <select id="yearFilter" wire:model.live="selectedYear" class="border rounded p-2">
            <option value="">Todos</option>
            @foreach(range(date('Y')-5, date('Y')) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
        @endif
        @if($showDateFilter == true)
        <label for="dateFilter" class="text-sm">Data:</label>
            <input type="date" id="dateFilter" wire:model.live="selectedDate" class="border rounded p-2">
        @endif    

        <button wire:click="resetFilters" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Limpar Filtros
        </button>
    </div>    
    </div>
    <div class="overflow-x-auto">
        <table class="tabela-escura w-full text-sm text-left">
        <thead>
            <tr>
                
                <th>Funcionário</th>
                <th>Filial</th>
                <th>Cliente</th>
                <th>Serviços</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Valor</th>
            </tr>
        </thead>
   
        {{-- Verifica se há agendamentos --}}
        {{-- Se não houver, exibe uma mensagem informando --}}
        {{-- Caso contrário, exibe os agendamentos --}}
        @if($agendamentos && count($agendamentos)==0)
            <tr>
                <td colspan="7" class="text-center">Nenhum agendamento encontrado.</td>
            </tr>
        @else
            @foreach($agendamentos as $agendamento)
                <tr>
                    
                    <td>
                    <a href="#" wire:click.prevent="showEmployeeDetails({{ $agendamento->employee->id }})" class="text-blue-600 hover:underline">
                    {{ $agendamento->employee->name ?? '-' }}
                    </a>               
                    </td>
                    <td>{{ $agendamento->branch->branch_name ?? '-' }}</td>
                    <td>{{ $agendamento->customer->name ?? '-' }}</td>

                    <td>
                    <span class="">{{ $agendamento->services }}
                    </span>                   
                    </td>
                    <td>{{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($agendamento->start_time)->format('H:i') }}</td>
                    <td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td>

                </tr>
                
            @endforeach
                <tr>
                    <td colspan="6" class="text-right font-semibold">
                        <span class="font-semibold">Total:</span>
                    </td>
                    <td>
                        R$ {{ number_format($agendamentos->sum('total'), 2, ',', '.') }}
                    </td>
                </tr>
        </tbody>

    </table>
        @endif
    </div>
    
    @if(method_exists($agendamentos, 'links'))
    <div class="mt-4">
    {{ $agendamentos->links() }}
    </div>
    @endif
    @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
            <h2 class="text-xl font-bold mb-4">Detalhes do Funcionário</h2>
            <ul class="mb-4">
                <li><strong>Nome:</strong> {{ $employeeDetails['nome'] }}</li>
                <li><strong>Email:</strong> {{ $employeeDetails['email'] }}</li>
               {{-- <li><strong>WhatsApp:</strong> {{ $employeeDetails['whatsapp'] }}</li> --}}
                <li><strong>Filial:</strong> 
                @foreach($employeeDetails['filiais'] as $filial)
                    <span class="">{{ $filial }}</span>
                @endforeach
                </li>
                <li>
                <strong>Serviços realizados:</strong> {{ $employeeDetails['agendamentos'] }} em {{ $employeeDetails['meses_trabalhados'] }} 
                @if($employeeDetails['meses_trabalhados'] > 1) meses 
                @else mês 
                @endif
                </li>
                <li><strong>Faturamento total:</strong> {{ $employeeDetails['faturamento_total'] }}</li>
                <li><strong>Tem agendamento marcado?</strong> 
                @if($employeeDetails['tem_agendamentos']==true)
                    <span class="text-green-600">Sim</span></li>
                    <li><strong>Agendamentos marcados:</strong></li>
                    <ul class="list-disc pl-5">               
                {{-- Itera sobre as datas dos agendamentos marcados --}}
                {{-- Formata a data para o formato 'd/m/Y' --}} 
                {{-- Formata a hora para o formato 'H:i' --}}
               @foreach($employeeDetails['datas_agendamentos'] as $data)
                   <li> <span class="block">{{ \Carbon\Carbon::parse($data['appointment_date'])->format('d/m/Y') }} -- {{ \Carbon\Carbon::parse($data['start_time'])->format('H:i') }}</span>
                {{-- Itera sobre os serviços agendados para cada data --}}
                
                   
                   
                   </li>
                @endforeach
                </ul>
                

                @else
                    <li><span class="text-red-600">Não</span></li>
                @endif
                </ul>


                
                
            </ul>
            <button wire:click="closeModal" class="bg-pink-600 text-white px-4 py-2 rounded">Fechar</button>
        </div>
    </div>
@endif



</div>
</div>
