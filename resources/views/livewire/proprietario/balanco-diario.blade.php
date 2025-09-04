<div>
    {{-- Be like water. --}}
   <div class="mb-4">
    <label for="data" class="block font-bold mb-1">Escolha o dia do balanço</label>
    <input wire:model.lazy="data" id="data" type="date" class="border rounded px-2 py-1">
</div>
<div class="overflow-x-auto">
    <h2 class="text-lg font-bold mb-4">Balanço Diário </h2>
  
<table class="min-w-full bg-white border border-gray-200">
    <thead class="bg-gray-100">
    

        <tr>
            <th>Cliente</th>
            <th>Serviço</th>
            <th>Funcionário</th>
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
                
                <td>
                    <select wire:change="atualizarStatus({{ $agendamento->id }}, $event.target.value)" class="border rounded px-6 py-0.5">
                        <option value="Pendente" {{ $agendamento->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Confirmado" {{ $agendamento->status == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="Realizado" {{ $agendamento->status == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="Cancelado" {{ $agendamento->status == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </td>
                <td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td>
                <td>{{ $agendamento->payment_method ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mb-4">
    <label for="totalCaixa" class="block font-bold mb-1">Total recebido no caixa:</label>
    <input type="number" step="0.01" wire:model.live="totalCaixa" id="totalCaixa" class="border rounded px-2 py-1" />
</div>
<p><strong>Total de serviços pagos (Confirmado + Realizado):</strong> R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
<p><strong>Total recebido no caixa:</strong> R$ {{ number_format($totalCaixa ?: 0, 2, ',', '.') }}</p>
@if($totalPago != $totalCaixa)
    <p class="text-red-600 font-bold">Atenção: Divergência entre serviços pagos e valor no caixa!</p>
@endif
</div>
</div>
