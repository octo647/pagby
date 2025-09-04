<div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div class="mb-4">
    
    </div>
    @if($selectedEmployeeId)
    @php
        $employee = collect($employees)->firstWhere('id', $selectedEmployeeId);
    @endphp
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">
        @if($employee)
            {{ __('Serviços do Funcionário:') }}
        @else
            {{ __('Funcionário não encontrado') }}
        @endif
    </h2>
    
    
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
                </tr>
            @endforeach
        </tbody>
    </table>
        

        
    @endif
    </div>
</div>

