<?php $__env->startSection('title', 'Gerenciar Produtos Recomendados'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('proprietario.gerenciar-produtos-servicos', [
        'serviceId' => $serviceId,
        'branchId' => auth()->user()->branches->first()?->id
    ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3766674748-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.proprietario', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/pagby/resources/views/proprietario/produtos-servicos.blade.php ENDPATH**/ ?>