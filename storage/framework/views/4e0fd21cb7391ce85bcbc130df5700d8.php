<?php $__env->startComponent('mail::message'); ?>
# Pagamento de renovação confirmado!

Olá <?php echo e($contact->owner_name ?? $contact->tenant_name ?? $contact->name); ?>,

Recebemos o pagamento da renovação do seu plano <strong><?php echo e(ucfirst($plan)); ?></strong> no PagBy.

Sua assinatura continua ativa e você pode seguir aproveitando todos os recursos da plataforma normalmente.

Se precisar de suporte, estamos à disposição!

<?php $__env->startComponent('mail::button', ['url' => config('app.url')]); ?>
Acessar PagBy
<?php echo $__env->renderComponent(); ?>

Obrigado por confiar no PagBy!

Atenciosamente,
Equipe PagBy
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/pagby/resources/views/emails/subscription-renewed.blade.php ENDPATH**/ ?>