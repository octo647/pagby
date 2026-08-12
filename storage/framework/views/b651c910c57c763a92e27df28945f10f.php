<div class="min-h-screen bg-gray-50">
    
    
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        <!--[if BLOCK]><![endif]--><?php if(session()->has('assinatura-valida')): ?>
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-green-800 font-medium"><?php echo e(session('assinatura-valida')); ?></p>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]--> 

        <!--[if BLOCK]><![endif]--><?php if(session('warning')): ?>
            <div class="mb-6 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <p class="text-yellow-800 font-medium"><?php echo e(session('warning')); ?></p>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if(empty($available_times)): ?>
            
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Selecione um funcionário e os serviços</h3>
                <p class="text-gray-500">Para ver os dias disponíveis, primeiro escolha o profissional e os serviços desejados.</p>
            </div>
        <?php else: ?>            
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['auth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo e($message); ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['subscription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo $message; ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selected_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo e($message); ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selected_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo e($message); ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['services'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo e($message); ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['professional'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?php echo e($message); ?></p>
                    </div>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

            
            <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
                <!--[if BLOCK]><![endif]--><?php if(!empty($recompensasDisponiveis)): ?>
                    <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 border-2 border-purple-200 shadow-md">
                        <div class="flex items-start gap-3">
                            <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-full p-2 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">
                                    🎉 Você tem <?php echo e(count($recompensasDisponiveis)); ?> recompensa(s) disponível(is)!
                                </h3>
                                <div class="space-y-2">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recompensasDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recompensa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-purple-200">
                                            <div class="flex items-center gap-3">
                                                <span class="text-2xl">💰</span>
                                                <div>
                                                    <p class="font-bold text-green-600 text-lg">
                                                        R$ <?php echo e(number_format($recompensa['valor_credito'], 2, ',', '.')); ?>

                                                    </p>
                                                    <p class="text-xs text-gray-600">
                                                        Válido até: <?php echo e($recompensa['validade']); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            <!--[if BLOCK]><![endif]--><?php if($recompensa['dias_restantes'] <= 7): ?>
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                                    ⏰ <?php echo e($recompensa['dias_restantes']); ?> <?php echo e($recompensa['dias_restantes'] == 1 ? 'dia' : 'dias'); ?>

                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <p class="text-sm text-purple-700 mt-3 font-medium">
                                    💡 Você poderá selecionar qual recompensa usar após escolher o horário do agendamento
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">📅 Dias Disponíveis</h2>
                    <p class="text-gray-600">Selecione um dia para ver os horários disponíveis</p>
                </div>
                <?php
                    $dias_pt = [
                        'monday' => 'segunda',
                        'tuesday' => 'terca',
                        'wednesday' => 'quarta',
                        'thursday' => 'quinta',
                        'friday' => 'sexta',
                        'saturday' => 'sabado',
                        'sunday' => 'domingo',
                    ];         
                ?>
                
                <!--[if BLOCK]><![endif]--><?php if(!empty($forward_days)): ?>
                    <?php
                        // Verifica se todos os serviços escolhidos estão incluídos no plano
                        $all_included = !empty($this->chosen_service_ids) && empty(array_diff($this->chosen_service_ids, $plan_services));
                    ?>
                    
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-4 justify-items-center">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $forward_days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $weekday_en = strtolower(\Carbon\Carbon::parse($day)->format('l'));
                                $weekday_pt = $dias_pt[$weekday_en] ?? $weekday_en;
                                $is_allowed = in_array($weekday_pt, $allowed_days ?? []);
                                
                                $show_as_allowed = $all_included ? $is_allowed : true;
                                
                                // Verificar se já existe agendamento neste dia
                                $has_appointment = in_array($day, $days_with_appointments ?? []);
                                
                            ?>
                            
                            <?php $sem_horario = empty($available_times[$day] ?? []); ?>
                            <button 
                                <?php if($sem_horario): ?>
                                    disabled
                                    title="Sem horários disponíveis"
                                <?php else: ?>
                                    wire:click="$set('selected_day', '<?php echo e($day); ?>')"
                                <?php endif; ?>
                                class="w-20 h-20 sm:w-24 sm:h-24 flex flex-col items-center justify-center rounded-2xl border-2 transition-all duration-300 transform
                                    <?php echo e($sem_horario ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed opacity-60' :
                                        ($has_appointment ? 'border-red-300 bg-red-50 text-red-700 cursor-not-allowed' :
                                        ((isset($selected_day) && $selected_day === $day) ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg scale-105' :
                                        ($show_as_allowed ? 'border-gray-200 hover:border-blue-300 hover:bg-blue-50 bg-white shadow-sm' : 'border-gray-200 bg-gray-50 text-gray-400 line-through')))); ?>"
                            >
                                <span class="font-bold text-base"><?php echo e(\Carbon\Carbon::parse($day)->format('d')); ?></span>
                                <span class="text-xs uppercase"><?php echo e(\Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('MMM')); ?></span>
                                <span class="text-xs"><?php echo e(\Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('ddd')); ?></span>
                                <!--[if BLOCK]><![endif]--><?php if($sem_horario): ?>
                                    <span class="text-xs font-bold mt-1">SEM HORÁRIO</span>
                                <?php elseif($has_appointment): ?>
                                    <span class="text-xs font-bold mt-1">OCUPADO</span>
                                <?php elseif(!$show_as_allowed): ?>
                                    <span class="text-xs font-bold mt-1">PAGO</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">Nenhum dia disponível</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            
            
            <!--[if BLOCK]><![endif]--><?php if(isset($selected_day) && !empty($available_times[$selected_day])): ?>
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">🕐 Horários Disponíveis</h2>
                        <p class="text-gray-600">
                            Para <?php echo e(\Carbon\Carbon::parse($selected_day)->format('d/m/Y')); ?>

                            (<?php echo e(\Carbon\Carbon::parse($selected_day)->locale('pt_BR')->isoFormat('dddd')); ?>)
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-4 justify-items-center">
                        <!--[if BLOCK]><![endif]--><?php if(!empty($available_times[$selected_day])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $available_times[$selected_day]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button 
                                    wire:click="selectTime('<?php echo e($slot[0]); ?>')"
                                    class="w-20 h-16 flex flex-col items-center justify-center rounded-xl border-2 transition-all duration-300 transform hover:scale-105
                                    <?php echo e((isset($selected_time) && $selected_time === $slot[0]) ? 'bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg border-green-400 scale-105' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50 bg-white shadow-sm'); ?>">
                                    <svg class="w-4 h-4 mb-1 <?php echo e((isset($selected_time) && $selected_time === $slot[0]) ? 'text-white' : 'text-gray-400'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-semibold"><?php echo e($slot[0]); ?></span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php else: ?>
                            <div class="col-span-full text-center py-8">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500">Nenhum horário disponível</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            
            
            <!--[if BLOCK]><![endif]--><?php if(isset($selected_time) && $selected_time): ?>
                
                <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
                    <!--[if BLOCK]><![endif]--><?php if(!empty($recompensasAtivasCompletas)): ?>
                        <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg">
                            
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800">
                                        🎉 Suas Recompensas Ativas
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Você tem <?php echo e(count($recompensasAtivasCompletas)); ?> recompensa(s) disponível(is). Clique para usar!
                                    </p>
                                </div>
                            </div>

                            
                            <div class="space-y-3">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recompensasAtivasCompletas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $recompensa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div wire:click="toggleRecompensa(<?php echo e($recompensa['id']); ?>)" 
                                         class="cursor-pointer transition-all duration-200 
                                                <?php echo e(in_array($recompensa['id'], $recompensasSelecionadas)
                                                    ? 'bg-white border-purple-500 shadow-lg scale-[1.02] ring-2 ring-purple-300' 
                                                    : 'bg-white border-gray-200 hover:border-purple-300 hover:shadow-md'); ?>

                                                border-2 rounded-lg p-4">
                                        
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    
                                                    <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center
                                                                <?php echo e(in_array($recompensa['id'], $recompensasSelecionadas)
                                                                    ? 'bg-purple-600 border-purple-600' 
                                                                    : 'bg-white border-gray-300'); ?>">
                                                        <!--[if BLOCK]><![endif]--><?php if(in_array($recompensa['id'], $recompensasSelecionadas)): ?>
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>

                                                    
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border bg-green-100 text-green-800 border-green-300">
                                                        💰 Crédito
                                                    </span>

                                                    
                                                    <!--[if BLOCK]><![endif]--><?php if($recompensa['dias_restantes'] <= 7): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                                            ⏰ Você tem <?php echo e($recompensa['dias_restantes']); ?> <?php echo e($recompensa['dias_restantes'] == 1 ? 'dia' : 'dias'); ?> para aproveitar
                                                        </span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    
                                                    
                                                    <!--[if BLOCK]><![endif]--><?php if($index === 0): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold border border-orange-300">
                                                            🔥 Será usada primeiro
                                                        </span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>

                                                <div class="ml-8">
                                                    <p class="text-2xl font-bold text-green-600">
                                                        R$ <?php echo e(number_format($recompensa['valor_credito'], 2, ',', '.')); ?>

                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        Use este crédito para pagar ou ter desconto em serviços
                                                    </p>

                                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                                        <span>📅 Válido até: <?php echo e($recompensa['validade']); ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <!--[if BLOCK]><![endif]--><?php if(in_array($recompensa['id'], $recompensasSelecionadas)): ?>
                                                <div class="ml-4 flex-shrink-0">
                                                    <div class="bg-purple-100 rounded-full px-3 py-1">
                                                        <span class="text-purple-700 font-semibold text-sm">✓ Selecionada</span>
                                                    </div>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <!--[if BLOCK]><![endif]--><?php if(count($recompensasSelecionadas) > 0): ?>
                                <?php
                                    $totalDesconto = collect($recompensasAtivasCompletas)
                                        ->whereIn('id', $recompensasSelecionadas)
                                        ->where('tipo', 'credito')
                                        ->sum('valor_credito');
                                ?>
                                <div class="mt-4 bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-400 p-4 rounded-lg shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-green-800 font-medium">
                                                ✅ <?php echo e(count($recompensasSelecionadas)); ?> recompensa(s) selecionada(s)
                                            </p>
                                            <p class="text-xs text-green-700 mt-1">
                                                🔥 Serão aplicadas por ordem de validade (as que expiram primeiro)
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-green-700 font-medium">Total de desconto:</p>
                                            <p class="text-2xl font-bold text-green-600">
                                                R$ <?php echo e(number_format($totalDesconto, 2, ',', '.')); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-4 bg-purple-100 border-l-4 border-purple-500 p-3 rounded">
                                    <p class="text-sm text-purple-800">
                                        <strong>💡 Dica:</strong> Você pode selecionar múltiplas recompensas! 
                                        Os descontos serão somados e aplicados automaticamente ao confirmar.
                                    </p>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($ch_professional && !empty($ch_services)): ?>
                    <div wire:key="produtos-wrapper-<?php echo e($selected_time); ?>" class="mb-6">
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cliente.sugestoes-produtos', [
                            'serviceId' => $ch_services[0] ?? null,
                            'branchId' => $ch_professional->branches->first()?->id ?? null,
                        ]);

$__html = app('livewire')->mount($__name, $__params, 'sugestoes-produtos-' . ($ch_services[0] ?? 'default') . '-' . str_replace(':', '', $selected_time), $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            
            <!--[if BLOCK]><![endif]--><?php if(isset($selected_time) && $selected_time): ?>

                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Horário Selecionado</h3>
                    <p class="text-gray-600 mb-2">
                        <?php echo e(\Carbon\Carbon::parse($selected_day)->format('d/m/Y')); ?> às <?php echo e($selected_time); ?>

                    </p>
                    
                    
                    <!--[if BLOCK]><![endif]--><?php if($ch_professional && !empty($ch_services)): ?>
                        <div class="bg-white rounded-lg p-4 mb-6 border border-green-100">
                            <div class="text-sm text-gray-600 space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-semibold">Profissional:</span>
                                    <span><?php echo e($ch_professional->name); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Serviço(s):</span>
                                    <span><?php echo e(count($ch_services)); ?> selecionado(s)</span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if(count($produtosSelecionados ?? []) > 0): ?>
                                    <div class="flex justify-between text-blue-600">
                                        <span class="font-semibold">Produtos:</span>
                                        <span><?php echo e(count($produtosSelecionados)); ?> selecionado(s)</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if(!empty($recompensasSelecionadas)): ?>
                                    <div class="flex justify-between text-purple-600">
                                        <span class="font-semibold">Recompensas:</span>
                                        <span><?php echo e(count($recompensasSelecionadas)); ?> aplicada(s)</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <button 
                        wire:click="confirmTime" 
                        class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirmar Agendamento
                    </button>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    
        <?php
        $__scriptKey = '1503486452-0';
        ob_start();
    ?>
    <script>
        // Processar dados restaurados após o componente carregar
        document.addEventListener('livewire:init', () => {
            const hasRestoredData = <?php echo \Illuminate\Support\Js::from($ch_professional_id && $ch_services && count($ch_services) > 0)->toHtml() ?>;
            
            if (hasRestoredData) {
                console.log('✅ Restored booking data detected');
                console.log('Professional ID:', <?php echo \Illuminate\Support\Js::from($ch_professional_id)->toHtml() ?>);
                console.log('Services:', <?php echo \Illuminate\Support\Js::from($ch_services)->toHtml() ?>);
                console.log('Selected day:', <?php echo \Illuminate\Support\Js::from($selected_day)->toHtml() ?>);
                console.log('Selected time:', <?php echo \Illuminate\Support\Js::from($selected_time)->toHtml() ?>);
                
                // Aguardar renderização e então forçar recálculo
                setTimeout(() => {
                    console.log('🔄 Calling forceRecalculate()...');
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('forceRecalculate');
                }, 200);
            } else {
                console.log('ℹ️ No restored booking data found');
            }
        });
    </script>
        <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
</div>
<?php /**PATH /home/helder/projetos/pagby/resources/views/livewire/cliente/make-appointment.blade.php ENDPATH**/ ?>