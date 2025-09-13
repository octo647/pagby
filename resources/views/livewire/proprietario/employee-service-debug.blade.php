<div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="mb-4">
            <!-- Debug info -->
            <p>Selected Employee ID: {{ $selectedEmployeeId ?? 'null' }}</p>
            <p>Employees count: {{ count($employees) }}</p>
        </div>

        @if($selectedEmployeeId)
            @php
                $employee = collect($employees)->firstWhere('id', $selectedEmployeeId);
            @endphp
            
            @if($employee)
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                    Serviços do Funcionário: {{ $employee['name'] }}
                </h2>
                
                <table class="tabela-escura w-full">
                    <caption class="caption-top text-lg font-semibold text-gray-900 text-center mb-4">
                        <img src="{{ tenant_asset($employee['photo']) }}" alt="{{ $employee['name'] }}" class="w-24 h-24 rounded-full mx-auto">
                        {{ $employee['name'] }}
                    </caption>
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Executa?</th>
                            <th>Debug</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>{{ $service['service'] }}</td>
                                <td>
                                    <input type="checkbox"
                                        wire:click="changeService({{ $service['id'] }}, {{ $employee['id'] }})"
                                        {{ in_array($service['id'], $employee['services']) ? 'checked' : '' }}>
                                </td>
                                <td>
                                    Service ID: {{ $service['id'] }}<br>
                                    In array: {{ in_array($service['id'], $employee['services']) ? 'YES' : 'NO' }}<br>
                                    Services array: {{ implode(',', $employee['services']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Funcionário não encontrado</p>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Selecione um funcionário</p>
            </div>
        @endif
    </div>
</div>