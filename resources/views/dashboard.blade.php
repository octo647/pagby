@php
    $menuSelecionado = request()->input('menu');
@endphp
<x-app-layout>
    <x-slot name="header">
    
        <h2 class="text-xl font-semibold leading-tight text-center text-gray-800">
        @if(auth()->user()->hasrole('Admin'))
            {{ __($tabelaAtiva === 'contatos' ? 'Contatos' : 
            ($tabelaAtiva === 'contatos-booksy' ? 'Contatos Booksy' :
            ($tabelaAtiva === 'saloes' ? 'Salões' : 
            ($tabelaAtiva === 'planos' ? 'Planos' :
            ($tabelaAtiva === 'ajustes-planos' ? 'Ajustes de Planos' : '')))))}}
        @elseif(auth()->user()->hasrole('Proprietário'))
            @php
                $titles = [
                    'usuarios' => 'Usuários',
                    'filiais' => 'Filiais',
                    'funcionarios' => 'Funcionários por filial',
                    'horarios' => 'Horários dos Funcionários',
                    'servicos' => 'Serviços oferecidos',
                    'func_serv' => 'Funcionários e Serviços',
                    'servicos-realizados' => 'Serviços Realizados',
                    'assiduidade' => 'Assiduidade dos Clientes',
                    'faturamento-mensal' => 'Faturamento Mensal',
                    'origens' => 'Origens dos Clientes',
                    'ticket-medio' => 'Ticket Médio por Cliente',
                    'horarios-pico' => 'Horários de Pico',
                    'dias-pico' => 'Dias de Pico',
                    'avaliacoes' => 'Satisfação dos Clientes',
                    'ranking-servicos' => 'Serviços mais Solicitados',
                    'clientes-novos-antigos' => 'Clientes Novos e Antigos',
                    'gerenciar-estoque' => 'Controle de Estoque',
                    'gerenciar-comandas' => 'Controle de Comandas'
                ];
            @endphp
            {{ __($titles[$tabelaAtiva] ?? '') }}
        @elseif(auth()->user()->hasrole('Funcionário'))
            @php
                $funcionarioTitles = [
                    'agenda' => 'Minha Agenda',
                    'servicos' => 'Meus Serviços',
                    'servicos-realizados' => 'Serviços Realizados',
                    'horarios' => 'Meus Horários',
                    'estatisticas' => 'Estatísticas Pessoais',
                    'ranking-servicos' => 'Ranking de Serviços',
                    'avaliacoes-profissional' => 'Avaliações dos Clientes',
                    'dias-pico' => 'Dias de Pico',
                    'horarios-pico' => 'Horários de Pico'
                ];
            @endphp
            {{ __($funcionarioTitles[$tabelaAtiva] ?? '') }}
        @elseif(auth()->user()->hasrole('Cliente'))
            {{ __($tabelaAtiva === 'appointments' ? 'Agendamentos' :
            ($tabelaAtiva === 'historico' ? 'Histórico de Serviços':
            ($tabelaAtiva === 'notificacoes' ? 'Notificações' : ''))) }}
        @else
            {{ __('Dashboard') }}
        @endif
            
        </h2>   
     </x-slot>
<script>
    window.addEventListener('error', function(e) {
        alert('Erro: ' + e.message);
    });
