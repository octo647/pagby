{{-- resources/views/plans/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Criar Novo Plano
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('plans.store') }}"
                  x-data="{ open: false, selectedServices: [] }">
                @csrf

                <div class="mb-4">
                    <label class="block font-bold">Nome do Plano</label>
                    <input type="text" name="name" class="border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Mensalidade</label>
                    <input type="number" step="0.01" name="price" class="border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Duração (dias)</label>
                    <input type="number" name="duration_days" class="border rounded w-full" required>
                </div>
                <!-- Campo para seleção de serviços -->
                <div class="mb-4">
                    <button type="button" @click="open = true" class="bg-blue-500 text-white px-3 py-1 rounded mb-2">
                        Selecionar Serviços
                    </button>
                    <div class="mb-2">
                        <span class="font-bold">Serviços selecionados:</span>
                        <span x-text="selectedServices.join(', ')"></span>
                    </div>
                    <input type="hidden" name="services" :value="JSON.stringify(selectedServices)">
                </div>
                <!-- Modal de seleção de serviços -->
                <div x-show="open" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
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
                            <button type="button" @click="open = false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>
                </div>
                <!-- Fim do campo para serviços -->

                <div class="mb-4">
                    <label class="block font-bold">Serviços Adicionais (JSON)</label>
                    <input type="text" name="additional_services" class="border rounded w-full">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Recursos (features, JSON)</label>
                    <input type="text" name="features" class="border rounded w-full">
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Ativo?</label>
                    <select name="active" class="border rounded w-full">
                        <option value="1" selected>Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold">Filial (branch_id)</label>
                    <input type="number" name="branch_id" class="border rounded w-full" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
            </form>
        </div>
    </div>
</x-app-layout>