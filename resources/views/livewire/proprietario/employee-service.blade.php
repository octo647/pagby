<div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div class="mb-4">
    
    </div>
    @if($selectedEmployeeId)
        @php
            $employee = collect($employees)->firstWhere('id', $selectedEmployeeId);
        @endphp
        
        @if($employee)
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ __('Serviços do Funcionário:') }}
            </h2>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">{{ __('Funcionário não encontrado') }}</p>
            </div>
        @endif
    @endif
    
    @if($selectedEmployeeId && $employee)
    
    
    <table class="tabela-escura w-full">
        <caption class="caption-top text-lg font-semibold text-gray-900 text-center mb-4">
            {{-- Exibe a foto do funcionário e o nome --}}
            {{-- Se a foto não existir, exibe uma imagem padrão ou um texto alternativo --}}
            {{-- Você pode substituir 'default-photo.jpg' por uma imagem padrão que você tenha --}}
            {{-- Certifique-se de que o caminho da imagem esteja correto --}}
            {{-- Título da tabela --}}
            <img src="{{ tenant_asset($employee['photo']) }}" alt="{{ $employee['name'] }}" class="w-24 h-24 rounded-full mx-auto">
            {{ $employee['name'] }}
        </caption>
        <thead>
            <tr>
                <th>Serviço</th>
                <th>Executa?</th>
                <th>Tempo Padrão</th>
                <th>Tempo Personalizado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
                @php
                    $serviceModel = App\Models\Service::find($service['id']);
                    $customDuration = null;
                    
                    if (in_array($service['id'], $employee['services'])) {
                        $employeeModel = App\Models\User::find($employee['id']);
                        $userService = $employeeModel->services()->where('service_id', $service['id'])->first();
                        $customDuration = $userService?->pivot?->custom_duration_minutes;
                    }
                @endphp
                <tr class="{{ $customDuration ? 'bg-blue-50' : '' }}">
                    <td>
                        {{ $service['service'] }}
                        @if($customDuration)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                Personalizado
                            </span>
                        @endif
                    </td>
                    <td>
                        <input type="checkbox"
                            wire:click="changeService({{ $service['id'] }}, {{ $employee['id'] }})"
                            {{ in_array($service['id'], $employee['services']) ? 'checked' : '' }}>
                    </td>
                    <td class="text-center">
                        <span class="px-2 py-1 bg-gray-100 rounded text-sm">
                            {{ $serviceModel?->time ?? 30 }} min
                        </span>
                    </td>
                    <td class="text-center">
                        @if(in_array($service['id'], $employee['services']))
                            <div class="flex flex-col items-center">
                                <input type="number" 
                                    min="1" 
                                    max="300"
                                    placeholder="{{ $serviceModel?->time ?? 30 }}"
                                    value="{{ $customDuration }}"
                                    wire:blur="updateCustomDuration({{ $service['id'] }}, {{ $employee['id'] }}, $event.target.value)"
                                    class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center">
                                <small class="text-gray-500 text-xs mt-1">
                                    @if($customDuration)
                                        <span class="text-blue-600 font-medium">{{ $customDuration }} min</span>
                                    @else
                                        <span class="text-gray-400">usar padrão</span>
                                    @endif
                                </small>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        

        
    @endif
    </div>
</div>

