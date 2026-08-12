<?php $__env->startSection('title', 'Minhas Recompensas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">🎁 Minhas Recompensas</h1>

    
    <?php if($saldoCreditos > 0): ?>
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 mb-6 shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Saldo Total em Créditos</p>
                    <p class="text-4xl font-bold mt-1">R$ <?php echo e(number_format($saldoCreditos, 2, ',', '.')); ?></p>
                    <p class="text-sm opacity-75 mt-2">Use em qualquer serviço!</p>
                </div>
                <div class="text-6xl">💰</div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($recompensas->isEmpty()): ?>
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-600 mb-4">Você ainda não tem recompensas.</p>
            <p class="text-sm text-gray-500">Compre produtos e ganhe bônus para usar em serviços!</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $recompensas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 
                            <?php echo e($reward->tipo === 'credito' ? 'border-green-500' : 
                               ($reward->tipo === 'cupom' ? 'border-blue-500' : 'border-purple-500')); ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">
                                    <?php echo e($reward->tipo === 'credito' ? '💰' : 
                                       ($reward->tipo === 'cupom' ? '🏷️' : '✂️')); ?>

                                </span>
                                <h3 class="text-xl font-bold text-gray-800">
                                    <?php echo e($reward->descricao_formatada); ?>

                                </h3>
                            </div>
                            
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>📅 Válido até: <?php echo e($reward->validade->format('d/m/Y')); ?></p>
                                <p>⏰ <?php echo e($reward->validade->diffForHumans()); ?></p>
                                <?php if($reward->origem): ?>
                                    <p class="text-xs text-gray-500">
                                        Origem: <?php echo e(match($reward->origem) {
                                            'compra_produto' => 'Compra de produto',
                                            'promocao' => 'Promoção especial',
                                            'indicacao' => 'Indicação de amigo',
                                            'aniversario' => 'Presente de aniversário',
                                            default => ucfirst($reward->origem)
                                        }); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition"
                                onclick="alert('Funcionalidade de uso de recompensas será implementada no checkout!')">
                            Usar Agora
                        </button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <h4 class="font-semibold text-blue-900 mb-2">💡 Como ganhar mais recompensas?</h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>✅ Compre produtos durante seus atendimentos</li>
            <li>✅ Ganhe até 25% do valor em créditos</li>
            <li>✅ Use os créditos em qualquer serviço</li>
            <li>✅ Cupons e bônus especiais em promoções</li>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.cliente', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/pagby/resources/views/cliente/recompensas.blade.php ENDPATH**/ ?>