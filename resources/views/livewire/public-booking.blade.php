<div>
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach(['Filial', 'Profissional', 'Serviços', 'Data/Hora', 'Seus Dados', 'Confirmação'] as $index => $stepName)
                <div class="flex items-center {{ $index + 1 < 6 ? 'flex-1' : '' }}">
                    <div class="flex items-center flex-col">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $step > $index + 1 ? 'bg-green-500' : ($step == $index + 1 ? 'bg-blue-600' : 'bg-gray-300') }} text-white font-semibold">
                            @if($step > $index + 1)
                                ✓
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="text-xs mt-1 {{ $step == $index + 1 ? 'font-semibold' : 'text-gray-500' }}">{{ $stepName }}</span>
                    </div>
                    @if($index + 1 < 6)
                        <div class="flex-1 h-1 {{ $step > $index + 1 ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Step 1: Escolher Filial -->
    @if($step == 1)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha a Filial</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($branches as $branch)
                    <button wire:click="selectBranch({{ $branch->id }})"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg mb-2 hover:bg-blue-700 transition w-full">
                        {{ $branch->branch_name }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Step 2: Escolher Profissional -->
    @if($step == 2)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha o Profissional</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($employees as $employee)
                    <button wire:click="selectEmployee({{ $employee->id }})" 
                            class="p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-lg transition flex flex-col items-center">
                        @if($employee->photo)
                            <img src="{{ tenant_asset($employee->photo) }}" alt="{{ $employee->name }}" 
                                 class="w-20 h-20 rounded-full object-cover mb-3">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-2xl text-white mb-3">
                                {{ substr($employee->name, 0, 1) }}
                            </div>
                        @endif
                        <h3 class="font-bold text-center">{{ $employee->name }}</h3>
                    </button>
                @endforeach
            </div>
            <button wire:click="back" class="mt-6 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                ← Voltar
            </button>
        </div>
    @endif

    <!-- Step 3: Escolher Serviços -->
    @if($step == 3)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha os Serviços</h2>
            <div class="space-y-3">
                @foreach($services as $service)
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer
                                  {{ in_array($service->id, $selectedServices) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="checkbox" wire:click="toggleService({{ $service->id }})" 
                               {{ in_array($service->id, $selectedServices) ? 'checked' : '' }}
                               class="mr-4 h-5 w-5">
                        <div class="flex-1">
                            <div class="font-semibold">{{ $service->name }}</div>
                            <div class="text-sm text-gray-600">{{ $service->duration }} min • R$ {{ number_format($service->price, 2, ',', '.') }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('selectedServices') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <div class="flex gap-4 mt-6">
                <button wire:click="back" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Voltar
                </button>
                <button wire:click="confirmServices" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Continuar →
                </button>
            </div>
        </div>
    @endif

    <!-- Step 4: Escolher Data e Hora -->
    @if($step == 4)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha Data e Horário</h2>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                  <input type="date" wire:change="selectDate($event.target.value)" min="{{ date('Y-m-d') }}"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            @if($selectedDate && count($availableTimes) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horários Disponíveis</label>
                    <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach($availableTimes as $time)
                            <button wire:click="selectTime('{{ $time }}')" 
                                    class="p-3 border-2 border-gray-200 rounded hover:border-blue-500 hover:bg-blue-50 transition">
                                {{ $time }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @elseif($selectedDate)
                <p class="text-red-500">Não há horários disponíveis para esta data.</p>
            @endif

            <button wire:click="back" class="mt-6 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                ← Voltar
            </button>
        </div>
    @endif

    <!-- Step 5: Dados do Cliente -->
    @if($step == 5)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Seus Dados</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" wire:model="customerName" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('customerName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" wire:model="customerEmail" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('customerEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                    <input type="tel" wire:model="customerPhone" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('customerPhone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="customerWhatsapp" class="mr-2 h-4 w-4">
                    <label class="text-sm text-gray-700">Este número é WhatsApp</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea wire:model="observation" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button wire:click="back" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Voltar
                </button>
                <button wire:click="confirmBooking" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Confirmar Agendamento
                </button>
            </div>
        </div>
    @endif

    <!-- Step 6: Confirmação -->
    @if($step == 6)
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-6xl mb-4">✅</div>
            <h2 class="text-3xl font-bold mb-4 text-green-600">Agendamento Confirmado!</h2>
            <p class="text-gray-600 mb-6">Seu agendamento foi realizado com sucesso.</p>
            <div class="bg-gray-50 p-6 rounded-lg mb-6 text-left">
                <h3 class="font-bold mb-3">Detalhes do Agendamento:</h3>
                <p><strong>Data:</strong> {{ date('d/m/Y', strtotime($selectedDate)) }}</p>
                <p><strong>Horário:</strong> {{ $selectedTime }}</p>
                <p><strong>Cliente:</strong> {{ $customerName }}</p>
            </div>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-4">
                <p class="text-yellow-800 font-semibold mb-1">Atenção!</p>
                <p class="text-yellow-700 text-sm">
                    Sua senha de acesso ao sistema foi definida como os <strong>últimos quatro dígitos do telefone informado</strong>.<br>
                    <strong>Sua senha:</strong> {{ substr(preg_replace('/\D/', '', $customerPhone), -4) }}<br>
                    Você poderá alterá-la depois de acessar o sistema.
                </p>
            </div>
            <p class="text-sm text-gray-500">Enviamos um e-mail de confirmação para {{ $customerEmail }}</p>
            <a href="/agendar" class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Fazer Novo Agendamento
            </a>
        </div>
    @endif
</div>
