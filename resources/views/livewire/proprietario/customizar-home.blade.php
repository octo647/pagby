<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
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

                <form wire:submit.prevent="salvarCustomizacoes">
                
                <!-- Menu de Navegação em Grid 2x4 -->
                <div class="mb-6">
                    <nav class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'cores')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'cores' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">🎨</span>
                                <span>Cores</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'hero')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'hero' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">🚀</span>
                                <span>Hero</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'logo')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'logo' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">🏷️</span>
                                <span>Logo</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'servicos')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'servicos' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">✂️</span>
                                <span>Serviços</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'sobre')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'sobre' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">📖</span>
                                <span>Sobre</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'galeria')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'galeria' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">🖼️</span>
                                <span>Galeria</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'ambiente')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'ambiente' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">🏢</span>
                                <span>Ambiente</span>
                            </span>
                        </button>
                        
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'equipe')"
                            class="px-4 py-3 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'equipe' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">👥</span>
                                <span>Equipe</span>
                            </span>
                        </button>
                    </nav>
                    
                    <!-- Botão de Visualização Separado -->
                    <div class="flex justify-center">
                        <a 
                            href="/" 
                            target="_blank" 
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition-colors shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Visualizar Página Inicial
                        </a>
                    </div>
                </div>

                <!-- Conteúdo Principal -->
                <div>
                    
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

                    @if($activeTab === 'logo')
                        <!-- ABA: Logo -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo do Salão</h3>
                            
                            <!-- Upload da Logo -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Logo
                                </label>
                                
                                <p class="text-sm text-gray-600 mb-4">
                                    A logo aparecerá no cabeçalho da página inicial e será usada como favicon.
                                </p>
                                
                                @if($logoImagemAtual)
                                    <!-- Preview da logo atual -->
                                    <div class="mb-4">
                                        <div class="relative inline-block">
                                            <img 
                                                src="/tenants/{{ tenant('id') }}/{{ $logoImagemAtual }}" 
                                                alt="Logo" 
                                                class="h-24 w-auto rounded-lg border-2 border-gray-300 object-contain bg-white p-2"
                                            >
                                            <button 
                                                type="button"
                                                wire:click="removerLogo"
                                                wire:confirm="Tem certeza que deseja remover a logo?"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">Logo atual</p>
                                    </div>
                                @endif

                                <!-- Upload de nova logo -->
                                <div>
                                    <label class="block">
                                        <span class="sr-only">Escolher arquivo</span>
                                        <div class="flex items-center gap-3">
                                            <label for="logoImagem" class="cursor-pointer">
                                                <div class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $logoImagemAtual ? 'Trocar Logo' : 'Enviar Logo' }}
                                                </div>
                                                <input 
                                                    type="file" 
                                                    id="logoImagem"
                                                    wire:model="logoImagem"
                                                    accept="image/*"
                                                    class="hidden"
                                                >
                                            </label>
                                            
                                            @if($logoImagem)
                                                <span class="text-sm text-green-600 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Nova logo selecionada
                                                </span>
                                            @endif
                                        </div>
                                    </label>
                                    
                                    <p class="text-xs text-gray-500 mt-2">
                                        Recomendado: imagem quadrada transparente (PNG) de pelo menos 512x512 pixels
                                    </p>
                                    
                                    @error('logoImagem')
                                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                    
                                    <div wire:loading wire:target="logoImagem" class="mt-3">
                                        <div class="flex items-center gap-2 text-sm text-blue-600">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Carregando logo...
                                        </div>
                                    </div>
                                    
                                    @if($logoImagem && !$errors->has('logoImagem'))
                                        <div class="mt-4 p-3 bg-blue-50 border-2 border-blue-300 rounded-lg">
                                            <p class="text-sm text-blue-700 font-medium flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Logo selecionada!
                                            </p>
                                            <p class="text-xs text-blue-600 mt-1">
                                                ⚠️ Role até o final e clique em "Salvar Customizações" para aplicar
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'servicos')
                        <!-- ABA: Serviços -->
                        <div class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Serviços Exibidos na Home</h3>
                                <p class="text-sm text-gray-600">
                                    Selecione até {{ $maxServicos }} serviços para exibir
                                </p>
                            </div>

                            @if(count($servicosDisponiveis) === 0)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-yellow-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-yellow-800 font-medium mb-2">Nenhum serviço cadastrado</p>
                                    <p class="text-yellow-700 text-sm">Cadastre serviços primeiro para exibi-los na home.</p>
                                    <a href="{{ route('tenant.dashboard', ['tabelaAtiva' => 'servicos', 'menu' => 'proprietario']) }}" class="mt-4 inline-block px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                        Cadastrar Serviços
                                    </a>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($servicosDisponiveis as $servico)
                                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all {{ in_array($servico['id'], $servicosSelecionados) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="servicosSelecionados"
                                                value="{{ $servico['id'] }}"
                                                {{ count($servicosSelecionados) >= $maxServicos && !in_array($servico['id'], $servicosSelecionados) ? 'disabled' : '' }}
                                                class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-medium text-gray-900">{{ $servico['service'] }}</span>
                                                    <span class="text-sm font-semibold text-blue-600">R$ {{ number_format($servico['price'], 2, ',', '.') }}</span>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">{{ $servico['time'] }} min</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-700">
                                        <strong>{{ count($servicosSelecionados) }}/{{ $maxServicos }}</strong> serviços selecionados
                                    </p>
                                    @if(count($servicosSelecionados) >= $maxServicos)
                                        <p class="text-xs text-blue-600 mt-1">
                                            Limite atingido. Desmarque um serviço para selecionar outro.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($activeTab === 'sobre')
                        <!-- ABA: Sobre -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Seção Sobre Nós</h3>
                            
                            <p class="text-sm text-gray-600">
                                Customize o conteúdo da seção "Sobre" que aparece na sua página inicial.
                            </p>

                            <!-- Título da seção Sobre -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Título da Seção
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.live="sobreTitulo"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                    placeholder="Ex: Sobre Nós, Nossa História, Conheça-nos"
                                >
                                <p class="text-xs text-gray-500 mt-1">Deixe vazio para usar "{{ tenant()->fantasy_name }}"</p>
                            </div>

                            <!-- Imagem da seção Sobre -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Imagem da Seção
                                </label>
                                
                                @if($sobreImagemAtual)
                                    <div class="mb-4">
                                        <div class="relative inline-block">
                                            <img 
                                                src="/tenants/{{ tenant('id') }}/sobre/{{ $sobreImagemAtual }}" 
                                                alt="Imagem Sobre" 
                                                class="h-32 w-auto rounded-lg border-2 border-gray-300 object-cover"
                                            >
                                            <button 
                                                type="button"
                                                wire:click="removerSobreImagem"
                                                wire:confirm="Remover esta imagem?"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg"
                                            >
                                                ×
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">Imagem atual</p>
                                    </div>
                                @endif

                                <label for="sobreImagem" class="cursor-pointer inline-block">
                                    <div class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $sobreImagemAtual ? 'Trocar Imagem' : 'Enviar Imagem' }}
                                    </div>
                                    <input 
                                        type="file" 
                                        id="sobreImagem"
                                        wire:model="sobreImagem"
                                        accept="image/*"
                                        class="hidden"
                                    >
                                </label>
                                
                                @if($sobreImagem)
                                    <span class="text-sm text-green-600 ml-3">✓ Nova imagem selecionada</span>
                                @endif
                                
                                <p class="text-xs text-gray-500 mt-2">Sugestão: foto da fachada ou do ambiente</p>
                            </div>

                            <!-- Parágrafos de texto -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Texto da Seção (até 3 parágrafos)
                                </label>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Parágrafo 1</label>
                                    <textarea 
                                        wire:model.live="sobreParagrafo1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                        rows="2"
                                        placeholder="Ex: Somos uma empresa dedicada a oferecer os melhores serviços..."
                                    ></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Parágrafo 2</label>
                                    <textarea 
                                        wire:model.live="sobreParagrafo2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                        rows="2"
                                        placeholder="Ex: Nossa equipe é formada por profissionais qualificados..."
                                    ></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Parágrafo 3 (opcional)</label>
                                    <textarea 
                                        wire:model.live="sobreParagrafo3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                        rows="2"
                                        placeholder="Ex: Venha nos conhecer e experimente um atendimento diferenciado!"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'galeria')
                        <!-- ABA: Galeria -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Galeria de Trabalhos</h3>
                            
                            <p class="text-sm text-gray-600">
                                Adicione até 4 imagens dos seus melhores trabalhos para exibir na galeria da página inicial.
                            </p>

                            <!-- Grid de upload de imagens -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Imagem {{ $i + 1 }}
                                        </label>
                                        
                                        @if(isset($galeriaImagensAtuais[$i]) && $galeriaImagensAtuais[$i])
                                            <div class="mb-4">
                                                <div class="relative inline-block">
                                                    <img 
                                                        src="/tenants/{{ tenant('id') }}/galeria/{{ $galeriaImagensAtuais[$i] }}" 
                                                        alt="Galeria {{ $i + 1 }}" 
                                                        class="h-40 w-full rounded-lg border-2 border-gray-300 object-cover"
                                                    >
                                                    <button 
                                                        type="button"
                                                        wire:click="removerGaleriaImagem({{ $i }})"
                                                        wire:confirm="Remover esta imagem?"
                                                        class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg"
                                                    >
                                                        ×
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <label for="galeriaImagem{{ $i }}" class="cursor-pointer inline-block">
                                            <div class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ isset($galeriaImagensAtuais[$i]) && $galeriaImagensAtuais[$i] ? 'Trocar' : 'Enviar' }}
                                            </div>
                                            <input 
                                                type="file" 
                                                id="galeriaImagem{{ $i }}"
                                                wire:model="galeriaImagens.{{ $i }}"
                                                accept="image/*"
                                                class="hidden"
                                            >
                                        </label>
                                        
                                        <div wire:loading wire:target="galeriaImagens.{{ $i }}" class="text-sm text-blue-600 mt-2">
                                            Carregando...
                                        </div>
                                        
                                        @if(isset($galeriaImagens[$i]) && $galeriaImagens[$i])
                                            <span class="text-sm text-green-600 ml-3">✓ Nova imagem</span>
                                        @endif
                                    </div>
                                @endfor
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-800">
                                    <strong>💡 Dica:</strong> Use imagens de boa qualidade dos seus melhores trabalhos. 
                                    Fotos de antes/depois, cortes diferentes ou acabamentos especiais chamam mais atenção!
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'ambiente')
                        <!-- ABA: Ambiente -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nosso Ambiente</h3>
                            
                            <p class="text-sm text-gray-600">
                                Mostre as diferentes áreas do seu estabelecimento. Adicione até 3 imagens com legendas personalizadas.
                            </p>

                            <!-- Grid de upload de imagens com legendas -->
                            <div class="grid grid-cols-1 gap-6">
                                @for($i = 0; $i < 3; $i++)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Imagem -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                                    Imagem {{ $i + 1 }}
                                                </label>
                                                
                                                @if(isset($ambienteImagensAtuais[$i]) && $ambienteImagensAtuais[$i])
                                                    <div class="mb-4">
                                                        <div class="relative inline-block w-full">
                                                            <img 
                                                                src="/tenants/{{ tenant('id') }}/ambiente/{{ $ambienteImagensAtuais[$i] }}" 
                                                                alt="Ambiente {{ $i + 1 }}" 
                                                                class="h-40 w-full rounded-lg border-2 border-gray-300 object-cover"
                                                            >
                                                            <button 
                                                                type="button"
                                                                wire:click="removerAmbienteImagem({{ $i }})"
                                                                wire:confirm="Remover esta imagem?"
                                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg"
                                                            >
                                                                ×
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif

                                                <label for="ambienteImagem{{ $i }}" class="cursor-pointer inline-block">
                                                    <div class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ isset($ambienteImagensAtuais[$i]) && $ambienteImagensAtuais[$i] ? 'Trocar' : 'Enviar' }}
                                                    </div>
                                                    <input 
                                                        type="file" 
                                                        id="ambienteImagem{{ $i }}"
                                                        wire:model="ambienteImagens.{{ $i }}"
                                                        accept="image/*"
                                                        class="hidden"
                                                    >
                                                </label>
                                                
                                                <div wire:loading wire:target="ambienteImagens.{{ $i }}" class="text-sm text-blue-600 mt-2">
                                                    Carregando...
                                                </div>
                                            </div>
                                            
                                            <!-- Legenda -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                                    Legenda da Imagem {{ $i + 1 }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    wire:model="ambienteLegendas.{{ $i }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                                    placeholder="Ex: Recepção, Área de Atendimento, Área VIP"
                                                >
                                                <p class="text-xs text-gray-500 mt-2">
                                                    Esta legenda aparecerá sobre a imagem
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-800">
                                    <strong>💡 Dica:</strong> Use fotos que mostrem os diferentes espaços do seu estabelecimento: 
                                    recepção, áreas de atendimento, espaços VIP, etc.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'equipe')
                        <!-- ABA: Equipe -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nossa Equipe</h3>
                            
                            <p class="text-sm text-gray-600">
                                Selecione os profissionais que você deseja destacar na página inicial.
                            </p>

                            @if(empty($equipeFuncionariosDisponiveis))
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-yellow-800 font-medium mb-2">Nenhum funcionário cadastrado</p>
                                    <p class="text-yellow-700 text-sm mb-4">
                                        Cadastre funcionários no sistema para poder exibi-los na página inicial.
                                    </p>
                                    <a 
                                        href="{{ route('tenant.users.index') }}"
                                        class="inline-block px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm font-medium transition-colors"
                                    >
                                        Gerenciar Usuários
                                    </a>
                                </div>
                            @else
                                <!-- Grid de seleção de funcionários -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($equipeFuncionariosDisponiveis as $funcionario)
                                        <div class="bg-gray-50 border-2 rounded-lg p-4 transition-all {{ in_array($funcionario['id'], $equipeFuncionarios) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                            <label class="cursor-pointer flex items-start gap-3">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model.live="equipeFuncionarios"
                                                    value="{{ $funcionario['id'] }}"
                                                    class="mt-1 h-4 w-4 text-blue-600 rounded"
                                                >
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        @if($funcionario['photo'])
                                                            <img 
                                                                src="{{ $funcionario['photo'] }}" 
                                                                alt="{{ $funcionario['name'] }}"
                                                                class="h-12 w-12 rounded-full object-cover border-2 border-gray-300"
                                                            >
                                                        @else
                                                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-800">{{ $funcionario['name'] }}</div>
                                                            <div class="text-sm text-gray-600">{{ $funcionario['role'] }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-blue-800">
                                        <strong>Selecionados:</strong> {{ count($equipeFuncionarios) }} funcionário(s)
                                    </p>
                                </div>
                            @endif

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-800">
                                    <strong>💡 Dica:</strong> Os funcionários selecionados aparecerão com sua foto, nome e cargo na seção "Nossa Equipe" da página inicial.
                                </p>
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
                            type="button"
                            wire:click="salvarCustomizacoes"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                            </svg>
                            <span>Salvar Customizações</span>
                            <span wire:loading wire:target="salvarCustomizacoes" class="ml-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>
