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
                            <p><strong>Salão:</strong> {{ $tenant_name }}</p>
                            <p><strong>Plano:</strong> {{ $plan_name }}</p>
                            <p><strong>Valor:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                        </div>
                        @if(str_contains($payment->description ?? '', 'http'))
                        <div class="mb-4">
                            <a href="{{ explode(' ', $payment->description)[2] ?? $payment->description }}" target="_blank" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Ir para página segura de pagamento
                            </a>
                            <p class="mt-2 text-muted">Você será redirecionado para o ambiente seguro do Asaas para finalizar o pagamento.</p>
                        </div>
                        @endif
                        
                        <div class="alert alert-info">
                            <strong>📋 Instruções:</strong><br>
                            1. Clique no botão verde acima<br>
                            2. Complete o pagamento usando o ambiente seguro Asaas<br>
                            3. Após o pagamento, clique em "Verificar Status"
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
                            
                            <div id="check-payment-section">
                                <button id="check-payment" class="btn btn-primary btn-lg" type="button" onclick="checkPaymentStatus()" style="min-width:260px;">
                                    <i class="fas fa-search"></i> Verificar Status do Pagamento
                                </button>
                                <div class="mt-3" id="status-line">
                                    <small class="text-muted">
                                        Status atual: <span id="current-status" class="badge bg-info">{{ ucfirst($payment->status) }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-secondary">
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
        document.getElementById('status-check').style.display = 'block';
        // Em vez de ocultar, apenas desabilita e muda o texto do botão azul
        var checkBtn = document.getElementById('check-payment');
        if (checkBtn) {
            checkBtn.disabled = true;
            checkBtn.innerHTML = '<i class="fas fa-search"></i> Verificando...';
        }
        
        checkInterval = setInterval(() => {
            checkCount++;
            
            fetch('/pagby-subscription/check-status/{{ $payment->id }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Resposta completa do status:', data);
                    if (!data || typeof data.status === 'undefined') {
                        clearInterval(checkInterval);
                        stopChecking();
                        alert('Erro: resposta inesperada do servidor ao verificar status do pagamento.');
                        return;
                    }
                    const status = (data.status || '').toLowerCase();
                    console.log('Status verificado:', data.status, 'Tentativa:', checkCount);
                    // Atualizar badge de status
                    const statusBadge = document.getElementById('current-status');
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    let badgeClass = 'bg-info';
                    if (["received","paid","confirmed","authorized"].includes(status)) badgeClass = 'bg-success';
                    else if (["cancelled","rejected"].includes(status)) badgeClass = 'bg-danger';
                    statusBadge.className = 'badge ' + badgeClass;
                    // Parar imediatamente se status for sucesso
                    if (["received","paid","confirmed","authorized"].includes(status)) {
                        clearInterval(checkInterval);
                        document.getElementById('status-check').style.display = 'none';
                        document.getElementById('success-message').style.display = 'block';
                        setTimeout(() => {
                            window.location.href = '/pagby-subscription/success?payment_id=' + data.payment_id + '&status=approved&external_reference=' + data.payment_id;
                        }, 2000);
                        return;
                    } else if (["cancelled","rejected"].includes(status)) {
                        clearInterval(checkInterval);
                        document.getElementById('status-check').style.display = 'none';
                        document.getElementById('rejected-message').style.display = 'block';
                        setTimeout(() => {
                            stopChecking();
                        }, 3000);//limita em 3 segundos
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
        var checkBtn = document.getElementById('check-payment');
        if (checkBtn) {
            checkBtn.disabled = false;
            checkBtn.innerHTML = '<i class="fas fa-search"></i> Verificar Status do Pagamento';
        }
    }

    // Adiciona integração com botão Pagar (Asaas)
    document.getElementById('asaas-pay-btn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Processando...';
        fetch('/pagby-subscription/asaas-pay/{{ $payment->id }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.textContent = 'Pagamento iniciado!';
                setTimeout(() => {
                    checkPaymentStatus();
                }, 1000);
            } else {
                btn.disabled = false;
                btn.textContent = 'Pagar';
                alert(data.message || 'Erro ao iniciar pagamento.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Pagar';
            alert('Erro ao conectar com o servidor.');
        });
    });

    // Atualiza o texto do botão verde quando status mudar para Confirmed/Authorized
    function updateGreenButtonOnStatus(status) {
        const btn = document.getElementById('asaas-pay-btn');
        const checkSection = document.getElementById('check-payment-section');
        const normalized = (status || '').toLowerCase();
        if (normalized === 'authorized' || normalized === 'confirmed') {
            if (btn) {
                btn.textContent = 'Pagamento Confirmado!';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
                btn.disabled = true;
            }
            if (checkSection) checkSection.style.display = 'none';
        } else {
            if (btn) {
                btn.textContent = 'Pagar';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                btn.disabled = false;
            }
            if (checkSection) checkSection.style.display = '';
        }
    }

    // Não auto-verificar - apenas mostrar botão
    console.log('Página carregada. Clique "Verificar Status" após fazer o pagamento.');
    </script>
</body>
</html>