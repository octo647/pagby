{{-- resources/views/plans/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edição do Plano {{ $plan->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            @include('includes.messages')

            <form method="POST" action="{{ route('plans.update', $plan) }}"
                x-data='{
                    openServices: false,
                    openAdicionais: false,
                    selectedServices: @json(old("services", $plan->services ?? [])),
                    
                    selectedAdicionais: @json(
                    collect(old("additional_services", $plan->additional_services ?? []))->map(function($s) {
                        return is_array($s) ? $s : ["id" => $s, "desconto" => ""];
                    })->all()
                    ),
                    
                    allServices: @json($services->map(fn($s) => ["id" => $s->id, "name" => $s->service])),
                    descontos: {},
                    init() {
                        for (const s of this.selectedAdicionais) {
                            this.descontos[s.id] = s.desconto ?? "";
                        }
                    }
                }'
                
                x-init="init()"
                
            >



  
                @csrf
                @method('PUT')

                

                <!-- Campos do plano -->
                <div class="mb-4">
                    <label class="block font-bold">Nome do Plano</label>
                    <input type="text" name="name" class="border rounded w-full" required value="{{ old('name', $plan->name) }}">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Mensalidade</label>
                    <input type="number" step="0.01" name="price" class="border rounded w-full" required value="{{ old('price', $plan->price) }}">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Duração (dias)</label>
                    <input type="number" name="duration_days" class="border rounded w-full" required value="{{ old('duration_days', $plan->duration_days) }}">
                </div>

                <!-- Campo para seleção de serviços -->
                <div class="mb-4">
                    <button type="button" @click="openServices = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
                        Selecionar Serviços
                    </button>
                    <div class="mb-2">
                        <span class="font-bold">Serviços selecionados:</span>
                        <span x-text="selectedServices.join(', ')"></span>
                    </div>
                    <input type="hidden" name="services" :value="JSON.stringify(selectedServices)">
                </div>
                <!-- Modal de seleção de serviços -->
                <div x-show="openServices" x-cloak wire:ignore class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded shadow w-96">
                        <h3 class="text-lg font-bold mb-2">Selecione os Serviços</h3>
                        <div class="max-h-60 overflow-y-auto">
                            @foreach($services as $service)
                                <label class="flex items-center mb-1">
                                    <input type="checkbox"
                                        :value="{{ $service->id }}"
                                        x-model="selectedServices"
                                    >
                                    <span class="ml-2">{{ $service->service }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" @click="openServices = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>
                </div>
                <!-- Fim do campo para serviços -->

                <!-- Bloco de serviços adicionais -->
                <div class="mb-4">
    <button type="button" @click="openAdicionais = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
        Selecionar Serviços Adicionais
    </button>

    <div class="mb-2">
    <span class="font-bold">Serviços adicionais selecionados:</span>
    <template x-for="s in selectedAdicionais" :key="s.id">
    <span class="inline-block bg-gray-200 rounded px-2 py-1 mx-1"
          x-text="allServices.find(as => as.id == s.id)?.name + (descontos[s.id] ? ' ('+descontos[s.id]+'%)' : '')"></span>
    </template>
    </div>
        <!-- Este input deve ser realmente oculto -->
        <input type="hidden" name="additional_services" :value="JSON.stringify(selectedAdicionais.map(s => ({id: s.id, desconto: descontos[s.id] || ''})))">
    </div>
        
    <input type="hidden" value="teste" id="teste-hidden">
    <!-- Modal de serviços adicionais -->
    <div x-show="openAdicionais" x-cloak wire:ignore class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-96">
        <h3 class="text-lg font-bold mb-2">Selecione os Serviços Adicionais</h3>
        <div class="max-h-60 overflow-y-auto">
            <template x-for="service in allServices" :key="service.id">
                <div class="flex items-center mb-2">
                    <input type="checkbox"
                        :value="service.id"
                        :id="'addserv-'+service.id"
                        @change="
                            if($event.target.checked) {
                                if (!selectedAdicionais.some(s => s.id == service.id)) {
                                    selectedAdicionais.push({id: service.id, desconto:descontos[service.id] || ''});
                                }
                            } else {
                                selectedAdicionais = selectedAdicionais.filter(s => s.id != service.id);
                                descontos[service.id] = '';
                            }
                        "
                        :checked="selectedAdicionais.some(s => s.id == service.id)"
                    >
                    <label class="ml-2" :for="'addserv-'+service.id" x-text="service.name"></label>
                    <input type="number" min="0" max="100" step="1"
                        class="ml-4 border rounded w-20"
                        placeholder="Desconto %"
                        x-model="descontos[service.id]"
                        :disabled="!selectedAdicionais.some(s => s.id == service.id)"
                    >
                </div>
            </template>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" @click="openAdicionais = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
        </div>
    </div>
</div>
                <!-- Fim do bloco de serviços adicionais -->

                <!-- Outros campos -->
                <div class="mb-4">
                    <label class="block font-bold">Recursos (features, JSON)</label>
                    <input type="text" name="features" class="border rounded w-full" value="{{ old('features', json_encode($plan->features ?? [])) }}">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Ativo?</label>
                    <select name="active" class="border rounded w-full">
                        <option value="1" {{ $plan->active ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ !$plan->active ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Filial (branch_id)</label>
                    <input type="number" name="branch_id" class="border rounded w-full" required value="{{ old('branch_id', $plan->branch_id) }}">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
                
            </form>
        </div>
    </div>
</x-app-layout>