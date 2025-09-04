<div>
    {{-- Success is as dangerous as failure. --}}
    <div class="p-6 bg-white border-b border-gray-200">
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Início</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervalo</th>
                        
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($horarios as $horario)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $horario->day_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($horario->start_time)->format('H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{\Carbon\Carbon::parse($horario->end_time)->format('H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{\Carbon\Carbon::parse($horario->lunch_start)->format('H:i') }} - {{ Carbon\Carbon::parse($horario->lunch_end)->format('H:i') }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
</div>
