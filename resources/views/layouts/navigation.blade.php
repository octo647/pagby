<!-- filepath: resources/views/layouts/navigation.blade.php -->
<nav x-data="{ open: false }" class="bg-gradient-to-r from-blue-100 via-white to-pink-100 border-b border-gray-200 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Hamburger sempre visível -->
            <button @click="open = ! open" class="mr-4 p-2 rounded bg-gradient-to-r from-blue-200 to-pink-200 shadow hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <!-- Logo -->
            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-9 w-auto drop-shadow" />
            </a>
            <!-- Settings Dropdown -->
            @auth
            <div class="flex items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gradient-to-r from-white to-blue-50 hover:text-pink-600 focus:outline-none transition ease-in-out duration-150 shadow">
                            @if(!empty(Auth::user()->photo))
                            @php
                                $user = Auth::user();
                                $isExternalPhoto = $user->photo && (Str::startsWith($user->photo, 'http://') || Str::startsWith($user->photo, 'https://'));
                            @endphp
                                <img src="{{$isExternalPhoto ? $user ->photo : ($user->photo ? tenant_asset(Auth::user()->photo) : '')}}"  alt="Foto de {{ Auth::user()->name }}" class="h-8 w-8 rounded-full border-2 border-blue-300 shadow"
                                @if(!$user->photo) style="display:none;" @endif>
                            @else
                                <img src="{{ global_asset('images/default-user.png') }}" title="Atualize sua foto de perfil" alt="Atualize a foto" class="h-8 w-8 rounded-full border-2 border-gray-300">
                                <div class="ml-2 text-xs text-gray-500">{{ Auth::user()->name ?? 'Visitante'}}</div> 
                            @endif
                            <div class="ml-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-dropdown-link :href="route('profile.edit')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>{{ __('Perfil') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                            <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6Zm-5.03 4.72a.75.75 0 0 0 0 1.06l1.72 1.72H2.25a.75.75 0 0 0 0 1.5h10.94l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Sair') }}
                        </x-dropdown-link>
                    </form>
                </x-dropdown>
            </div>
            @endauth
        </div>
    </div>
    <!-- Menu lateral (off-canvas) sempre disponível -->
    
    <div x-show="open" @click.self="open = false" class="fixed top-0 left-0 h-full w-72 z-50 bg-white bg-opacity-20 transition-opacity duration-300">
        <div class="bg-gradient-to-b from-blue-50 via-white to-pink-50 w-72 h-full max-h-screen overflow-y-auto shadow-2xl p-6 rounded-r-2xl border-r border-blue-200 animate-slidein">
            <button @click="open = false" class="mb-6 p-2 rounded-full bg-gradient-to-r from-pink-200 to-blue-200 shadow hover:scale-110 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="mb-8 flex flex-col items-center gap-2">
                @if(!empty(Auth::user()->photo))
                    <img src="{{tenant_asset(Auth::user()->photo)}}" alt="Foto de {{ Auth::user()->name }}" class="h-16 w-16 rounded-full border-4 border-blue-200 shadow">
                @else
                    <img src="{{ global_asset('images/default-user.png') }}" title="Atualize sua foto de perfil" alt="Atualize a foto" class="h-16 w-16 rounded-full border-4 border-gray-200">
                @endif
                <div class="font-semibold text-blue-700 text-lg">{{ Auth::user()->name ?? 'Visitante'}}</div>
                <div class="text-xs text-gray-400">{{ Auth::user()->email ?? '' }}</div>
            </div>

            {{-- Menu simplificado baseado no papel do usuário --}}
            @php
                // Determinar qual menu mostrar baseado no papel principal
                $user = Auth::user();
                $isAdmin = $user?->hasRole('Admin') ?? false;
                $isProprietario = $user?->hasRole('Proprietário') ?? false;
                $isFuncionario = $user?->hasRole('Funcionário') ?? false;
                $isCliente = $user?->hasRole('Cliente') ?? false;
                
                // Se for ambos (Proprietário e Funcionário), usar parâmetro de menu
                $menuSelecionado = request()->input('menu');
                if ($isProprietario && $isFuncionario) {
                    if (!$menuSelecionado) {
                        $menuSelecionado = 'funcionario'; // padrão
                    }
                } elseif ($isFuncionario) {
                    $menuSelecionado = 'funcionario';
                } elseif ($isProprietario) {
                    $menuSelecionado = 'proprietario';
                }
            @endphp
            
            {{-- Seletor de menu para usuários com ambos papéis --}}
            @if($isProprietario && $isFuncionario)
                @php
                    $query = request()->query();
                    $urlProprietario = url()->current() . '?' . http_build_query(array_merge($query, ['menu' => 'proprietario']));
                    $urlFuncionario = url()->current() . '?' . http_build_query(array_merge($query, ['menu' => 'funcionario']));
                @endphp
                <div class="flex justify-center gap-2 mb-6">
                    <a href="{{ route('tenant.dashboard', ['tabelaAtiva' => 'gerenciar-comandas', 'menu' => 'proprietario']) }}" class="px-3 py-1 rounded-full text-sm font-semibold border transition-all duration-150 {{ $menuSelecionado === 'proprietario' ? 'bg-blue-200 text-blue-900 border-blue-400' : 'bg-white text-gray-600 border-gray-300 hover:bg-blue-50' }}">
                        Proprietário
                    </a>
                    <a href="{{ route('tenant.dashboard', ['tabelaAtiva' => 'agenda', 'menu' => 'funcionario']) }}" class="px-3 py-1 rounded-full text-sm font-semibold border transition-all duration-150 {{ $menuSelecionado === 'funcionario' ? 'bg-pink-200 text-pink-900 border-pink-400' : 'bg-white text-gray-600 border-gray-300 hover:bg-pink-50' }}">
                        Funcionário
                    </a>
                </div>
            @endif

            {{-- MENU ADMIN --}}
            @php
                use App\Models\Plan;
                $isTenant = !in_array(request()->getHost(), config('tenancy.central_domains'));
                $temPlanoAtivo = false;
                if ($isTenant) {
                    $temPlanoAtivo = Plan::where('active', true)->exists();
                }
                $menuAdmin = [
                    [ 'label' => 'Contatos Booksy',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="8" r="4" fill="#F59E42"/><rect x="4" y="16" width="16" height="5" rx="2.5" fill="#F472B6"/></svg>',
                    'tabelaAtiva' => 'contatos-booksy'],
                    [ 'label' => 'Contatos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="8" r="4" fill="#60A5FA"/><rect x="4" y="16" width="16" height="5" rx="2.5" fill="#F472B6"/></svg>', 'tabelaAtiva' => 'contatos'],
                    [ 'label' => 'Salões', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="10" width="18" height="8" rx="2" fill="#FBBF24"/><rect x="7" y="6" width="10" height="4" rx="2" fill="#34D399"/></svg>', 'tabelaAtiva' => 'saloes'],
                    [ 'label' => 'Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><polygon points="12,2 22,8 12,14 2,8" fill="#A78BFA"/><rect x="6" y="16" width="12" height="4" rx="2" fill="#F472B6"/></svg>', 'tabelaAtiva' => 'planos'],
                    [ 'label' => 'Ajustes de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="8" width="18" height="8" rx="3" fill="#10B981"/><path d="M8 12h8M12 8v8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>', 'tabelaAtiva' => 'ajustes-planos'],
                ];
            @endphp
            @if($isAdmin)
                @foreach($menuAdmin as $item)
                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'admin'">
                        {!! $item['icon'] ?? '' !!}
                        {{ $item['label'] }}
                    </x-responsive-nav-link>
                @endforeach
            @endif

            {{-- MENU PROPRIETÁRIO --}}
            @if(($menuSelecionado === 'proprietario' && $isProprietario) || ($isProprietario && !$isFuncionario))
                @php
                    $menuProprietario = [
                        ['tabelaAtiva' => 'controle-pagamento', 'label' => 'Pagamentos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#34D399"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>'],
                        ['tabelaAtiva' => 'controle-pagamento-planos', 'label' => 'Pagamento de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#A78BFA"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                        ['tabelaAtiva' => 'controle-agenda', 'label' => 'Controle de Agendas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#34D399"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>'],
                        ['tabelaAtiva' => 'gerenciar-comandas', 'label' => 'Controle de Comandas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#FBBF24"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#34D399"/><circle cx="12" cy="16" r="2" fill="#F472B6"/></svg>'],
                        ['tabelaAtiva' => 'link-agendamento', 'label' => 'Link de Agendamento', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#60A5FA"/><circle cx="8" cy="12" r="2" fill="#FBBF24"/><circle cx="16" cy="12" r="2" fill="#FBBF24"/><path d="M10 12h4" stroke="#F472B6" stroke-width="2" stroke-linecap="round"/></svg>'],
                    ];
                    
                        $menuProprietario[] = ['tabelaAtiva' => 'planos-de-assinatura', 'label' => 'Planos de Assinatura', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'];
                    
                    $menuProprietario[] = ['tabelaAtiva' => 'meu-pagby', 'label' => 'Meu Pagby', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#F472B6"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#FBBF24"/></svg>'];
                    
                    $menuCadastros = [
                        'label' => 'Cadastros',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#FBBF24"/><circle cx="12" cy="12" r="4" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#F472B6"/></svg>',
                        'submenu' => [
                            ['tabelaAtiva' => 'usuarios', 'label' => 'Usuários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="8" r="4" fill="#60A5FA"/><rect x="4" y="16" width="16" height="5" rx="2.5" fill="#F472B6"/></svg>'],
                            ['tabelaAtiva' => 'filiais', 'label' => 'Filiais', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="10" width="18" height="8" rx="2" fill="#FBBF24"/><rect x="7" y="6" width="10" height="4" rx="2" fill="#34D399"/></svg>'],
                            ['tabelaAtiva' => 'funcionarios', 'label' => 'Funcionários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="2" y="6" width="20" height="12" rx="3" fill="#A78BFA"/><circle cx="8" cy="12" r="3" fill="#FBBF24"/><circle cx="16" cy="12" r="3" fill="#F472B6"/></svg>'],
                            ['tabelaAtiva' => 'horarios', 'label' => 'Horários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#A78BFA"/><rect x="11" y="6" width="2" height="7" rx="1" fill="#FBBF24"/><rect x="11" y="14" width="2" height="4" rx="1" fill="#34D399"/></svg>'],
                            ['tabelaAtiva' => 'servicos', 'label' => 'Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="8" width="18" height="8" rx="4" fill="#34D399"/><path d="M7 12h10" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="3" fill="#F472B6"/></svg>'],
                            ['tabelaAtiva' => 'func_serv', 'label' => 'Funcionários x Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="2" y="6" width="20" height="12" rx="3" fill="#A78BFA"/><circle cx="8" cy="12" r="3" fill="#FBBF24"/><circle cx="16" cy="12" r="3" fill="#F472B6"/><rect x="10" y="10" width="4" height="4" rx="2" fill="#34D399"/></svg>', 'menu' => $menuSelecionado],
                            ['tabelaAtiva' => 'gerenciar-estoque', 'label' => 'Controle de Estoque', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#FBBF24"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                        ]
                    ];
                    
                    $menuRelatorios = [
                        'label' => 'Relatórios',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#A78BFA"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>',
                        'submenu' => [
                            ['tabelaAtiva' => 'balanco-diario', 'label' => 'Balanço Diário', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                            ['tabelaAtiva' => 'ajuste-balanco-diario', 'label' => 'Ajuste Balanço Diário', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#F472B6"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#FBBF24"/></svg>'],
                            ['tabelaAtiva' => 'faturamento-mensal', 'label' => 'Faturamento Mensal', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle"><path fill-rule="evenodd" d="M3.75 3.375c0-1.036.84-1.875 1.875-1.875H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375Zm10.5 1.875a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25ZM12 10.5a.75.75 0 0 1 .75.75v.028a9.727 9.727 0 0 1 1.687.28.75.75 0 1 1-.374 1.452 8.207 8.207 0 0 0-1.313-.226v1.68l.969.332c.67.23 1.281.85 1.281 1.704 0 .158-.007.314-.02.468-.083.931-.83 1.582-1.669 1.695a9.776 9.776 0 0 1-.561.059v.028a.75.75 0 0 1-1.5 0v-.029a9.724 9.724 0 0 1-1.687-.278.75.75 0 0 1 .374-1.453c.425.11.864.186 1.313.226v-1.68l-.968-.332C9.612 14.974 9 14.354 9 13.5c0-.158.007-.314.02-.468.083-.931.831-1.582 1.67-1.694.185-.025.372-.045.56-.06v-.028a.75.75 0 0 1 .75-.75Zm-1.11 2.324c.119-.016.239-.03.36-.04v1.166l-.482-.165c-.208-.072-.268-.211-.268-.285 0-.113.005-.225.015-.336.013-.146.14-.309.374-.34Zm1.86 4.392V16.05l.482.165c.208.072.268.211.268.285 0 .113-.005.225-.015.336-.012.146-.14.309-.374.34-.12.016-.24.03-.361.04Z" clip-rule="evenodd" /></svg>'],
                        ]
                    ];
                    
                    $menuEstatisticas = [
                        'label' => 'Estatísticas',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#60A5FA"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#F472B6"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>',
                        'submenu' => [
                            ['tabelaAtiva' => 'dias-pico', 'label' => 'Dias de Pico', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#FBBF24"/><circle cx="12" cy="12" r="6" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#34D399"/></svg>'],
                            ['tabelaAtiva' => 'horarios-pico', 'label' => 'Horários de Pico', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#60A5FA"/><path d="M7 17l5-5 5 5" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="9" r="2" fill="#F472B6"/></svg>'],
                            ['tabelaAtiva' => 'avaliacoes', 'label' => 'Avaliações', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#F472B6"/><path d="M12 8l2.5 5H9.5L12 8z" fill="#FBBF24"/><circle cx="12" cy="15" r="2" fill="#60A5FA"/></svg>'],
                            ['tabelaAtiva' => 'ranking-servicos', 'label' => 'Ranking de Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#34D399"/><path d="M12 7l3 7H9l3-7z" fill="#FBBF24"/><circle cx="12" cy="17" r="2" fill="#60A5FA"/></svg>'],
                        ]
                    ];
                @endphp
                
                {{-- Itens principais --}}
                @foreach($menuProprietario as $item)
                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'proprietario'">
                        {!! $item['icon'] ?? '' !!}
                        {{ $item['label'] }}
                    </x-responsive-nav-link>
                @endforeach
                
                {{-- Cadastros (com submenu) --}}
                <div x-data="{ openSub: false }" class="mb-2">
                    <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                        {!! $menuCadastros['icon'] !!}
                        <span>{{ $menuCadastros['label'] }}</span>
                        <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                        @foreach($menuCadastros['submenu'] as $subitem)
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'proprietario'">
                                {!! $subitem['icon'] !!}
                                {{ $subitem['label'] }}
                            </x-responsive-nav-link>
                        @endforeach
                    </div>
                </div>
                
                {{-- Relatórios (com submenu) --}}
                <div x-data="{ openSub: false }" class="mb-2">
                    <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                        {!! $menuRelatorios['icon'] !!}
                        <span>{{ $menuRelatorios['label'] }}</span>
                        <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                        @foreach($menuRelatorios['submenu'] as $subitem)
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'proprietario'">
                                {!! $subitem['icon'] !!}
                                {{ $subitem['label'] }}
                            </x-responsive-nav-link>
                        @endforeach
                    </div>
                </div>
                
                {{-- Estatísticas (com submenu) --}}
                <div x-data="{ openSub: false }" class="mb-2">
                    <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                        {!! $menuEstatisticas['icon'] !!}
                        <span>{{ $menuEstatisticas['label'] }}</span>
                        <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                        @foreach($menuEstatisticas['submenu'] as $subitem)
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'proprietario'">
                                {!! $subitem['icon'] !!}
                                {{ $subitem['label'] }}
                            </x-responsive-nav-link>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- MENU FUNCIONÁRIO --}}
            @if(($menuSelecionado === 'funcionario' && $isFuncionario) || ($isFuncionario && !$isProprietario))
                @php
                    $menuFuncionario = [
                        ['tabelaAtiva' => 'agenda', 'label' => 'Agenda', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="5" width="18" height="16" rx="3" fill="#FBBF24"/><rect x="7" y="2" width="10" height="4" rx="2" fill="#60A5FA"/><circle cx="12" cy="13" r="3" fill="#F472B6"/></svg>'],
                        ['tabelaAtiva' => 'servicos', 'label' => 'Meus Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg>'],
                        ['tabelaAtiva' => 'servicos-funcionario-realizados', 'label' => 'Histórico de Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#F472B6"/><circle cx="12" cy="12" r="4" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#FBBF24"/></svg>'],
                        ['tabelaAtiva' => 'horarios', 'label' => 'Meus Horários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#A78BFA"/><rect x="11" y="6" width="2" height="7" rx="1" fill="#FBBF24"/><rect x="11" y="14" width="2" height="4" rx="1" fill="#34D399"/></svg>'],
                        ['tabelaAtiva' => 'estatisticas', 'label' => 'Estatísticas Pessoais', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="14" width="3" height="6" rx="1.5" fill="#FBBF24"/><rect x="10" y="10" width="3" height="10" rx="1.5" fill="#F472B6"/><rect x="16" y="6" width="3" height="14" rx="1.5" fill="#60A5FA"/></svg>'],
                        ['tabelaAtiva' => 'avaliacoes-profissional', 'label' => 'Avaliações', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><polygon points="12,2 15,9 22,9 17,14 19,21 12,17 5,21 7,14 2,9 9,9" fill="#FBBF24"/></svg>'],
                    ];
                    
                    $menuHonorarios = [
                        'label' => 'Honorários',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg>',
                        'submenu' => [
                            ['tabelaAtiva' => 'servicos-avulsos', 'label' => 'Serviços Avulsos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                            ['tabelaAtiva' => 'servicos-planos', 'label' => 'Serviços de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>']
                        ]
                    ];
                @endphp
                
                {{-- Itens principais do funcionário --}}
                @foreach($menuFuncionario as $item)
                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'funcionario'">
                        {!! $item['icon'] !!}
                        {{ $item['label'] }}
                    </x-responsive-nav-link>
                @endforeach
                
                {{-- Honorários (com submenu) --}}
                <div x-data="{ openSub: false }" class="mb-2">
                    <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                        {!! $menuHonorarios['icon'] !!}
                        <span>{{ $menuHonorarios['label'] }}</span>
                        <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                        @foreach($menuHonorarios['submenu'] as $subitem)
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'funcionario'">
                                {!! $subitem['icon'] !!}
                                {{ $subitem['label'] }}
                            </x-responsive-nav-link>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- MENU CLIENTE --}}
            @if($isCliente)
                @php
                    $menuCliente = [
                        ['tabelaAtiva' => 'appointments', 'label' => '<span class="inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="5" width="18" height="16" rx="3" fill="#FBBF24"/><rect x="7" y="2" width="10" height="4" rx="2" fill="#60A5FA"/><circle cx="12" cy="13" r="3" fill="#F472B6"/></svg> Agendamentos</span>'],
                        ['tabelaAtiva' => 'historico', 'label' => '<span class="inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg> Histórico de Serviços</span>'],
                    ];
                    
                    // Adicionar planos se tiver plano ativo
                    // $temPlanoAtivo já definido acima
                    if($temPlanoAtivo) {
                        $menuCliente[] = ['tabelaAtiva' => 'planos-de-assinatura', 'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle"><path fill-rule="evenodd" d="M3.75 3.375c0-1.036.84-1.875 1.875-1.875H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375Zm10.5 1.875a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25ZM12 10.5a.75.75 0 0 1 .75.75v.028a9 .727 .727 .00001 .00001 -2 .28 .75 .75 .00001 -2 .374 -2 .453 1.452 8.207 8.207 0 0 0 -1.313 -.226v1.68l.969 .332c.67 .23 1.281 .85 1.281 1.704 0 .158 -.007 .314 -.02 .468 -.083 .931 -.83 1.582 -1.669 1.695a9.776 9.776 0 0 1 -.561 .059v .028a.75 .75 0 0 1 -1.5 0v -.029a9 .724 .724 .00001 -2 -1.687 -.278 .75 .75 .00001 -2 .374 -2 .453 1.453 c0 .113 -.005 .225 -.015 .336 -.013 .146 -.14 .309 -.374 .34 -.12 .016 -.24 .03 -.361 .04Z" clip-rule="evenodd" /></svg> Planos de Assinatura'];
                    }
                @endphp
                
                @foreach($menuCliente as $item)
                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva'], 'menu' => $menuSelecionado])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva'] && request()->input('menu', $menuSelecionado) === 'cliente'">
                        {!! $item['label'] !!}
                    </x-responsive-nav-link>
                @endforeach
            @endif

            {{-- Links de perfil e logout --}}
            @auth
            <div class="mt-6 border-t pt-4">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Perfil') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                            <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6Zm-5.03 4.72a.75.75 0 0 0 0 1.06l1.72 1.72H2.25a.75.75 0 0 0 0 1.5h10.94l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            @endauth
        </div>
    </div>
</nav>