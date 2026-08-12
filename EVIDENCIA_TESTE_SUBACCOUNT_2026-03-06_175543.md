# 🧪 Evidência: Teste de Subconta Asaas

**Data/Hora:** 2026-03-06 17:55:39
**Ambiente:** SANDBOX
**API URL:** https://sandbox.asaas.com/api/v3

---

## 📋 Dados do Teste

- **Tenant ID:** teste1772829838
- **Tenant Nome:** Salão Teste Validação NF
- **Account ID (Subconta):** 81c3346a-8464-4cc6-8616-7b2cdef6b664
- **Customer ID:** cus_000007642173
- **Payment ID:** pay_cu1dbjsgtk3b6my3

---

## 🎯 RESULTADO DO TESTE

### Campo 'account' da Cobrança

```
Subconta criada:     81c3346a-8464-4cc6-8616-7b2cdef6b664
Cobrança pertence a: 
```

### ❌❌❌ TESTE FALHOU ❌❌❌

**Cobrança NÃO pertence à subconta!**

Isso significa que:
- ❌ A Nota Fiscal será emitida em nome do PAGBY (master)
- ❌ NÃO será emitida em nome do Salão
- ❌ Modelo precisa AJUSTES
- ❌ Contatar suporte Asaas

---

## 📄 Dados Completos da Cobrança

```json
{
    "object": "payment",
    "id": "pay_cu1dbjsgtk3b6my3",
    "dateCreated": "2026-03-06",
    "customer": "cus_000007642173",
    "checkoutSession": null,
    "paymentLink": null,
    "value": 100,
    "netValue": 99.01,
    "originalValue": null,
    "interestValue": null,
    "description": "TESTE CRÍTICO: Validação de emissor da NF",
    "billingType": "PIX",
    "pixTransaction": null,
    "status": "PENDING",
    "dueDate": "2026-03-13",
    "originalDueDate": "2026-03-13",
    "paymentDate": null,
    "clientPaymentDate": null,
    "installmentNumber": null,
    "invoiceUrl": "https:\/\/sandbox.asaas.com\/i\/cu1dbjsgtk3b6my3",
    "invoiceNumber": "13348532",
    "externalReference": null,
    "deleted": false,
    "anticipated": false,
    "anticipable": false,
    "creditDate": null,
    "estimatedCreditDate": null,
    "transactionReceiptUrl": null,
    "nossoNumero": null,
    "bankSlipUrl": null,
    "lastInvoiceViewedDate": null,
    "lastBankSlipViewedDate": null,
    "discount": {
        "value": 0,
        "limitDate": null,
        "dueDateLimitDays": 0,
        "type": "FIXED"
    },
    "fine": {
        "value": 0,
        "type": "FIXED"
    },
    "interest": {
        "value": 0,
        "type": "PERCENTAGE"
    },
    "postalService": false,
    "escrow": null,
    "refunds": null
}
```

---

## 📊 Próximos Passos

### ❌ Teste Falhou - Buscar Solução

1. Contatar suporte Asaas
2. Perguntar: "Como fazer NF sair em nome da subconta?"
3. Avaliar modelo alternativo se não for possível
4. NÃO implementar em produção até resolver

---

*Documento gerado automaticamente pelo comando:*
```bash
php artisan asaas:test-subaccount-invoice
```
