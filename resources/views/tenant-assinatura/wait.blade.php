<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento PagBy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <strong>PagBy</strong>
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-shopping-cart"></i> Complete seu Pagamento</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <h6>Quase lá! 🎉</h6>
                            <p><strong>{{ $tenant_name }}</strong> </p>
                            <p><strong>Plano:</strong> {{ $plan_name }}</p>
                            <p><strong>Valor:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>📋 Instruções:</strong><br>
                            1. Clique no botão verde abaixo<br>
                            2. Complete o pagamento no MercadoPago<br>
                            3. Volte para esta página<br>
                            4. Clique "Verificar Status"
                        </div>
                        
                        <div class="mt-4 mb-4">
                            <a href="{{ $checkout_url }}" target="_blank" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Pagar no MercadoPago
                            </a>
                        </div>
                        
                        <hr>
                        
                        <div id="status-section">
                            <div id="status-check" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-spinner fa-spin"></i> Verificando pagamento...
                                    <br>
                                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="stopChecking()">
                                        Parar Verificação
                                    </button>
                                </div>
                            </div>
                            
                            <div id="success-message" style="display: none;">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-check-circle"></i> Pagamento Aprovado!</h6>
                                    <p>Redirecionando...</p>
                                </div>
                            </div>
                            
                            <div id="rejected-message" style="display: none;">
                                <div class="alert alert-danger">
                                    <h6><i class="fas fa-times-circle"></i> Pagamento Rejeitado</h6>
                                    <p>Tente novamente...</p>
                                </div>
                            </div>
                            
                            <button id="check-payment" class="btn btn-primary btn-lg" onclick="checkPaymentStatus()">
                                <i class="fas fa-search"></i> Verificar Status do Pagamento
                            </button>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    Status atual: <span id="current-status" class="badge bg-info">{{ ucfirst($payment->status) }}</span>
                                </small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mt-4">
                            <a href="{{ url('/dashboard?tabelaAtiva=planos-de-assinatura') }}" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Voltar ao Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let checkInterval;
    let isChecking = false;
    let checkCount = 0;
    const maxChecks = 20; // Máximo 20 verificações (1 minuto)

    function checkPaymentStatus() {
      
        if (isChecking) return;
        
        isChecking = true;
        checkCount = 0;
       
        
        document.getElementById('check-payment').style.display = 'none';
        
        checkInterval = setInterval(() => {
            checkCount++;
            document.getElementById('status-check').style.display = 'block';
           
            fetch('/tenant-assinatura/check-status/{{ $payment->id }}')
           
                .then(response => response.json())
                .then(data => {
                    console.log('Status verificado:', data.status, 'Tentativa:', checkCount);
                   
                    // Atualizar badge de status
                    const statusBadge = document.getElementById('current-status');
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusBadge.className = 'badge ' + (data.status === 'approved' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-info');
                    
                    if (data.status === 'approved' || data.status === 'authorized') {
                        clearInterval(checkInterval);
                        document.getElementById('status-check').style.display = 'none';
                        document.getElementById('success-message').style.display = 'block';
                        
                        setTimeout(() => {
                            window.location.href = '/tenant-assinatura/success?payment_id=' + data.payment_id;
                        }, 2000);
                        
                    } else if (data.status === 'rejected') {
                        clearInterval(checkInterval);
                        document.getElementById('status-check').style.display = 'none';
                        document.getElementById('rejected-message').style.display = 'block';
                        
                        setTimeout(() => {
                            stopChecking();
                        }, 3000);
                    } else if (checkCount >= maxChecks) {
                        // Parar após muitas tentativas
                        stopChecking();
                        alert('Verificação finalizada. Status ainda é: ' + data.status + '. Clique "Verificar Status" novamente se necessário.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar status:', error);
                    stopChecking();
                    alert('Erro ao verificar status. Tente novamente.');
                });
        }, 3000); // Verificar a cada 3 segundos
    }

    function stopChecking() {
        clearInterval(checkInterval);
        isChecking = false;
        checkCount = 0;
        document.getElementById('status-check').style.display = 'none';
        document.getElementById('check-payment').style.display = 'block';
    }

    // Não auto-verificar - apenas mostrar botão
    console.log('Página carregada. Clique "Verificar Status" após fazer o aamento.');
    </script>
</body>
</html>