<?php
    $menuSelecionado = request()->input('menu');
?>
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
    
        <h2 class="text-2xl font-semibold leading-tight text-center text-gray-800">
        <?php if(auth()->user()->hasrole('Admin')): ?>
            <?php echo e(__($tabelaAtiva === 'contatos' ? 'Contatos' : 
            ($tabelaAtiva === 'contatos-booksy' ? 'Contatos Booksy' :
            ($tabelaAtiva === 'saloes' ? 'Salões' : 
            ($tabelaAtiva === 'planos' ? 'Planos' :
            ($tabelaAtiva === 'ajustes-planos' ? 'Ajustes de Planos' : '')))))); ?>

        <?php elseif(auth()->user()->hasrole('Proprietário')): ?>
            <?php
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
                    'dashboard-fidelidade' => 'Fidelidade & Produtos',
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
                    'dashboard-fidelidade' => 'Gerencie vínculos de produtos com serviços e rewards de fidelidade',
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
            ?>

        <?php echo e(__($titles[$tabelaAtiva] ?? '')); ?>


        <?php elseif(auth()->user()->hasrole('Funcionário')): ?>
            <?php
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
            ?>
            <?php echo e(__($funcionarioTitles[$tabelaAtiva] ?? '')); ?>

        <?php elseif(auth()->user()->hasrole('Cliente')): ?>
            <?php echo e(__($tabelaAtiva === 'appointments' ? 'Agendamentos' :
            ($tabelaAtiva === 'historico' ? 'Histórico de Serviços':
            ($tabelaAtiva === 'notificacoes' ? 'Notificações' : '')))); ?>

        <?php else: ?>
            <?php echo e(__('Dashboard')); ?>

        <?php endif; ?>
            
        </h2> 
        <?php if(auth()->user()->hasrole('Proprietário')): ?>
            <p class="text-gray-600 mt-1 text-center"><?php echo e(__($subtitles[$tabelaAtiva] ?? '')); ?></p>
        <?php endif; ?> 
      <?php $__env->endSlot(); ?>
<script>
    window.addEventListener('error', function(e) {
        alert('Erro: ' + e.message);
    });
</script>

    <div class="py-1">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">

                <?php echo $__env->make('includes.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(session('chooseone')): ?>
                <?php echo e(session('chooseone')); ?>

                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Admin')): ?>
                <?php if($tabelaAtiva === 'contatos-booksy'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.booksy-contacts');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> 
                <?php elseif($tabelaAtiva === 'contatos'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('list-contacts');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> 
                <?php elseif($tabelaAtiva === 'saloes'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.saloes');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'planos'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.planos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'ajustes-planos'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.plan-adjustments');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-4', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>

                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Proprietário')): ?>
               
                                                           
                
                    <?php if($tabelaAtiva === 'usuarios'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('salon-users');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-5', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'filiais'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.filiais');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-6', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'funcionarios'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.branch-users');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-7', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'horarios' && $menuSelecionado === 'proprietario'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.salon-times');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-8', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'servicos' && $menuSelecionado === 'proprietario'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.services');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-9', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'func_serv'): ?>
                        <?php if(request()->input('funcionario_id')): ?>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.employee-service');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-10', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php else: ?>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.employee-selector');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-11', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        <?php endif; ?>
                    <?php elseif($tabelaAtiva === 'servicos-realizados'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.servicos-realizados');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-12', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'assiduidade'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.assiduidade');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-13', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>   
                    <?php elseif($tabelaAtiva === 'faturamento-mensal'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.faturamento-mensal');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-14', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'origens'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.origens');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-15', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'ticket-medio'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.ticket-medio');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-16', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'horarios-pico'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('horarios-pico');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-17', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'dias-pico'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dias-pico');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-18', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>  
                    <?php elseif($tabelaAtiva === 'avaliacoes'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.avaliacoes');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-19', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> 
                    <?php elseif($tabelaAtiva === 'ranking-servicos'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.ranking-servicos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-20', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'clientes-novos-antigos'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.clientes-novos-antigos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-21', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'balanco-diario'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.balanco-diario');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-22', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'ajuste-balanco-diario'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.ajuste-balanco-diario');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-23', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'gerenciar-estoque'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.gerenciar-estoque');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-24', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'controle-agenda'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.controle-agenda');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-25', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'controle-pagamento'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.controle-pagamento');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-26', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'controle-pagamento-planos'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.controle-pagamento-planos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-27', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'gerenciar-comandas'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.gerenciar-comandas');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-28', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'dashboard-fidelidade'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.dashboard-fidelidade');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-29', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'relatorio-geral'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.relatorio-geral');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-30', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'relatorio-mensal'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.relatorio-mensal');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-31', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'relatorio-anual'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.relatorio-anual');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-32', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'planos-de-assinatura'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('planos-de-assinatura');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-33', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'meu-pagby'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.meu-pagby');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-34', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'customizar-home'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.customizar-home');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-35', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'link-agendamento'): ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('create-post');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-36', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <?php elseif($tabelaAtiva === 'gerenciar-comandas'): ?>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h1 class="text-3xl font-bold text-gray-900">Controle de Comandas</h1>
                            </div>
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.gerenciar-comandas');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-37', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                    <?php endif; ?>  
            </div>              
                
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Funcionário')): ?>
                <?php if($tabelaAtiva === 'agenda'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.agenda');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-38', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'appointments' && $menuSelecionado === 'funcionario'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.agenda');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-39', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'servicos' && $menuSelecionado === 'funcionario'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.servicos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-40', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'servicos-planos'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.controle-pagamento-planos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-41', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'servicos-funcionario-realizados'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.servicos-funcionario-realizados');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-42', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'horarios' && $menuSelecionado === 'funcionario'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.horarios');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-43', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'estatisticas'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.estatisticas');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-44', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'avaliacoes-profissional'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.avaliacoes-profissional');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-45', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
               
                <?php elseif($tabelaAtiva === 'servicos-avulsos'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('funcionario.controle-ganhos-avulsos');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-46', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>

                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Cliente')): ?>
                <?php if($tabelaAtiva === 'appointments' && $menuSelecionado !== 'funcionario'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.appointments');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-47', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?> 
                <?php elseif($tabelaAtiva === 'historico'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.historico');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-48', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
               
                <?php elseif($tabelaAtiva === 'notificacoes'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.notificacoes');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-49', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php elseif($tabelaAtiva === 'planos-de-assinatura'): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('planos-de-assinatura');

$__html = app('livewire')->mount($__name, $__params, 'lw-4138052474-50', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>
                
                <?php endif; ?>
                

               
               

            </div>
        </div>


    </div>

   

    </div>


   
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>

<?php /**PATH /home/helder/projetos/pagby/resources/views/dashboard.blade.php ENDPATH**/ ?>