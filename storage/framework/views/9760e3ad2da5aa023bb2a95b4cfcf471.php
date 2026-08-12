

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
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full bg-white rounded-xl shadow-2xl p-8">
            
            <!-- Cabeçalho -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Registre Seu Negócio</h2>
                <p class="text-gray-600">Crie sua conta e comece a usar o PagBy</p>
                
                <!-- Mostrar plano selecionado se existir -->
                <?php if(isset($selectedPlan) && $selectedPlan === 'trial'): ?>
                <div class="mt-4 bg-gradient-to-r from-pink-500 to-indigo-500 text-white rounded-lg p-4 shadow">
                    <p class="font-semibold text-lg">
                        🎉 Você está começando com <span class="underline">30 dias grátis</span> de teste no PagBy!
                    </p>
                    <p class="text-white/90 mt-1 text-sm">Aproveite todas as funcionalidades sem compromisso.</p>
                </div>
                <?php elseif(isset($selectedPlan)): ?>
                <div class="mt-4 bg-purple-50 border border-purple-200 rounded-lg p-3">
                    <p class="text-purple-800 font-semibold">
                        🎯 Plano selecionado: <span class="capitalize"><?php echo e($selectedPlan); ?></span>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Mensagens de sucesso/erro -->
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo e(session('success')); ?>

                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo e(session('error')); ?>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Erros de validação -->
            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                        <div>
                            <p class="font-semibold mb-2">Corrija os seguintes erros:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="text-sm"><?php echo $error; ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="<?php echo e(route('register-tenant')); ?>" method="POST" class="space-y-6" id="registration-form">
