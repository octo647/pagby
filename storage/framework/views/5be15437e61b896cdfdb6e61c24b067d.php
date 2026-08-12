<!DOCTYPE html>
<html>
<body>
	<h2>Novo contato de dúvida sobre o modelo de negócio</h2>
	<p><strong>Nome:</strong> <?php echo e($contato->nome); ?></p>
	<p><strong>E-mail:</strong> <?php echo e($contato->email); ?></p>
	<p><strong>Telefone:</strong> <?php echo e($contato->telefone ?? '-'); ?></p>
	<p><strong>Mensagem:</strong><br><?php echo e($contato->mensagem); ?></p>
</body>
</html>
<?php /**PATH /var/www/pagby/resources/views/emails/contato_duvida_suporte.blade.php ENDPATH**/ ?>