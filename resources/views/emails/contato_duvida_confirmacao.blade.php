<!DOCTYPE html>
<html>
<body>
	<h2>Olá, {{ $contato->nome }}!</h2>
	<p>Recebemos sua dúvida sobre o modelo de negócio Pagby. Em breve nossa equipe entrará em contato.</p>
	<p><strong>Mensagem enviada:</strong><br>{{ $contato->mensagem }}</p>
	<p>Se precisar de mais informações, basta responder este e-mail.</p>
	<p>Atenciosamente,<br>Equipe Pagby</p>
</body>
</html>
