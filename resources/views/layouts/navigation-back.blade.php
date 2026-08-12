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
        <div class="bg-gradient-to-b from-blue-50 via-white to-pink-50 w-72 h-full shadow-2xl p-6 rounded-r-2xl border-r border-blue-200 animate-slidein">
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
            
             @php
                if (!isset($tabelaAtiva)) {
                    $tabelaAtiva = null;
                }
                use App\Models\Plan;
                $isTenant = !in_array(request()->getHost(), config('tenancy.central_domains'));
                $temPlanoAtivo = false;
                if ($isTenant) {
                $temPlanoAtivo = Plan::where('active', true)->exists();
                }
                $menuAdmin = [
                    [ 'label' => 'Contatos', 
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle">
                    <circle cx="12" cy="8" r="4" fill="#60A5FA"/>
                    <rect x="4" y="16" width="16" height="5" rx="2.5" fill="#F472B6"/>
                    </svg>', 'tabelaAtiva' => 'contatos'],
                    [ 'label' => 'Salões', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle">
                    <rect x="3" y="10" width="18" height="8" rx="2" fill="#FBBF24"/>
                    <rect x="7" y="6" width="10" height="4" rx="2" fill="#34D399"/>
                    </svg>', 'tabelaAtiva' => 'saloes'],
                    [ 'label' => 'Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle">
                    <polygon points="12,2 22,8 12,14 2,8" fill="#A78BFA"/>
                    <rect x="6" y="16" width="12" height="4" rx="2" fill="#F472B6"/>
                    </svg>', 'tabelaAtiva' => 'planos'],
                    [ 'label' => 'Ajustes de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle">
                    <rect x="3" y="8" width="18" height="8" rx="3" fill="#10B981"/>
                    <path d="M8 12h8M12 8v8" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                    </svg>', 'tabelaAtiva' => 'ajustes-planos'],
                ];
                
                $menuProprietario = [
                     ['tabelaAtiva' => 'controle-pagamento', 'label' => 'Pagamentos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#34D399"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>'],
                    ['tabelaAtiva' => 'controle-pagamento-planos', 'label' => 'Pagamento de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#A78BFA"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                    ['tabelaAtiva' => 'controle-agenda', 'label' => 'Controle de Agendas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#34D399"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>'],
                    
                    ['tabelaAtiva' => 'gerenciar-comandas', 'label' => 'Controle de Comandas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#FBBF24"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#34D399"/><circle cx="12" cy="16" r="2" fill="#F472B6"/></svg>'],
                ];
                
                // Adicionar Customizar Home apenas para template Padrão
                if (tenant()->template === 'Padrao') {
                    $menuProprietario[] = ['tabelaAtiva' => 'customizar-home', 'label' => 'Customizar Home', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle text-purple-600"><path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z"/><path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z"/></svg>'];
                }
                
                $menuProprietario[] =

                    [ 'label' => 'Cadastros', 
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#FBBF24"/><circle cx="12" cy="12" r="4" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#F472B6"/></svg>',
                    'submenu' => [
                        ['tabelaAtiva' => 'usuarios', 'label' => 'Usuários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="8" r="4" fill="#60A5FA"/><rect x="4" y="16" width="16" height="5" rx="2.5" fill="#F472B6"/></svg>'
                        ],
                        ['tabelaAtiva' => 'filiais', 'label' => 'Filiais', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="10" width="18" height="8" rx="2" fill="#FBBF24"/><rect x="7" y="6" width="10" height="4" rx="2" fill="#34D399"/></svg>'
                        ],
                        ['tabelaAtiva' => 'funcionarios', 'label' => 'Funcionários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="2" y="6" width="20" height="12" rx="3" fill="#A78BFA"/><circle cx="8" cy="12" r="3" fill="#FBBF24"/><circle cx="16" cy="12" r="3" fill="#F472B6"/></svg>'
                        ],
                        ['tabelaAtiva' => 'horarios', 'label' => 'Horários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#A78BFA"/><rect x="11" y="6" width="2" height="7" rx="1" fill="#FBBF24"/><rect x="11" y="14" width="2" height="4" rx="1" fill="#34D399"/></svg>'],
                        ['tabelaAtiva' => 'servicos', 'label' => 'Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="8" width="18" height="8" rx="4" fill="#34D399"/><path d="M7 12h10" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="3" fill="#F472B6"/></svg>'],
                        ['tabelaAtiva' => 'func_serv', 'label' => 'Funcionários x Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="2" y="6" width="20" height="12" rx="3" fill="#A78BFA"/><circle cx="8" cy="12" r="3" fill="#FBBF24"/><circle cx="16" cy="12" r="3" fill="#F472B6"/><rect x="10" y="10" width="4" height="4" rx="2" fill="#34D399"/></svg>', 'special' => 'func_serv'],
                        ['tabelaAtiva' => 'gerenciar-estoque', 'label' => 'Controle de Estoque', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#FBBF24"/><rect x="7" y="10" width="10" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],

                        
                    ]
                    ],
                   
                [
             'label' => 'Relatórios',
             'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#A78BFA"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>',
                'submenu' => [
                    ['tabelaAtiva' => 'balanco-diario', 'label' => 'Balanço Diário', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                    ['tabelaAtiva' => 'ajuste-balanco-diario', 'label' => 'Ajuste Balanço Diário', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#F472B6"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#FBBF24"/></svg>'],
                    ['tabelaAtiva' => 'gerenciar-estoque', 'label' => 'Controle de Estoque', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>'],
                    
                    
                    ['tabelaAtiva' => 'faturamento-mensal', 'label' => 'Faturamento Mensal',        
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                    <path fill-rule="evenodd" d="M3.75 3.375c0-1.036.84-1.875 1.875-1.875H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375Zm10.5 1.875a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25ZM12 10.5a.75.75 0 0 1 .75.75v.028a9.727 9.727 0 0 1 1.687.28.75.75 0 1 1-.374 1.452 8.207 8.207 0 0 0-1.313-.226v1.68l.969.332c.67.23 1.281.85 1.281 1.704 0 .158-.007.314-.02.468-.083.931-.83 1.582-1.669 1.695a9.776 9.776 0 0 1-.561.059v.028a.75.75 0 0 1-1.5 0v-.029a9.724 9.724 0 0 1-1.687-.278.75.75 0 0 1 .374-1.453c.425.11.864.186 1.313.226v-1.68l-.968-.332C9.612 14.974 9 14.354 9 13.5c0-.158.007-.314.02-.468.083-.931.831-1.582 1.67-1.694.185-.025.372-.045.56-.06v-.028a.75.75 0 0 1 .75-.75Zm-1.11 2.324c.119-.016.239-.03.36-.04v1.166l-.482-.165c-.208-.072-.268-.211-.268-.285 0-.113.005-.225.015-.336.013-.146.14-.309.374-.34Zm1.86 4.392V16.05l.482.165c.208.072.268.211.268.285 0 .113-.005.225-.015.336-.012.146-.14.309-.374.34-.12.016-.24.03-.361.04Z" clip-rule="evenodd" />
                    </svg>
                    '],
                   
                    
                ]
            ],
            [
                'label' => 'Estatísticas',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="6" width="16" height="12" rx="3" fill="#60A5FA"/><rect x="8" y="10" width="8" height="4" rx="2" fill="#F472B6"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>',
                'submenu' => [
                    ['tabelaAtiva' => 'dias-pico', 'label' => 'Dias de Pico',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#FBBF24"/><circle cx="12" cy="12" r="6" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#34D399"/></svg>',
                    ],
                    ['tabelaAtiva' => 'horarios-pico', 'label' => 'Horários de Pico', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#60A5FA"/><path d="M7 17l5-5 5 5" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="9" r="2" fill="#F472B6"/></svg>',
                    ],
                    ['tabelaAtiva' => 'avaliacoes', 'label' => 'Avaliações', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#F472B6"/><path d="M12 8l2.5 5H9.5L12 8z" fill="#FBBF24"/><circle cx="12" cy="15" r="2" fill="#60A5FA"/></svg>',
                    ],
                    ['tabelaAtiva' => 'ranking-servicos', 'label' => 'Ranking de Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="3" width="18" height="18" rx="4" fill="#34D399"/><path d="M12 7l3 7H9l3-7z" fill="#FBBF24"/><circle cx="12" cy="17" r="2" fill="#60A5FA"/></svg>',
                    ],
                    ['tabelaAtiva' => 'ticket-medio', 'label' => 'Ticket Médio', 'icon' =>'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="2" y="6" width="20" height="12" rx="3" fill="#F472B6"/><path d="M7 12h10M12 9v6" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2" fill="#FBBF24"/></svg>',
                    ],
                    ['tabelaAtiva' => 'clientes-novos-antigos', 'label' => 'Clientes Novos e Antigos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#60A5FA"/><circle cx="8" cy="12" r="3" fill="#FBBF24"/><circle cx="16" cy="12" r="3" fill="#F472B6"/></svg>',
                    ],
                    ['tabelaAtiva' => 'assiduidade', 'label' => 'Assiduidade', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#FBBF24"/><circle cx="12" cy="12" r="6" fill="#34D399"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#F472B6"/></svg>',
                    ],
                    ['tabelaAtiva' => 'origens', 'label' => 'Origens', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#60A5FA"/><circle cx="12" cy="12" r="6" fill="#FBBF24"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#34D399"/></svg>',
                    ],
                    ['tabelaAtiva' => 'servicos-realizados', 'label' => 'Serviços Realizados', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="4" width="16" height="16" rx="4" fill="#F472B6"/><path d="M8 16h8M12 8v8" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2" fill="#FBBF24"/></svg>',
                    ],
            ],              
            
            ],
            ['tabelaAtiva' => 'planos-de-assinatura', 'label' => 'Planos de Assinatura', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
            ['tabelaAtiva' => 'meu-pagby', 'label' => 'Meu Pagby', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#F472B6"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#A78BFA"/><circle cx="12" cy="16" r="2" fill="#FBBF24"/></svg>'],
    ];

                $menuFuncionario = [
                    ['tabelaAtiva' => 'agenda', 'label' => 'Agenda', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="5" width="18" height="16" rx="3" fill="#FBBF24"/><rect x="7" y="2" width="10" height="4" rx="2" fill="#60A5FA"/><circle cx="12" cy="13" r="3" fill="#F472B6"/></svg>'],
                    [ 'label' => 'Honorários', 
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg>',
                    'submenu'=>[
                        ['tabelaAtiva' => 'servicos-avulsos', 'label' => 'Serviços Avulsos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#A78BFA"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#34D399"/></svg>'],
                        ['tabelaAtiva' => 'servicos-planos', 'label' => 'Serviços de Planos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><rect x="8" y="12" width="8" height="4" rx="2" fill="#FBBF24"/><circle cx="12" cy="16" r="2" fill="#A78BFA"/></svg>']
                    ]
                    ],
                    ['tabelaAtiva' => 'servicos', 'label' => 'Meus Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg>'],
                    ['tabelaAtiva' => 'servicos-funcionario-realizados', 'label' => 'Histórico de Serviços', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="6" width="18" height="12" rx="3" fill="#F472B6"/><circle cx="12" cy="12" r="4" fill="#60A5FA"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#FBBF24"/></svg>'],
                    ['tabelaAtiva' => 'horarios', 'label' => 'Meus Horários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#A78BFA"/><rect x="11" y="6" width="2" height="7" rx="1" fill="#FBBF24"/><rect x="11" y="14" width="2" height="4" rx="1" fill="#34D399"/></svg>'],
                    ['tabelaAtiva' => 'estatisticas', 'label' => 'Estatísticas Pessoais', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="14" width="3" height="6" rx="1.5" fill="#FBBF24"/><rect x="10" y="10" width="3" height="10" rx="1.5" fill="#F472B6"/><rect x="16" y="6" width="3" height="14" rx="1.5" fill="#60A5FA"/></svg>'],
                    ['tabelaAtiva' => 'avaliacoes-profissional', 'label' => 'Avaliações', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><polygon points="12,2 15,9 22,9 17,14 19,21 12,17 5,21 7,14 2,9 9,9" fill="#FBBF24"/></svg>'],
                    ['tabelaAtiva' => 'dias-pico', 'label' => 'Dias de Pico','icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#34D399"/><rect x="8" y="8" width="8" height="8" rx="4" fill="#F472B6"/></svg>'],
                    ['tabelaAtiva' => 'horarios-pico', 'label' => 'Horários de Pico', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><circle cx="12" cy="12" r="10" fill="#60A5FA"/><rect x="11" y="7" width="2" height="10" rx="1" fill="#FBBF24"/></svg>'],
                    
                ];
                $funcionarioAtivo = request()->input('funcionario_id');
                $menuCliente = [
                    ['tabelaAtiva' => 'appointments', 'label' =>  '<span class="inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="3" y="5" width="18" height="16" rx="3" fill="#FBBF24"/><rect x="7" y="2" width="10" height="4" rx="2" fill="#60A5FA"/><circle cx="12" cy="13" r="3" fill="#F472B6"/></svg> Agendamentos</span>'],
                    ['tabelaAtiva' => 'historico', 'label' => '<span class="inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-5 inline mr-1 align-middle"><rect x="4" y="8" width="16" height="8" rx="4" fill="#34D399"/><circle cx="12" cy="12" r="3" fill="#FBBF24"/><rect x="10" y="16" width="4" height="2" rx="1" fill="#A78BFA"/></svg> Histórico de Serviços</span>'],

                    
                    ]; 
                    if($temPlanoAtivo) {
                        $menuCliente[] = ['tabelaAtiva' => 'planos-de-assinatura', 'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 inline mr-1 align-middle">
                        <path fill-rule="evenodd" d="M3.75 3.375c0-1.036.84-1.875 1.875-1.875H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375Zm10.5 1.875a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25ZM12 10.5a.75.75 0 0 1 .75 .75v .028a9 .727 .727 .00001 .00001 -2 .28 .75 .75 .00001 -2 .374 -2 .453 1.452 8.207 8.207 0 0 0 -1.313 -.226v1.68l.969 .332c.67 .23 1.281 .85 1.281 1.704 0 .158 -.007 .314 -.02 .468 -.083 .931 -.83 1.582 -1.669 1.695a9.776 9.776 0 0 1 -.561 .059v .028a.75 .75 0 0 1 -1.5 0v -.029a9 .724 .724 .00001 -2 -1.687 -.278 .75 .75 .00001 -2 .374 -2 .453 1.453 c0 .113 -.005 .225 -.015 .336 -.013 .146 -.14 .309 -.374 .34 -.12 .016 -.24 .03 -.361 .04Z" clip-rule="evenodd" />  
                        </svg> Planos de Assinatura'];
                    }
            @endphp
    
            @auth
             @if(auth()->user()->hasRole('Admin') )
                @foreach($menuAdmin as $item)
                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva']])" :active="$tabelaAtiva === $item['tabelaAtiva']">
                        {!! $item['icon'] ?? '' !!}
                        {{ $item['label'] }}
                    </x-responsive-nav-link>
                @endforeach
            @endif
            @if(auth()->user()->hasrole('Proprietário'))
                   @foreach($menuProprietario as $item)
                   @if(isset($item['submenu']))
                        <!-- Renderiza grupo com submenu -->
                        <div x-data="{ openSub: false }" class="mb-2">
                            <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                                {!! $item['icon'] ?? '' !!}
                                <span>{{ $item['label'] }} </span>
                                <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                    @foreach($item['submenu'] as $subitem)
                        @if(isset($subitem['submenu']))
                        <div x-data="{ openSub2: false }" class="mb-2">
                            <button @click="openSub2 = !openSub2" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                                {!! $subitem['icon'] ?? '' !!}
                                <span>{{ $subitem['label'] }}</span>
                                <svg :class="{'rotate-90': openSub2}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="openSub2" class="pl-6 mt-1 space-y-1">
                                @foreach($subitem['submenu'] as $subsubitem)
                                    <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subsubitem['tabelaAtiva'], 'funcionario_id' => $subsubitem['funcionario_id']])" :active="request()->input('tabelaAtiva') === $subsubitem['tabelaAtiva'] && request()->input('funcionario_id') == $subsubitem['funcionario_id']">
                                        {!! $subsubitem['icon'] ?? '' !!}
                                        {{ $subsubitem['label'] }}
                                    </x-responsive-nav-link>
                                @endforeach
                            </div>
                        </div>
                        @else
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva']])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva']">
                            {!! $subitem['icon'] ?? '' !!}
                                {{ $subitem['label'] }} 
                            </x-responsive-nav-link>
                            @endif
                        @endforeach
                            </div>
                        </div>
                    @elseif(isset($item['special']) && $item['special'] === 'func_serv')
                    <!-- Funcionários x Serviços como item simples -->
                        <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => 'func_serv'])" :active="request()->input('tabelaAtiva') === 'func_serv'">
                            {!! $item['icon'] ?? '' !!}
                            {{ $item['label'] }}
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva']])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva']">
                            {!! $item['icon'] ?? '' !!}
                            {{ $item['label'] }}
                        </x-responsive-nav-link>
                @endif 
                @endforeach
                @elseif(auth()->user()->hasrole('Funcionário'))
                    @foreach($menuFuncionario as $item)
                        @if(isset($item['submenu']))
                            <div x-data="{ openSub: false }" class="mb-2">
                                <button @click="openSub = !openSub" class="flex items-center w-full px-4 py-2 text-left hover:bg-gray-100 rounded">
                                    {!! $item['icon'] ?? '' !!}
                                    <span>{{ $item['label'] }}</span>
                                    <svg :class="{'rotate-90': openSub}" class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="openSub" class="pl-6 mt-1 space-y-1">
                                    @foreach($item['submenu'] as $subitem)
                                        <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $subitem['tabelaAtiva'], 'funcionario_id' => $funcionarioAtivo])" :active="request()->input('tabelaAtiva') === $subitem['tabelaAtiva'] && request()->input('funcionario_id') == $funcionarioAtivo">
                                            {!! $subitem['icon'] ?? '' !!}
                                            {{ $subitem['label'] }}
                                        </x-responsive-nav-link>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva']])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva']">
                                {!! $item['icon'] ?? '' !!}
                                {{ $item['label'] }}
                            </x-responsive-nav-link>
                        @endif
                    @endforeach

                @elseif(auth()->user()->hasrole('Cliente'))
                    @foreach($menuCliente as $item)
                    

                        <x-responsive-nav-link :href="route('tenant.dashboard', ['tabelaAtiva' => $item['tabelaAtiva']])" :active="request()->input('tabelaAtiva') === $item['tabelaAtiva']">
                            {!! $item['label'] !!}
                        </x-responsive-nav-link>
                    @endforeach
                @endif
            @endauth
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