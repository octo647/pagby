

<?php if (isset($component)) { $__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagby-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagby-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-3xl mx-auto">
            <!-- Plano Selecionado -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="mb-6">
                    <h1 class="text-4xl font-bold mb-2 text-gray-800">
                        <?php echo e($planData['name']); ?>

                    </h1>
                    <p class="text-gray-600">Assinatura única por funcionário</p>
                </div>
                
                <div class="text-center mb-8">
                    <div class="text-6xl font-bold text-purple-600 mb-2">
                        R$ <?php echo e(number_format($planData['price'], 2, ',', '.')); ?>

                        <span class="text-xl text-gray-500 font-normal">/mês</span>
                    </div>
                    <div class="text-xl font-bold text-purple-600 mb-2">
                        (por profissional)                       
                    </div>
                    <p class="text-lg text-gray-600"><?php echo e($planData['description']); ?></p>
                </div>

                <!-- Benefícios do plano -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        ✨ O que está incluído:
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 text-left">
                        <?php $__currentLoopData = config('pricing.features'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-check mr-2"></i>
                                <span><?php echo e($feature); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>


                <!-- Botões de ação -->
                <div class="flex flex-col gap-4">
                    
                    <form action="<?php echo e(route('tenant.renew')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan" value="pagby-unico">
                        <input type="hidden" name="tenant_id" value="<?php echo e(tenant()->id); ?>">
                        
                        <button type="submit" 
                                class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                            🚀 Confirmar pagamento
                        </button>
                    </form>


                    
                    <a href="<?php echo e(route('home')); ?>#planos" 
                       class="text-gray-600 hover:text-gray-800 transition-colors">
                        ← Voltar aos planos
                    </a>
                </div>
            </div>

            <!-- Garantia e Segurança -->
            <div class="grid md:grid-cols-2 gap-6 text-white flex-center">
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-shield-alt text-2xl text-green-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Segurança Total</h4>
                    <p class="text-sm text-gray-300">Seus dados protegidos com criptografia de ponta</p>
                </div>
               
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-headset text-2xl text-purple-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Suporte Dedicado</h4>
                    <p class="text-sm text-gray-300">Equipe especializada para te ajudar</p>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4)): ?>
<?php $attributes = $__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4; ?>
<?php unset($__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4)): ?>
<?php $component = $__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4; ?>
<?php unset($__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4); ?>
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/pagby-subscription/select-plan.blade.php ENDPATH**/ ?>