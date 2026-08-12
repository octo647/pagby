# Implementação: Gestão de Inadimplência de Clientes

## ✅ Arquivos Criados

### 1. Component Livewire
- **Arquivo**: `app/Livewire/Proprietario/ClientesInadimplentes.php`
- **Função**: Lista clientes com pagamentos atrasados, permite configurar bloqueio automático
- **Funcionalidades**:
  - Lista clientes inadimplentes com total de dívida
  - Envio de lembretes (WhatsApp/Email)
  - Bloqueio/desbloqueio manual
  - Configuração de bloqueio automático

###2. View Blade
- **Arquivo**: `resources/views/livewire/proprietario/clientes-inadimplentes.blade.php`
- **Design**: Interface moderna com cards, resumo financeiro e ações

### 3. Webhook Controller
- **Arquivo**: `app/Http/Controllers/SubcontaWebhookController.php`
- **Função**: Recebe notificações do Asaas quando pagamentos mudam de status
- **Eventos tratados**:
  - `PAYMENT_RECEIVED`: Marca como pago
  - `PAYMENT_OVERDUE`: Marca atrasado, opcionalmente bloqueia cliente
  - `PAYMENT_CONFIRMED`: Confirma pagamento
  
---

## 🔧 Como Integrar

### 1. Adicionar Rota do Webhook

Adicionar em `routes/web.php` (FORA do middleware 'web'):

```php
// Webhook da Subconta (sem CSRF)
Route::post('/api/subconta-webhook', [SubcontaWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

### 2. Registrar Webhook ao Criar Subconta

Em `AsaasService.php`, adicionar método:

```php
public function registrarWebhookSubconta(string $accountId)
{
    // Usar API MASTER para configurar webhook DA SUBCONTA
    $response = Http::timeout(60)->withHeaders([
        'access_token' => $this->apiKey, // Master key
        'Content-Type' => 'application/json',
        'Asaas-Account' => $accountId, // Header especial para subcontas
    ])->post($this->apiUrl . '/webhook', [
        'name' => 'PagBy - Notificações de Pagamento',
        'url' => 'https://pagby.com.br/api/subconta-webhook',
        'email' => 'webhooks@pagby.com.br',
        'interrupted' => false,
        'enabled' => true,
        'events' => [
            'PAYMENT_CREATED',
            'PAYMENT_AWAITING_RISK_ANALYSIS',
            'PAYMENT_RECEIVED',
            'PAYMENT_CONFIRMED',
            'PAYMENT_OVERDUE',
            'PAYMENT_DELETED',
            'PAYMENT_REFUNDED',
        ]
    ]);

    if ($response->successful()) {
        Log::info('[Asaas] Webhook registrado para subconta', [
            'account_id' => $accountId,
            'webhook_id' => $response->json()['id']
        ]);
        return ['success' => true, 'data' => $response->json()];
    }

    return ['success' => false, 'message' => $response->body()];
}
```

### 3. Chamar ao Criar Subconta

Em `TestAsaasSubaccountInvoice.php` ou onde cria tenant:

```php
// Após criar subconta
if ($result['success']) {
    $accountId = $result['data']['id'];
    
    // Registrar webhook
    $asaasMaster->registrarWebhookSubconta($accountId);
}
```

### 4. Adicionar ao Menu do Proprietário

Em `routes/menu.php`:

```php
[
    'label' => 'Inadimplência',
    'icon' => '<svg>...</svg>',
    'route' => 'proprietario.clientes-inadimplentes',
    'component' => 'proprietario.clientes-inadimplentes',
],
```

### 5. Adicionar Campos ao Model User (Cliente)

Migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_blocked')->default(false)->after('active');
});
```

### 6. Adicionar Método ao Tenant Model

```php
// app/Models/Tenant.php
public function getSetting($key, $default = null)
{
    $settings = json_decode($this->settings ?? '{}', true);
    return $settings[$key] ?? $default;
}

public function putSetting($key, $value)
{
    $settings = json_decode($this->settings ?? '{}', true);
    $settings[$key] = $value;
    $this->settings = json_encode($settings);
    $this->save();
}
```

Migration:

```php
Schema::table('tenants', function (Blueprint $table) {
    $table->text('settings')->nullable();
});
```

---

## 📊 Funcionalidades Implementadas

### Para o Salão (Proprietário)

✅ Dashboard com total de inadimplentes e valor em dívida  
✅ Lista detalhada por cliente com dias de atraso  
✅ Envio de lembretes (integrar WhatsApp/Email)  
✅ Bloqueio/desbloqueio manual de clientes  
✅ Configuração de bloqueio automático  
✅ Configuração de dias para notificar  

### Automatizado (Webhook)

✅ Atualização automática de status quando cliente paga  
✅ Detecção automática de atraso  
✅ Bloqueio automático (opcional/configurável)  
✅ Notificações para proprietário  

---

## 🎯 Decisão do Modelo: SaaS Puro ✅

- **PagBy**: Cobra assinatura fixa (R$ 29,90 ou R$ 59,90 por funcionário)
- **PagBy**: Fornece ferramentas para salão identificar inadimplentes
- **Salão**: Decide se bloqueia ou aceita cliente com débito
- **PagBy**: NÃO interfere na relação Salão ↔ Cliente

**Você fornece a ferramenta, o salão usa como quiser!**

---

## 🚀 Próximos Passos

1. ✅ Testar componente em desenvolvimento
2. ✅ Integrar envio de WhatsApp (usar serviço existente)
3. ✅ Adicionar ao menu do proprietário
4. ✅ Documentar para equipe
5. ✅ Deploy em produção

