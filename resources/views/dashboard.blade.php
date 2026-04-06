@php
    $menuSelecionado = request()->input('menu');
@endphp
<x-app-layout>
    <x-slot name="header">
    
        <h2 class="text-2xl font-semibold leading-tight text-center text-gray-800">
        @if(auth()->user()->hasrole('Admin'))
            {{ __($tabelaAtiva === 'contatos' ? 'Contatos' : 
            ($tabelaAtiva === 'contatos-booksy' ? 'Contatos Booksy' :
            ($tabelaAtiva === 'saloes' ? 'Salões' : 
            ($tabelaAtiva === 'planos' ? 'Planos' :
            ($tabelaAtiva === 'ajustes-planos' ? 'Ajustes de Planos' : '')))))}}
        @elseif(auth()->user()->hasrole('Proprietário'))
            @php
                $titles = [
                    'agenda' => 'Minha Agenda',
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
                    'gerenciar-comandas' => 'Controle de Comandas',
                    'link-agendamento' => 'Link de Agendamento para Redes Sociais',
                    'controle-pagamento' => 'Controle de Pagamento',
                    'controle-pagamento-planos' => 'Pagamento dos Planos',
                    'balanco-diario' => 'Balanço Diário',
                    'ajuste-balanco-diario' => 'Ajuste de Balanço Diário',
                    'balanco-semanal' => 'Balanço Semanal',
                    'controle-agenda' => 'Controle de Agendas',
                    'customizar-home' => 'Customizar Home',
                    'meu-pagby' => 'Meu PagBy',
                    'relatorio-geral' => 'Relatório Geral',
                    'relatorio-mensal' => 'Relatório Mensal',
                    'relatorio-anual' => 'Relatório Anual',
                    'clientes-inadimplentes' => 'Gestão de Inadimplência',
                    'planos-de-assinatura' => 'Planos de Assinatura'
                ];
                $subtitles = [
                    'agenda' => 'Visualize e gerencie seus compromissos',
                    'usuarios' => 'Gerencie o status dos usuários',
                    'filiais' => 'Gerencie as filiais do seu salão',
                    'funcionarios' => 'Gerencie os funcionários de cada filial',
                    'horarios' => 'Configure os horários de atendimento dos funcionários',
                    'servicos' => 'Gerencie os serviços oferecidos pelo seu salão',
                    'func_serv' => 'Associe funcionários aos serviços que eles oferecem',
                    'servicos-realizados' => 'Acompanhe os serviços realizados por período',
                    'assiduidade' => 'Analise a frequência dos clientes',
                    'faturamento-mensal' => 'Monitore o faturamento mensal do seu salão',
                    'origens' => 'Identifique as origens dos seus clientes',
                    'ticket-medio' => 'Calcule o ticket médio por cliente',
                    'horarios-pico' => 'Identifique os horários de maior movimento',
                    'dias-pico' => 'Identifique os dias de maior movimento',
                    'avaliacoes' => 'Avaliações e feedback dos clientes',
                    'ranking-servicos' => 'Veja quais serviços são mais populares',
                    'clientes-novos-antigos' => 'Analise a proporção de clientes novos e antigos',
                    'gerenciar-estoque' => 'Controle o estoque de produtos do seu salão',
                    'gerenciar-comandas' => '',
                    'link-agendamento' => '',
                    'controle-pagamento' => '',                  'controle-pagamento-planos' => 'Gerencie os pagamentos dos planos de assinatura',
                    'planos-de-assinatura' => 'Gerencie os planos de assinatura disponíveis para seus clientes',
                    'balanco-diario' => 'Acompanhe o balanço diário do seu salão',
                    'ajuste-balanco-diario' => 'Faça ajustes no balanço diário para correções financeiras',
                    'balanco-semanal' => '',
                    'controle-agenda' => '',
                    'customizar-home' => '',
                    'meu-pagby' => '',
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
        @if(auth()->user()->hasrole('Proprietário'))
            <p class="text-gray-600 mt-1 text-center">{{ __($subtitles[$tabelaAtiva] ?? '') }}</p>
        @endif 
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
                    @elseif($tabelaAtiva === 'customizar-home')
                        @livewire('proprietario.customizar-home')
                    @elseif($tabelaAtiva === 'link-agendamento')
                        @livewire('create-post')
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
                @elseif($tabelaAtiva === 'appointments' && $menuSelecionado === 'funcionario')
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
                @if($tabelaAtiva === 'appointments' && $menuSelecionado !== 'funcionario')
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