<script>
// Salva e restaura todos os campos principais do formulário no localStorage
document.addEventListener('DOMContentLoaded', function() {
    const fields = [
        'owner_name', 'cpf', 'email', 'phone', 'tenant_name',
        'employee_count', 'cep', 'address', 'neighborhood', 'city', 'state'
    ];
    fields.forEach(function(field) {
        const input = document.getElementById(field);
        if (input) {
            // Preencher campo se houver valor salvo
            const saved = localStorage.getItem('pagby_' + field);
            if (saved) input.value = saved;
            // Salvar alterações
            input.addEventListener('input', function() {
                localStorage.setItem('pagby_' + field, input.value);
            });
        }
    });

    // Restaurar radio "tipo"
    const tipo = localStorage.getItem('pagby_tipo');
    if (tipo) {
        const radio = document.querySelector('input[name="tipo"][value="' + tipo + '"]');
        if (radio) radio.checked = true;
    }
    // Salvar radio "tipo"
    document.querySelectorAll('input[name="tipo"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (radio.checked) {
                localStorage.setItem('pagby_tipo', radio.value);
            }
        });
    });
});
</script>
                <?php echo csrf_field(); ?>
                
                <!-- Hidden field para o plano selecionado -->
                <?php if(isset($selectedPlan)): ?>
                    <input type="hidden" name="selected_plan" value="<?php echo e($selectedPlan); ?>">
                <?php endif; ?>
                <?php if(isset($selectedEmployeeCount)): ?>
                    <input type="hidden" name="selected_employee_count" value="<?php echo e($selectedEmployeeCount); ?>">
                <?php endif; ?>

                <!-- Aceite do contrato -->

                <!-- Seção: Dados do Proprietário -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user text-purple-600 mr-2"></i>
                        Dados do Proprietário
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo *
                            </label>
                            <input type="text" 
                                   id="owner_name" 
                                   name="owner_name" 
                                   value="<?php echo e(old('owner_name')); ?>" 
                                   required 
                                   minlength="3"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Digite seu nome completo">
                            <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                            CPF/CNPJ *
                        </label>
                        <input type="text" 
                               id="cpf" 
                               name="cpf" 
                               value="<?php echo e(old('cpf')); ?>" 
                               required 
                               maxlength="14"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['owner_cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="000.000.000-00">
                        <div id="cpf-feedback" class="mt-1 text-sm"></div>
                        <?php $__errorArgs = ['owner_cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email *
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email')); ?>" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="seu@email.com">
                            <div id="email-feedback" class="mt-1 text-sm"></div>
                            
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone/WhatsApp *
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo e(old('phone')); ?>" 
                                   required 
                                   maxlength="15"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="(11) 99999-9999">
                            <div id="phone-feedback" class="mt-1 text-sm"></div>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Seção: Dados do Negócio -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-store text-purple-600 mr-2"></i>
                        Dados do Negócio
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Tipo de Negócio *
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="radio" 
                                        name="tipo" 
                                        value="Barbearia" 
                                        <?php echo e(old('tipo') == 'Barbearia' ? 'checked' : ''); ?>

                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="ml-3 text-gray-900">✂️ Barbearia</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" 
                                       name="tipo" 
                                       value="Salão de Beleza" 
                                       <?php echo e(old('tipo') == 'Salão de Beleza' ? 'checked' : ''); ?>

                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                <span class="ml-3 text-gray-900">💅 Salão de Beleza</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" 
                                       name="tipo" 
                                       value="Outro" 
                                       <?php echo e(old('tipo') == 'Outro' ? 'checked' : ''); ?>

                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                <span class="ml-3 text-gray-900">✨ Outro</span>
                            </label>
                        </div>
                        <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Número de profissionais removido, agora enviado como campo oculto -->

                    <div>
                        <label for="tenant_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Estabelecimento *
                        </label>
                        <input type="text" 
                               id="tenant_name" 
                               name="tenant_name" 
                               value="<?php echo e(old('tenant_name')); ?>" 
                               required 
                               minlength="2"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['tenant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Nome do seu negócio">
                        <?php $__errorArgs = ['tenant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- CEP -->
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">
                            CEP *
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="cep" 
                                   name="cep" 
                                   value="<?php echo e(old('cep')); ?>" 
                                   required 
                                   maxlength="9"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="00000-000">
                            <div id="cep-loading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-purple-600"></i>
                            </div>
                        </div>
                        <div id="cep-feedback" class="mt-1 text-sm"></div>
                        <?php $__errorArgs = ['cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Endereço *
                            </label>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="<?php echo e(old('address')); ?>" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Rua">
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="number" class="block text-sm font-medium text-gray-700 mb-2">
                                Número *
                            </label>
                            <input type="text" 
                                   id="number" 
                                   name="number" 
                                   value="<?php echo e(old('number')); ?>" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Número">
                            <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
 <div>
                            <label for="complement" class="block text-sm font-medium text-gray-700 mb-2">
                                Complemento (Opcional)
                            </label>
                            <input type="text" 
                                   id="complement" 
                                   name="complement" 
                                   value="<?php echo e(old('complement')); ?>" 
                                   
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['complement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Apto, Sala, etc.">
                            <?php $__errorArgs = ['complement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-2">
                                Bairro *
                            </label>
                            <input type="text" 
                                   id="neighborhood" 
                                   name="neighborhood" 
                                   value="<?php echo e(old('neighborhood')); ?>" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['neighborhood'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Nome do bairro">
                            <?php $__errorArgs = ['neighborhood'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Cidade *
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="<?php echo e(old('city')); ?>" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Nome da cidade">
                            <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado *
                            </label>
                            <select id="state" 
                                    name="state" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-gray-900 bg-white <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="" class="text-gray-500">Selecione o estado</option>
                                <option value="AC" <?php echo e(old('state') == 'AC' ? 'selected' : ''); ?>>Acre</option>
                                <option value="AL" <?php echo e(old('state') == 'AL' ? 'selected' : ''); ?>>Alagoas</option>
                                <option value="AP" <?php echo e(old('state') == 'AP' ? 'selected' : ''); ?>>Amapá</option>
                                <option value="AM" <?php echo e(old('state') == 'AM' ? 'selected' : ''); ?>>Amazonas</option>
                                <option value="BA" <?php echo e(old('state') == 'BA' ? 'selected' : ''); ?>>Bahia</option>
                                <option value="CE" <?php echo e(old('state') == 'CE' ? 'selected' : ''); ?>>Ceará</option>
                                <option value="DF" <?php echo e(old('state') == 'DF' ? 'selected' : ''); ?>>Distrito Federal</option>
                                <option value="ES" <?php echo e(old('state') == 'ES' ? 'selected' : ''); ?>>Espírito Santo</option>
                                <option value="GO" <?php echo e(old('state') == 'GO' ? 'selected' : ''); ?>>Goiás</option>
                                <option value="MA" <?php echo e(old('state') == 'MA' ? 'selected' : ''); ?>>Maranhão</option>
                                <option value="MT" <?php echo e(old('state') == 'MT' ? 'selected' : ''); ?>>Mato Grosso</option>
                                <option value="MS" <?php echo e(old('state') == 'MS' ? 'selected' : ''); ?>>Mato Grosso do Sul</option>
                                <option value="MG" <?php echo e(old('state') == 'MG' ? 'selected' : ''); ?>>Minas Gerais</option>
                                <option value="PA" <?php echo e(old('state') == 'PA' ? 'selected' : ''); ?>>Pará</option>
                                <option value="PB" <?php echo e(old('state') == 'PB' ? 'selected' : ''); ?>>Paraíba</option>
                                <option value="PR" <?php echo e(old('state') == 'PR' ? 'selected' : ''); ?>>Paraná</option>
                                <option value="PE" <?php echo e(old('state') == 'PE' ? 'selected' : ''); ?>>Pernambuco</option>
                                <option value="PI" <?php echo e(old('state') == 'PI' ? 'selected' : ''); ?>>Piauí</option>
                                <option value="RJ" <?php echo e(old('state') == 'RJ' ? 'selected' : ''); ?>>Rio de Janeiro</option>
                                <option value="RN" <?php echo e(old('state') == 'RN' ? 'selected' : ''); ?>>Rio Grande do Norte</option>
                                <option value="RS" <?php echo e(old('state') == 'RS' ? 'selected' : ''); ?>>Rio Grande do Sul</option>
                                <option value="RO" <?php echo e(old('state') == 'RO' ? 'selected' : ''); ?>>Rondônia</option>
                                <option value="RR" <?php echo e(old('state') == 'RR' ? 'selected' : ''); ?>>Roraima</option>
                                <option value="SC" <?php echo e(old('state') == 'SC' ? 'selected' : ''); ?>>Santa Catarina</option>
                                <option value="SP" <?php echo e(old('state') == 'SP' ? 'selected' : ''); ?>>São Paulo</option>
                                <option value="SE" <?php echo e(old('state') == 'SE' ? 'selected' : ''); ?>>Sergipe</option>
                                <option value="TO" <?php echo e(old('state') == 'TO' ? 'selected' : ''); ?>>Tocantins</option>
                            </select>
                            <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Botão de submit -->
                                <!-- Aceite do contrato -->
                                <div class="flex items-start mb-6">
                                    <input type="checkbox" id="contract_accepted" name="contract_accepted" value="1" required class="mt-1 mr-2">
                                    <label for="contract_accepted" class="text-sm text-gray-700 select-none">
                                        Eu li e aceito os <a href="<?php echo e(route('contrato')); ?>" target="_blank" class="text-purple-600 underline hover:text-purple-800">Termos e Contrato da Plataforma PagBy</a> *
                                    </label>
                                </div>
                <div class="pt-6">
                    <button type="submit" 
                            id="submit-btn"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-rocket mr-2"></i>
                        <span id="submit-text">Criar Minha Conta PagBy</span>
                    </button>
                </div>

                <!-- Link para voltar -->
                <div class="text-center pt-4">
                    <a href="<?php echo e(route('home')); ?>" 
                       class="text-gray-600 hover:text-gray-800 transition-colors">
                        ← Voltar para a página inicial
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript para validações e CEP -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos do formulário
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const cepInput = document.getElementById('cep');
            const addressInput = document.getElementById('address');
            const neighborhoodInput = document.getElementById('neighborhood');
            const cityInput = document.getElementById('city');
            const stateSelect = document.getElementById('state');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.getElementById('registration-form');

            // Máscaras
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    if (value.length <= 10) {
                        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    }
                }
                e.target.value = value;
                validatePhone();
            });

            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                }
                e.target.value = value;
                
                if (value.replace(/\D/g, '').length === 8) {
                    searchCEP(value.replace(/\D/g, ''));
                }
            });

            // Validação de email
            emailInput.addEventListener('blur', validateEmail);
            emailInput.addEventListener('input', clearEmailFeedback);

            function validateEmail() {
                const email = emailInput.value;
                const feedback = document.getElementById('email-feedback');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailPattern.test(email)) {
                    feedback.textContent = '❌ Email inválido';
                    feedback.className = 'mt-1 text-sm text-red-600';
                    emailInput.classList.add('border-red-500');
                    return false;
                } else if (email && emailPattern.test(email)) {
                    feedback.textContent = '✅ Email válido';
                    feedback.className = 'mt-1 text-sm text-green-600';
                    emailInput.classList.remove('border-red-500');
                    emailInput.classList.add('border-green-500');
                    return true;
                }
                return true;
            }

            function clearEmailFeedback() {
                const feedback = document.getElementById('email-feedback');
                feedback.textContent = '';
                emailInput.classList.remove('border-red-500', 'border-green-500');
            }

            // Validação de telefone
            function validatePhone() {
                const phone = phoneInput.value.replace(/\D/g, '');
                const feedback = document.getElementById('phone-feedback');
                
                if (phone.length > 0 && phone.length < 10) {
                    feedback.textContent = '❌ Telefone deve ter pelo menos 10 dígitos';
                    feedback.className = 'mt-1 text-sm text-red-600';
                    phoneInput.classList.add('border-red-500');
                    return false;
                } else if (phone.length >= 10) {
                    feedback.textContent = '✅ Telefone válido';
                    feedback.className = 'mt-1 text-sm text-green-600';
                    phoneInput.classList.remove('border-red-500');
                    phoneInput.classList.add('border-green-500');
                    return true;
                } else {
                    feedback.textContent = '';
                    phoneInput.classList.remove('border-red-500', 'border-green-500');
                    return true;
                }
            }

            // Busca CEP
            function searchCEP(cep) {
                const loading = document.getElementById('cep-loading');
                const feedback = document.getElementById('cep-feedback');
                
                loading.classList.remove('hidden');
                feedback.textContent = '';
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');
                        
                        if (data.erro) {
                            feedback.textContent = '❌ CEP não encontrado';
                            feedback.className = 'mt-1 text-sm text-red-600';
                            cepInput.classList.add('border-red-500');
                        } else {
                            feedback.textContent = '✅ CEP encontrado';
                            feedback.className = 'mt-1 text-sm text-green-600';
                            cepInput.classList.remove('border-red-500');
                            cepInput.classList.add('border-green-500');
                            
                            // Preencher campos automaticamente
                            addressInput.value = data.logradouro || '';
                            neighborhoodInput.value = data.bairro || '';
                            cityInput.value = data.localidade || '';
                            stateSelect.value = data.uf || '';
                            
                            // Focar no campo endereço se estiver vazio
                            if (!addressInput.value) {
                                addressInput.focus();
                            }
                        }
                    })
                    .catch(error => {
                        loading.classList.add('hidden');
                        feedback.textContent = '❌ Erro ao buscar CEP';
                        feedback.className = 'mt-1 text-sm text-red-600';
                        console.error('Erro:', error);
                    });
            }

            // Validação do formulário antes do envio
            form.addEventListener('submit', function(e) {
                const isEmailValid = validateEmail();
                const isPhoneValid = validatePhone();
                
                if (!isEmailValid || !isPhoneValid) {
                    e.preventDefault();
                    alert('Por favor, corrija os erros no formulário antes de continuar.');
                    return false;
                }
                
                // Desabilitar botão durante envio
                submitBtn.disabled = true;
                document.getElementById('submit-text').textContent = 'Criando conta...';
            });
        });
    </script>

    <!-- Facebook Pixel - CompleteRegistration Event -->
    <?php if(session('success')): ?>
    <script>
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Lead');
            fbq('track', 'CompleteRegistration');
        }
    </script>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4)): ?>
<?php $attributes = $__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4; ?>
<?php unset($__attributesOriginalfb91d33e41a9f08d1e726d8e14c1b6e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4)): ?>
<?php $component = $__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4; ?>
<?php unset($__componentOriginalfb91d33e41a9f08d1e726d8e14c1b6e4); ?>
<?php endif; ?><?php /**PATH /home/helder/projetos/pagby/resources/views/register-tenant.blade.php ENDPATH**/ ?>