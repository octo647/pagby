<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Cabeçalho -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 inline-block mr-2 text-blue-600">
                    <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                    <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                </svg>
                Customizar Home
            </h1>
            <p class="text-gray-600">Personalize a aparência da sua página inicial</p>
        </div>

        @if(!$canEdit)
            <!-- Aviso: Template não editável -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mr-3 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                            Template Não Editável
                        </h3>
                        <p class="text-yellow-700 mb-3">
                            Sua página inicial usa o template: <strong>{{ $templateType }}</strong>
                        </p>
                        <div class="bg-white rounded-md p-4 mt-3">
                            <p class="text-gray-700 mb-2">
                                <strong>Por que não posso editar?</strong>
                            </p>
                            <p class="text-gray-600 text-sm mb-3">
                                Templates específicos (Clean, Moderna, etc.) são compartilhados entre múltiplos tenants 
                                e mantém um design profissional consistente. Por isso, não podem ser editados individualmente.
                            </p>
                            <p class="text-gray-700 mb-2">
                                <strong>Como posso customizar minha home?</strong>
                            </p>
                            <p class="text-gray-600 text-sm">
                                Para ter controle total sobre a customização, entre em contato com o suporte 
                                para migrar sua home para o <strong>Template Padrao</strong>, que pode ser 
                                editado livremente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Interface de Edição -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                
                <!-- Mensagens de Feedback -->
                @if (session()->has('message'))
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-green-800">{{ session('message') }}</p>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Info Template -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-blue-800">
                            <strong>Template Editável:</strong> {{ $templateType }}
                        </span>
                    </div>
                </div>

                <!-- Abas -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <button 
                            wire:click="$set('activeTab', 'cores')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'cores' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            🎨 Cores
                        </button>
                        <button 
                            wire:click="$set('activeTab', 'hero')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'hero' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            🚀 Seção Hero
                        </button>
                        <button 
                            wire:click="$set('activeTab', 'preview')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'preview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            👁️ Preview
                        </button>
                    </nav>
                </div>

                <!-- Conteúdo das Abas -->
                <form wire:submit.prevent="salvarCustomizacoes">
                    
                    @if($activeTab === 'cores')
                        <!-- ABA: Cores -->
                        <div class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Paleta de Cores</h3>
                                <button 
                                    type="button"
                                    wire:click="restaurarCoresPadrao"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors flex items-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    Restaurar Padrão
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Cor Primária -->
                                <div class="text-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Cor Primária
                                    </label>
                                    <div class="flex justify-center">
                                        <input 
                                            type="color" 
                                            wire:model.live="corPrimaria"
                                            class="h-20 w-20 cursor-pointer rounded-lg border-2 border-gray-300 shadow-sm"
                                        >
                                    </div>
                                    <p class="text-xs text-gray-500 mt-3">Usada em títulos e menus</p>
                                </div>

                                <!-- Cor Secundária -->
                                <div class="text-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Cor Secundária
                                    </label>
                                    <div class="flex justify-center">
                                        <input 
                                            type="color" 
                                            wire:model.live="corSecundaria"
                                            class="h-20 w-20 cursor-pointer rounded-lg border-2 border-gray-300 shadow-sm"
                                        >
                                    </div>
                                    <p class="text-xs text-gray-500 mt-3">Usada em botões e links</p>
                                </div>

                                <!-- Cor Destaque -->
                                <div class="text-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Cor de Destaque
                                    </label>
                                    <div class="flex justify-center">
                                        <input 
                                            type="color" 
                                            wire:model.live="corDestaque"
                                            class="h-20 w-20 cursor-pointer rounded-lg border-2 border-gray-300 shadow-sm"
                                        >
                                    </div>
                                    <p class="text-xs text-gray-500 mt-3">Usada para elementos de ênfase</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'hero')
                        <!-- ABA: Hero -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Seção Hero (Topo da Página)</h3>
                            
                            <!-- Título Principal -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Título Principal
                                </label>
                                
                                <div class="space-y-3">
                                    <!-- Opção 1: Nome do Salão -->
                                    <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all {{ $heroTituloTipo === 'nome_salao' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input 
                                            type="radio" 
                                            wire:model.live="heroTituloTipo"
                                            value="nome_salao"
                                            class="mt-1 mr-3"
                                        >
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                Nome do Salão
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Exibe: <strong>{{ tenant()->fantasy_name ?? 'Nome do seu salão' }}</strong>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                ✓ Atualiza automaticamente se você mudar o nome do salão
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Opção 2: Texto Personalizado -->
                                    <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all {{ $heroTituloTipo === 'personalizado' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input 
                                            type="radio" 
                                            wire:model.live="heroTituloTipo"
                                            value="personalizado"
                                            class="mt-1 mr-3"
                                        >
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                Texto Personalizado
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Escreva o que quiser
                                            </div>
                                        </div>
                                    </label>

                                    @if($heroTituloTipo === 'personalizado')
                                        <div class="ml-8 mt-2">
                                            <input 
                                                type="text" 
                                                wire:model.live="heroTituloPersonalizado"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg"
                                                placeholder="Ex: Bem-vindo à melhor experiência!"
                                            >
                                            <p class="text-xs text-gray-500 mt-1">Digite o título que deseja exibir</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Subtítulo -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Subtítulo / Descrição
                                </label>
                                
                                <div class="space-y-3">
                                    <!-- Opção 1: Endereço do Salão -->
                                    <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all {{ $heroSubtituloTipo === 'endereco_salao' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input 
                                            type="radio" 
                                            wire:model.live="heroSubtituloTipo"
                                            value="endereco_salao"
                                            class="mt-1 mr-3"
                                        >
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                Endereço do Salão
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Exibe: <strong>{{ tenant()->address ?? 'Seu endereço' }}{{ tenant()->number ? ', ' . tenant()->number : '' }}</strong>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                ✓ Atualiza automaticamente se você mudar o endereço do salão
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Opção 2: Texto Personalizado -->
                                    <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all {{ $heroSubtituloTipo === 'personalizado' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input 
                                            type="radio" 
                                            wire:model.live="heroSubtituloTipo"
                                            value="personalizado"
                                            class="mt-1 mr-3"
                                        >
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                Texto Personalizado
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Escreva o que quiser
                                            </div>
                                        </div>
                                    </label>

                                    @if($heroSubtituloTipo === 'personalizado')
                                        <div class="ml-8 mt-2">
                                            <textarea 
                                                wire:model.live="heroSubtituloPersonalizado"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-md"
                                                rows="3"
                                                placeholder="Ex: Oferecemos os melhores serviços com qualidade e dedicação"
                                            ></textarea>
                                            <p class="text-xs text-gray-500 mt-1">Digite a descrição que deseja exibir</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Imagem do Hero -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Imagem de Fundo
                                </label>
                                
                                @if($heroImagemAtual)
                                    <!-- Preview da imagem atual -->
                                    <div class="mb-4">
                                        <div class="relative inline-block">
                                            <img 
                                                src="/tenants/{{ tenant('id') }}/hero/{{ $heroImagemAtual }}" 
                                                alt="Imagem Hero" 
                                                class="h-32 w-auto rounded-lg border-2 border-gray-300 object-cover"
                                            >
                                            <button 
                                                type="button"
                                                wire:click="removerImagemHero"
                                                wire:confirm="Tem certeza que deseja remover esta imagem?"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">Imagem atual</p>
                                    </div>
                                @endif

                                <!-- Upload de nova imagem -->
                                <div>
                                    <label class="block">
                                        <span class="sr-only">Escolher arquivo</span>
                                        <div class="flex items-center gap-3">
                                            <label for="heroImagem" class="cursor-pointer">
                                                <div class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $heroImagemAtual ? 'Trocar Imagem' : 'Enviar Imagem' }}
                                                </div>
                                                <input 
                                                    type="file" 
                                                    id="heroImagem"
                                                    wire:model="heroImagem"
                                                    accept="image/*"
                                                    class="hidden"
                                                >
                                            </label>
                                            
                                            @if($heroImagem)
                                                <span class="text-sm text-green-600 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Nova imagem selecionada
                                                </span>
                                            @endif
                                        </div>
                                    </label>
                                    
                                    <p class="text-xs text-gray-500 mt-2">
                                        Recomendado: imagem horizontal de pelo menos 1920x1080 pixels (JPG, PNG ou WEBP)
                                    </p>
                                    
                                    @error('heroImagem')
                                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                    
                                    <div wire:loading wire:target="heroImagem" class="mt-3">
                                        <div class="flex items-center gap-2 text-sm text-blue-600">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Carregando imagem...
                                        </div>
                                    </div>
                                    
                                    <!-- Mensagem de confirmação quando imagem é selecionada -->
                                    @if($heroImagem && !$errors->has('heroImagem'))
                                        <div class="mt-4 p-3 bg-blue-50 border-2 border-blue-300 rounded-lg">
                                            <p class="text-sm text-blue-700 font-medium flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Imagem selecionada!
                                            </p>
                                            <p class="text-xs text-blue-600 mt-1">
                                                ⚠️ Role até o final e clique em "Salvar Customizações" para aplicar
                                            </p>
                                        </div>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'preview')
                        <!-- ABA: Preview -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Preview das Customizações</h3>
                            
                            <div class="border-2 border-gray-200 rounded-lg p-6" style="background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);">
                                <div class="text-white text-center">
                                    <h1 class="text-4xl font-bold mb-4">
                                        @if($heroTituloTipo === 'nome_salao')
                                            {{ tenant()->fantasy_name ?? 'Nome do Salão' }}
                                        @else
                                            {{ $heroTituloPersonalizado ?: 'Seu Título Personalizado' }}
                                        @endif
                                    </h1>
                                    <p class="text-lg opacity-90 mb-6">
                                        @if($heroSubtituloTipo === 'endereco_salao')
                                            {{ tenant()->address ?? 'Endereço do Salão' }}{{ tenant()->number ? ', ' . tenant()->number : '' }}
                                        @else
                                            {{ $heroSubtituloPersonalizado ?: 'Seu subtítulo personalizado' }}
                                        @endif
                                    </p>
                                    <button style="background-color: {{ $corDestaque }};" class="px-6 py-3 rounded-md text-white font-semibold">
                                        Botão de Exemplo
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-2">
                                    <strong>Dica:</strong> Clique no botão abaixo para visualizar sua página real após salvar as alterações.
                                </p>
                                <a href="/" target="_blank" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    Abrir Página Inicial em Nova Aba
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Botões -->
                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <button 
                            type="button"
                            onclick="window.location.reload()"
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                            </svg>
                            Salvar Customizações
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>
