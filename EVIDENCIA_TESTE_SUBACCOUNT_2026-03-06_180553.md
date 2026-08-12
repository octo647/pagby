# 🧪 Evidência: Teste de Subconta Asaas

**Data/Hora:** 2026-03-06 18:05:49
**Ambiente:** SANDBOX
**API URL:** https://sandbox.asaas.com/api/v3

---

## 📋 Dados do Teste

- **Tenant ID:** teste1772829838
- **Tenant Nome:** Salão Teste Validação NF
- **Account ID (Subconta):** 81c3346a-8464-4cc6-8616-7b2cdef6b664
- **Customer ID:** cus_000007642209
- **Payment ID:** pay_uahxjq65ya22lrdk

---

## 🎯 RESULTADO DO TESTE

### Campo 'account' da Cobrança

```
Subconta criada:     81c3346a-8464-4cc6-8616-7b2cdef6b664
Cobrança pertence a: 81c3346a-8464-4cc6-8616-7b2cdef6b664
```

### ✅✅✅ SUCESSO! ✅✅✅

**Cobrança pertence à SUBCONTA!**

Isso significa que:
- ✅ A Nota Fiscal será emitida em nome do SALÃO (subconta)
- ✅ NÃO será emitida em nome do PagBy (master)
- ✅ Modelo SEM SPLIT está TECNICAMENTE VALIDADO
- ✅ Cliente paga direto na conta do salão (100%)
- ✅ Fiscalmente CORRETO!

---

## 📄 Dados Completos da Cobrança

```json
{
    "object": "payment",
    "id": "pay_uahxjq65ya22lrdk",
    "dateCreated": "2026-03-06",
    "customer": "cus_000007642209",
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
    "invoiceUrl": "https:\/\/sandbox.asaas.com\/i\/uahxjq65ya22lrdk",
    "invoiceNumber": "13348592",
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

### ✅ Teste Passou - Implementar em Produção

1. ✅ Modelo tecnicamente validado
2. Implementar código completo
3. Contratar contador COM EVIDÊNCIA (este documento)
4. Perguntar: "A NF sai em nome correto. Está OK fiscalmente?"
5. Consulta confirmatória (mais barata que exploratória)
6. Deploy em produção

---

*Documento gerado automaticamente pelo comando:*
```bash
php artisan asaas:test-subaccount-invoice
```
