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

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Plano Simples e Transparente</h1>
            
            <?php if($tenant->isInTrial()): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">✨ Período de Teste Ativo</p>
                    <p>Seu período de teste expira em: <strong><?php echo e($tenant->trial_ends_at->format('d/m/Y H:i')); ?></strong></p>
                    <p>Restam <strong><?php echo e($tenant->trial_ends_at->diffInDays(now())); ?></strong> dias - Teste com até 5 funcionários!</p>
                </div>
            <?php elseif($tenant->isTrialExpired()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">⏰ Período de Teste Expirado</p>
                    <p>Para continuar usando a plataforma, escolha quantos funcionários você precisa.</p>
                </div>
            <?php elseif($tenant->hasActiveSubscription()): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">✅ <?php echo e($tenant->getSubscriptionStatusDisplay()); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 mb-8">
                <div class="text-6xl font-bold text-gray-900 mb-2">
                    <span class="text-6xl font-bold text-gray-900 mb-2">R$ <?php echo e(number_format($tenant->getCurrentPricePerEmployee(), 2, ',', '.')); ?><span class="text-2xl text-gray-600">/mês</span></span>
                    <?php if($tenant->getCurrentPricePerEmployee() < config('pricing.base_price_per_employee')): ?>
                        <div class="text-lg text-green-600 font-bold mt-2">Promoção: R$ <?php echo e(number_format(config('pricing.promo_price_first_year'), 2, ',', '.')); ?> por funcionário/mês no 1º ano!</div>
                    <?php endif; ?>
                </div>
                <p class="text-xl text-gray-700">por funcionário</p>
                <p class="text-sm text-gray-600 mt-2">Simples assim. Sem taxas ocultas.</p>
            </div>
        </div>

        <!-- Calculadora de Preço -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Escolha o número de funcionários</h2>
            
            <form action="<?php echo e(route('tenant.subscription.select')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label for="employee_count" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantos funcionários você tem?
                    </label>
                    <select id="employee_count" name="employee_count" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                            onchange="updatePrice(this.value)">
                        <?php for($i = 1; $i <= 20; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e($tenant->employee_count == $i ? 'selected' : ''); ?>>
                                <?php echo e($i); ?> funcionário<?php echo e($i > 1 ? 's' : ''); ?>

                            </option>
                        <?php endfor; ?>
                        <option value="20">Mais de 20 funcionários (entre em contato)</option>
                    </select>
                </div>

                <div class="bg-blue-50 rounded-lg p-6 border-2 border-blue-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg text-gray-700">Valor mensal:</span>
                        <span id="monthly-price" class="text-3xl font-bold text-blue-600">
                            <span id="monthly-price">
                                R$ <?php echo e(number_format($tenant->employee_count * $tenant->getCurrentPricePerEmployee(), 2, ',', '.')); ?>

                                <?php if($tenant->getCurrentPricePerEmployee() < config('pricing.base_price_per_employee')): ?>
                                    <span class="text-base text-green-600">(promoção 1º ano)</span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>✓ <span id="employee-text"><?php echo e($tenant->employee_count); ?> funcionário<?php echo e($tenant->employee_count > 1 ? 's' : ''); ?></span></p>
                        <p>✓ Todos os recursos inclusos</p>
                        <p>✓ Sem limite de agendamentos</p>
                        <p>✓ Suporte prioritário</p>
                    </div>
                </div>

                <?php if(!$tenant->hasActiveSubscription() || $tenant->isTrialExpired()): ?>
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition duration-200 text-lg">
                        <?php echo e($tenant->isInTrial() ? 'Assinar Agora' : 'Ativar Plano'); ?>

                    </button>
                <?php else: ?>
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-gray-900 hover:bg-gray-800 transition duration-200 text-lg">
                        Alterar Número de Funcionários
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Recursos -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tudo incluso em todos os planos</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <?php $__currentLoopData = config('pricing.features'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700"><?php echo e($feature); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <?php if($tenant->isInTrial()): ?>
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="font-bold text-yellow-900 mb-2">💡 Aproveite seu período de teste!</h3>
            <p class="text-yellow-800">Durante os <?php echo e(config('pricing.trial.duration_days', 30)); ?> dias de trial, você pode testar com até <?php echo e(config('pricing.trial.max_employees', 5)); ?> funcionários gratuitamente. Depois, pague apenas pelo que usar!</p>
        </div>
        <?php endif; ?>

        <div class="text-center mt-8">
            <p class="text-gray-600 mb-2">Precisa de um plano personalizado?</p>
            <a href="mailto:suporte@pagby.com.br" class="text-blue-500 hover:text-blue-600 font-semibold">
                Entre em contato conosco
            </a>
        </div>
    </div>
</div>

<script>
function updatePrice(employeeCount) {
    const pricePerEmployee = <?php echo json_encode($tenant->getCurrentPricePerEmployee(), 15, 512) ?>;
    const basePrice = <?php echo json_encode(config('pricing.base_price_per_employee'), 15, 512) ?>;
    let totalPrice = 0;
    let promoText = '';
    employeeCount = parseInt(employeeCount);
    if (employeeCount > 0) {
        totalPrice = employeeCount * pricePerEmployee;
        if (pricePerEmployee < basePrice) {
            promoText = ' (promoção 1º ano)';
        } else {
            promoText = '';
        }
    } else {
        totalPrice = 0;
        promoText = '';
    }
    document.getElementById('monthly-price').innerHTML =
        'R$ ' + totalPrice.toFixed(2).replace('.', ',') + '<span class="text-base text-green-600">' + promoText + '</span>';
    document.getElementById('employee-text').textContent =
        employeeCount + ' funcionário' + (employeeCount > 1 ? 's' : '');
}
</script>
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
<?php /**PATH /var/www/pagby/resources/views/tenant/subscription/plans.blade.php ENDPATH**/ ?>