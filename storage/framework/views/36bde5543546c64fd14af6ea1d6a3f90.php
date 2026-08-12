<?php $__env->startComponent('mail::message'); ?>
# Bem-vindo ao PagBy!

Olá <?php echo e($contact->owner_name ?? $contact->tenant_name ?? $contact->name); ?>,

Seu pagamento do plano <strong><?php echo e(ucfirst($plan)); ?></strong> foi confirmado com sucesso! 🎉

Agora você tem acesso completo à plataforma PagBy para gerenciar seu negócio de beleza.

**Próximos passos:**
- Aguarde a configuração do seu ambiente (em breve você receberá os dados de acesso)
- Em caso de dúvidas, entre em contato pelo WhatsApp ou e-mail abaixo.



Obrigado por escolher o PagBy!

Atenciosamente,
Equipe PagBy

<?php $__env->slot('subcopy'); ?>
Se precisar de suporte, envie um e-mail para suportepagby@gmail.com ou WhatsApp <?php echo e(config('pagby.whatsapp_display')); ?>

<?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/pagby/resources/views/emails/welcome-subscription.blade.php ENDPATH**/ ?>