<!DOCTYPE html>
<html>
<body>
	<h2>Novo contato de dúvida sobre o modelo de negócio</h2>
	<p><strong>Nome:</strong> {{ $contato->nome }}</p>
	<p><strong>E-mail:</strong> {{ $contato->email }}</p>
	<p><strong>Telefone:</strong> {{ $contato->telefone ?? '-' }}</p>
	<p><strong>Mensagem:</strong><br>{{ $contato->mensagem }}</p>
</body>
</html>
