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
    <header class="w-full py-6 bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700 shadow">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">     
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo PagBy" class="w-12 h-12 rounded-full object-cover border-2 border-pink-400 shadow">          
                <span class="text-3xl font-extrabold text-white tracking-wide">PagBy</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="<?php echo e(route('login')); ?>" class="bg-white text-pink-700 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-100 transition">Entrar</a>
                <a href="<?php echo e(route('register')); ?>" class="bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-700 transition">Registrar</a>
            </div>
        </div>   
    </header>
    <main class="flex-1 flex flex-col items-center justify-center text-center bg-gray-900 px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-4 text-pink-600 drop-shadow">Planos Pixby </h1>   
        <p class="text-lg text-gray-300">Descubra todas as funcionalidades que o Pixby oferece para otimizar a gestão do seu negócio.</p>   
    </main>
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-2"><?php echo e($plan->name); ?></h2>
                    <p class="text-gray-700 mb-4">Mensalidade: R$ <?php echo e(number_format($plan->price, 2, ',', '.')); ?></p>
                    <p class="text-gray-700 mb-4">Duração: <?php echo e($plan->duration_days); ?> dias</p>
                    <p class="text-gray-700 mb-4">Serviços: <?php echo e(implode(', ', $plan->services->pluck('service')->toArray())); ?></p>
                    <a href="<?php echo e(route('plans.show', $plan)); ?>" class="text-pink-600 hover:underline">Ver Detalhes</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/planos/index.blade.php ENDPATH**/ ?>