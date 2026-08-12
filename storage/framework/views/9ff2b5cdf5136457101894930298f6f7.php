

<?php if(tenant() && tenant()->logo): ?>
    <img src="/<?php echo e(tenant()->logo); ?>" alt="Logo do Salão" <?php echo e($attributes->merge(['class' => 'object-cover rounded-full shadow-lg'])); ?>>

<?php else: ?>
    <img src="/images/logo.png" alt="Logo padrão" <?php echo e($attributes->merge(['class' => 'object-cover rounded-full shadow-lg'])); ?>>
<?php endif; ?><?php /**PATH /var/www/pagby/resources/views/components/application-logo.blade.php ENDPATH**/ ?>