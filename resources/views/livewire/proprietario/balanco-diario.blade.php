<div>
    {{-- Be like water. --}}
   <div class="mb-4 flex gap-4 items-end">
        <div>
            <label for="branch_id" class="block font-bold mb-1">Filial</label>
            <select wire:model.live="branch_id" id="branch_id" class="border rounded px-2 py-1">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="data" class="block font-bold mb-1">Escolha o dia do balanço</label>
            <input wire:model.lazy="data" id="data" type="date" class="border rounded px-2 py-1">
            @error('data') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
   </div>
<div class="overflow-x-auto">
    <h2 class="text-lg font-bold mb-4">Balanço Diário </h2>
  
<table class="min-w-full bg-white border border-gray-200">
    <thead class="bg-gray-100">
    

        <tr>
            <th>Cliente</th>
            <th>Serviço</th>
            <th>Funcionário</th>
            <th>Filial</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Forma de Pagamento</th>
        </tr>
    </thead>
    <tbody>
        @foreach($agendamentosDoDia as $index=>$agendamento)
            <tr @if($agendamento->status == 'Pendente') class="bg-yellow-100" @endif>
                <td>{{ $agendamento->customer->name }}</td>
                <td>{{ $agendamento->services}}</td>
                <td>{{ $agendamento->employee->name }}</td>
                <td>{{ $agendamento->branch->branch_name }}</td>

                <td>
                    <select wire:change="atualizarStatus({{ $agendamento->id }}, $event.target.value)" class="border rounded px-6 py-0.5">
                        <option value="Pendente" {{ $agendamento->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Confirmado" {{ $agendamento->status == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="Realizado" {{ $agendamento->status == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="Cancelado" {{ $agendamento->status == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </td>
                <td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td>
                <td class="text-center">{{ $agendamento->payment_method ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label for="entrada" class="block font-bold mb-1">Entradas do caixa</label>
        <input type="number" step="0.01" wire:model="entrada" id="entrada" class="border rounded px-2 py-1 w-full" />
    </div>
    <div>
        <label for="saida" class="block font-bold mb-1">Saídas do caixa</label>
        <input type="number" step="0.01" wire:model="saida" id="saida" class="border rounded px-2 py-1 w-full" />
    </div>
    <div>
        <label for="saldo_final" class="block font-bold mb-1">Saldo final</label>
        <input type="number" step="0.01" wire:model="saldo_final" id="saldo_final" class="border rounded px-2 py-1 w-full bg-gray-100" readonly />
    </div>
</div>
<div class="mb-4">
    <button wire:click="salvarCaixa" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Salvar Caixa</button>
    @if(session()->has('message'))
        <span class="text-green-600 font-bold ml-4">{{ session('message') }}</span>
    @endif
</div>
<p><strong>Total de serviços pagos (Confirmado + Realizado):</strong> R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
<p><strong>Total recebido no caixa:</strong> R$ {{ number_format($totalCaixa ?: 0, 2, ',', '.') }}</p>
<p><strong>Comissão ({{ $comission }}%):</strong> R$ {{ number_format($value_comission ?: 0, 2, ',', '.') }}</p>
@if($totalPago != $entrada)
    <p class="text-red-600 font-bold">Atenção: Divergência entre serviços pagos e valor no caixa!</p>
@endif
</div>
</div>