</script>

    <div class="py-1">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">

                @include('includes.messages')
                @if(session('chooseone'))
                {{session('chooseone')}}
                @endif
                @can('Admin')
                @if($tabelaAtiva === 'contatos-booksy')
                    @livewire('admin.booksy-contacts') 
                @elseif($tabelaAtiva === 'contatos')
                    @livewire('list-contacts') 
                @elseif($tabelaAtiva === 'saloes')
                    @livewire('admin.saloes')
                @elseif($tabelaAtiva === 'planos')
                    @livewire('admin.planos')
                @elseif($tabelaAtiva === 'ajustes-planos')
                    @livewire('admin.plan-adjustments')
                @endif

                @endcan

                @can('Proprietário')
               
                                                           
                {{-- Exibe o conteúdo conforme a aba ativa --}}
                    @if($tabelaAtiva === 'usuarios')
                        @livewire('salon-users')
                    @elseif($tabelaAtiva === 'filiais')
                        @livewire('proprietario.filiais')
                    @elseif($tabelaAtiva === 'funcionarios')
                        @livewire('proprietario.branch-users')
                    @elseif($tabelaAtiva === 'horarios' && $menuSelecionado === 'proprietario')
                        @livewire('proprietario.salon-times')
                    @elseif($tabelaAtiva === 'servicos' && $menuSelecionado === 'proprietario')
                        @livewire('proprietario.services')
                    @elseif($tabelaAtiva === 'func_serv')
                        @if(request()->input('funcionario_id'))
                            @livewire('proprietario.employee-service')
                        @else
                            @livewire('proprietario.employee-selector')
                        @endif
                    @elseif($tabelaAtiva === 'servicos-realizados')
                        @livewire('proprietario.servicos-realizados')
                    @elseif($tabelaAtiva === 'assiduidade')
                        @livewire('proprietario.assiduidade')   
                    @elseif($tabelaAtiva === 'faturamento-mensal')
                        @livewire('proprietario.faturamento-mensal')
                    @elseif($tabelaAtiva === 'origens')
                        @livewire('proprietario.origens')
                    @elseif($tabelaAtiva === 'ticket-medio')
                        @livewire('proprietario.ticket-medio')
                    @elseif($tabelaAtiva === 'horarios-pico')
                        @livewire('horarios-pico')
                    @elseif($tabelaAtiva === 'dias-pico')
                        @livewire('dias-pico')  
                    @elseif($tabelaAtiva === 'avaliacoes')
                        @livewire('proprietario.avaliacoes') 
                    @elseif($tabelaAtiva === 'ranking-servicos')
                        @livewire('proprietario.ranking-servicos')
                    @elseif($tabelaAtiva === 'clientes-novos-antigos')
                        @livewire('proprietario.clientes-novos-antigos')
                    @elseif($tabelaAtiva === 'balanco-diario')
                        @livewire('proprietario.balanco-diario')
                    @elseif($tabelaAtiva === 'ajuste-balanco-diario')
                        @livewire('proprietario.ajuste-balanco-diario')
                    @elseif($tabelaAtiva === 'gerenciar-estoque')
                        @livewire('proprietario.gerenciar-estoque')
                    @elseif($tabelaAtiva === 'controle-agenda')
                        @livewire('proprietario.controle-agenda')
                    @elseif($tabelaAtiva === 'controle-pagamento')
                        @livewire('proprietario.controle-pagamento')
                    @elseif($tabelaAtiva === 'controle-pagamento-planos')
                        @livewire('proprietario.controle-pagamento-planos')
                    @elseif($tabelaAtiva === 'gerenciar-comandas')
                        @livewire('proprietario.gerenciar-comandas')
                    @elseif($tabelaAtiva === 'relatorio-geral')
                        @livewire('proprietario.relatorio-geral')
                    @elseif($tabelaAtiva === 'relatorio-mensal')
                        @livewire('proprietario.relatorio-mensal')
                    @elseif($tabelaAtiva === 'relatorio-anual')
                        @livewire('proprietario.relatorio-anual')
                    @elseif($tabelaAtiva === 'planos-de-assinatura')
                        @livewire('planos-de-assinatura')
                    @elseif($tabelaAtiva === 'meu-pagby')
                        @livewire('proprietario.meu-pagby')
                    @elseif($tabelaAtiva === 'gerenciar-comandas')
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h1 class="text-3xl font-bold text-gray-900">Controle de Comandas</h1>
                            </div>
                            @livewire('proprietario.gerenciar-comandas')
                        </div>
                    @endif  
            </div>              
                
                @endcan


                @can('Funcionário')
                @if($tabelaAtiva === 'agenda')
                    @livewire('funcionario.agenda')
                @elseif($tabelaAtiva === 'servicos' && $menuSelecionado === 'funcionario')
                    @livewire('funcionario.servicos')
                @elseif($tabelaAtiva === 'servicos-planos')
                    @livewire('funcionario.controle-pagamento-planos')
                @elseif($tabelaAtiva === 'servicos-funcionario-realizados')
                    @livewire('funcionario.servicos-funcionario-realizados')
                @elseif($tabelaAtiva === 'horarios' && $menuSelecionado === 'funcionario')
                    @livewire('funcionario.horarios')
                @elseif($tabelaAtiva === 'estatisticas')
                    @livewire('funcionario.estatisticas')
                @elseif($tabelaAtiva === 'avaliacoes-profissional')
                    @livewire('funcionario.avaliacoes-profissional')
               {{-- @elseif($tabelaAtiva === 'dias-pico')
                    @livewire('dias-pico')
                @elseif($tabelaAtiva === 'horarios-pico')
                    @livewire('horarios-pico')--}}
                @elseif($tabelaAtiva === 'servicos-avulsos')
                    @livewire('funcionario.controle-ganhos-avulsos')
                @endif

                @endcan
                @can('Cliente')
                @if($tabelaAtiva === 'appointments')
                    @livewire('cliente.appointments') 
                @elseif($tabelaAtiva === 'historico')
                    @livewire('cliente.historico')
               
                @elseif($tabelaAtiva === 'notificacoes')
                    @livewire('cliente.notificacoes')
                @elseif($tabelaAtiva === 'planos-de-assinatura')
                    @livewire('planos-de-assinatura')
                @endif
                
                @endcan
                

               
               {{-- <x-welcome/> --}}

            </div>
        </div>


    </div>

   

    </div>


   
</x-app-layout>

