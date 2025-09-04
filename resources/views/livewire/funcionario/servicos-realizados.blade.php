<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="p-6 bg-white border-b border-gray-200">
    <div class="mb-4 flex gap-2">
    <button wire:click="setFiltroPeriodo('1D')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1D' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1D</button>
    <button wire:click="setFiltroPeriodo('5D')" class="px-2 py-1 rounded {{ $filtroPeriodo === '5D' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">5D</button>
    <button wire:click="setFiltroPeriodo('1M')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1M' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1M</button>
    <button wire:click="setFiltroPeriodo('6M')" class="px-2 py-1 rounded {{ $filtroPeriodo === '6M' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">6M</button>
    <button wire:click="setFiltroPeriodo('1A')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1A' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1A</button>
    <button wire:click="setFiltroPeriodo('Tudo')" class="px-2 py-1 rounded {{ $filtroPeriodo === 'Tudo' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Tudo</button>
</div>
      
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                     
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>  
                        
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Razão do cancelamento</th>

                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($appointments as $index=>$servico)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($servico->appointment_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $servico->services }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">{{ $servico->customer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $servico->total}}</td>                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="atualizarStatus({{ $servico->id }}, $event.target.value)" class="border rounded px-1 py-0.5">
                                    <option value="Pendente" {{ $servico->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="Confirmado" {{ $servico->status == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="Realizado" {{ $servico->status == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                                    <option value="Cancelado" {{ $servico->status == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            
                            </td>                            
                            <td class="px-6 py-4 whitespace-nowrap">{{ $servico->cancellation_reason ?? '--'}} </td>


                        </tr>
                    @endforeach
                   
                </tbody> 
            </table>{{ $appointments->links() }}
        </div>
</div>
