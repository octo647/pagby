
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
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <?php echo e(__('Planos de Assinatura')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
    
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Proprietário')): ?>
        <?php echo $__env->make('includes.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="mb-4 flex justify-end">
        <a href="<?php echo e(route('plans.create')); ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Novo Plano
        </a>
    </div>
    <?php endif; ?>
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 py-2">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-blue-500 text-white">
                                <h4 class="text-center">Plano <?php echo e($plan->name); ?></h4>
                            </div>
                            
                            <div class="card-body px-4 py-3">
                                <p><strong>Mensalidade:</strong> R$ <?php echo e(number_format($plan->price, 2, ',', '.')); ?></p>                          
                                <p><strong>Serviços inclusos:</strong></p>
                                <ul class="list-disc pl-5">
                                <?php if(is_array($plan->services)): ?>
                                    <?php $__currentLoopData = $plan->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e(\App\Models\Service::find($serviceId)->service ?? 'Serviço removido'); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <li> Nenhum serviço incluído</li>
                                <?php endif; ?>
                                </ul>
                                <p><strong>Serviços adicionais:</strong></p>
                                <ul class="list-disc pl-5">
                                <?php if(is_array($plan->additional_services) && count($plan->additional_services) > 0): ?>
                                    <?php $__currentLoopData = $plan->additional_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceName => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $service = \App\Models\Service::find($info['id']);
                                        ?>
                                        <li>
                                            <?php echo e($service->service ?? 'Serviço removido'); ?> 
                                            - Desconto: <?php echo e($info['desconto'] ?? '-'); ?>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <li> Nenhum serviço adicional incluído</li>
                                <?php endif; ?>
</ul>
                                <form method="POST" action="<?php echo e(route('subscriptions.store')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                                    <input type="hidden" name="branch_id" value="<?php echo e($plan->branch_id); ?>">
                                    <input type="hidden" name="user_id" value="<?php echo e(auth()->user()->id); ?>">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Cliente')): ?>
                                    <div class ="mb-4 flex justify-center py-4">                               
                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2">Assinar</button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Proprietário')): ?>
                                <div class="mb-4 py-4 flex justify-center">
                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2"><a href="<?php echo e(route('plans.edit', $plan)); ?>" class="btn btn-warning">Editar</a></button>
                                    <form action="<?php echo e(route('plans.destroy', $plan)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium
                                     rounded-lg text-base inline-flex items-center px-2 py-1.5 text-center mr-2">Excluir</button>
                                </form>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/plans/index.blade.php ENDPATH**/ ?>