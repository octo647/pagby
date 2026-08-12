
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">📝 Histórico de Serviços Realizados</h1>
        <div class="mb-4 flex flex-wrap gap-2">
            <button wire:click="setFiltroPeriodo('1D')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1D' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1D</button>
            <button wire:click="setFiltroPeriodo('5D')" class="px-2 py-1 rounded {{ $filtroPeriodo === '5D' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">5D</button>
            <button wire:click="setFiltroPeriodo('1M')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1M' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1M</button>
            <button wire:click="setFiltroPeriodo('6M')" class="px-2 py-1 rounded {{ $filtroPeriodo === '6M' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">6M</button>
            <button wire:click="setFiltroPeriodo('1A')" class="px-2 py-1 rounded {{ $filtroPeriodo === '1A' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">1A</button>
            <button wire:click="setFiltroPeriodo('Tudo')" class="px-2 py-1 rounded {{ $filtroPeriodo === 'Tudo' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Tudo</button>
        </div>

        {{-- Tabela para desktop --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-xl overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Serviço</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                        
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($appointments as $index=>$servico)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($servico->appointment_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach(array_filter(preg_split('/\s*[,\/]+\s*/', $servico->services)) as $item)
                                    <div>{{ trim($item) }}</div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ collect(explode(' ', $servico->customer->name))->take(2)->implode(' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $servico->total}}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="py-4">{{ $appointments->links() }}</div>
        </div>

        {{-- Cards para mobile --}}
        <div class="md:hidden space-y-4">
            @foreach($appointments as $index=>$servico)
                <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">{{ \Carbon\Carbon::parse($servico->appointment_date)->format('d/m/Y') }}</span>
                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-medium">
                            @foreach(array_filter(preg_split('/\s*[,\/]+\s*/', $servico->services)) as $item)
                                <div>{{ trim($item) }}</div>
                            @endforeach
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ $servico->customer->name ? substr($servico->customer->name, 0, 2) : '-' }}
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900">
                                {{ collect(explode(' ', $servico->customer->name))->take(2)->implode(' ') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block bg-gray-100 text-gray-700 rounded px-2 py-1 text-xs">Valor: R$ {{ $servico->total }}</span>
                        <span class="inline-block rounded px-2 py-1 text-xs font-semibold
                            {{ $servico->status == 'Pendente' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $servico->status == 'Confirmado' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $servico->status == 'Realizado' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $servico->status == 'Cancelado' ? 'bg-red-100 text-red-700' : '' }}
                        ">
                            {{ $servico->status }}
                        </span>
                    </div>
                    @if($servico->status == 'Cancelado')
                        <div class="text-xs text-red-600 bg-red-50 rounded p-2">Razão do cancelamento: {{ $servico->cancellation_reason ?? '--' }}</div>
                    @endif
                </div>
            @endforeach
            <div class="py-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</div>
