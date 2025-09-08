<x-app-layout>
    <x-slot name="header">
    
        <h2 class="text-xl font-semibold leading-tight text-center text-gray-800">
        @if(auth()->user()->hasrole('Admin'))
            {{ __($tabelaAtiva === 'contatos' ? 'Contatos' : 
            ($tabelaAtiva === 'saloes' ? 'Salões' : 
            ($tabelaAtiva === 'planos' ? 'Planos' : '')))}}
        @elseif(auth()->user()->hasrole('Proprietário'))
            {{ __($tabelaAtiva === 'usuarios' ? 'Usuários' : 
            ($tabelaAtiva === 'filiais' ? 'Filiais' :
            ($tabelaAtiva === 'funcionarios' ? 'Funcionários por filial' : ($tabelaAtiva === 'horarios' ? 'Horários dos Funcionários' : ($tabelaAtiva === 'servicos' ? 'Serviços oferecidos' : ($tabelaAtiva === 'func_serv' ? 'Funcionários e Serviços' : ($tabelaAtiva === 'servicos-realizados' ? 'Serviços Realizados' :
            ($tabelaAtiva === 'assiduidade' ? 'Assiduidade dos Clientes' : ($tabelaAtiva === 'faturamento-mensal' ? 'Faturamento Mensal' : ($tabelaAtiva === 'origens' ? 'Origens dos Clientes' : ($tabelaAtiva === 'ticket-medio' ? 'Ticket Médio por Cliente' : ($tabelaAtiva === 'horarios-pico' ? 'Horários de Pico' : ($tabelaAtiva === 'dias-pico' ? 'Dias de Pico' : ($tabelaAtiva === 'avaliacoes' ? 'Satisfação dos Clientes' : 
            ($tabelaAtiva === 'ranking-servicos' ? 'Serviços mais Solicitados' : ($tabelaAtiva === 'clientes-novos-antigos' ? 'Clientes Novos e Antigos':'')))))))))))))))) }}
        @elseif(auth()->user()->hasrole('Funcionário'))
            {{ __($tabelaAtiva === 'agenda' ? 'Minha Agenda' : 
            ($tabelaAtiva === 'servicos' ? 'Meus Serviços' : 
            ($tabelaAtiva === 'servicos-realizados' ? 'Serviços Realizados' : 
            ($tabelaAtiva === 'horarios' ? 'Meus Horários' :
            ($tabelaAtiva === 'estatisticas' ? 'Estatísticas Pessoais' : 
            ($tabelaAtiva === 'ranking-servicos' ? 'Ranking de Serviços' : 
            ($tabelaAtiva === 'avaliacoes-profissional' ? 'Avaliações dos Clientes' : 
            ($tabelaAtiva === 'dias-pico' ? 'Dias de Pico' : 
            ($tabelaAtiva === 'horarios-pico' ? 'Horários de Pico' :
            ''))))))))) }}
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
    
                @if($tabelaAtiva === 'contatos')
                    @livewire('list-contacts') 
                @elseif($tabelaAtiva === 'saloes')
                    @livewire('admin.saloes')
                @elseif($tabelaAtiva === 'planos')
                    @livewire('admin.planos')
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
                    @elseif($tabelaAtiva === 'horarios')
                        @livewire('proprietario.salon-times')
                    @elseif($tabelaAtiva === 'servicos')
                        @livewire('proprietario.services')
                    @elseif($tabelaAtiva === 'func_serv')
                        @if(request()->input('funcionario_id'))
                            @livewire('proprietario.employee-service')
                        @else
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Funcionários x Serviços</h3>
                                <p class="text-gray-600 mb-4">Selecione um funcionário para gerenciar seus serviços:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @if(isset($employees) && $employees->count() > 0)
                                        @foreach($employees as $employee)
                                            <a href="{{ route('tenant.dashboard', ['tabelaAtiva' => 'func_serv', 'funcionario_id' => $employee->id]) }}" 
                                               class="block p-4 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                                                <h4 class="font-semibold text-gray-900">{{ $employee->name }}</h4>
                                                <p class="text-gray-600 text-sm">Clique para gerenciar serviços</p>
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">Nenhum funcionário encontrado.</p>
                                    @endif
                                </div>
                            </div>
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
                    @elseif($tabelaAtiva === 'relatorio-geral')
                        @livewire('proprietario.relatorio-geral')
                    @elseif($tabelaAtiva === 'relatorio-mensal')
                        @livewire('proprietario.relatorio-mensal')
                    @elseif($tabelaAtiva === 'relatorio-anual')
                        @livewire('proprietario.relatorio-anual')
                    @elseif($tabelaAtiva === 'planos-de-assinatura')
                        @livewire('planos-de-assinatura')
                    @elseif($tabelaAtiva === 'meu-pixby')
                        @livewire('proprietario.meu-pixby')
                    @endif  
            </div>              
                
                @endcan


                @can('Funcionário')
                @if($tabelaAtiva === 'agenda')
                        @livewire('funcionario.agenda')
                @elseif($tabelaAtiva === 'servicos')
                        @livewire('funcionario.servicos')
                @elseif($tabelaAtiva === 'servicos-realizados')
                        @livewire('funcionario.servicos-realizados')
                @elseif($tabelaAtiva === 'horarios')
                        @livewire('funcionario.horarios')
                @elseif($tabelaAtiva === 'estatisticas')
                        @livewire('funcionario.estatisticas')
                @elseif($tabelaAtiva === 'avaliacoes-profissional')
                        @livewire('funcionario.avaliacoes-profissional')
                @elseif($tabelaAtiva === 'dias-pico')
                        @livewire('dias-pico')
                @elseif($tabelaAtiva === 'horarios-pico')
                        @livewire('horarios-pico')
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

