#!/bin/bash

# Simula webhook do Asaas para pagamento de assinatura
# Tenant teste: teste1772962022 (Account: 23fa0512-1fc8-4ccc-bd79-236dd329db0e)

echo "🎯 Testando webhook de ASSINATURA..."
echo "📤 Enviando payload para: https://pagby.com.br/api/subconta-webhook"
echo ""

curl -X POST https://pagby.com.br/api/subconta-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "event": "PAYMENT_RECEIVED",
    "account": "23fa0512-1fc8-4ccc-bd79-236dd329db0e",
    "subscription": "sub_test_123456",
    "payment": {
      "id": "pay_sub_test_001",
      "subscription": "sub_test_123456",
      "customer": "cus_test_001",
      "value": 99.90,
      "netValue": 95.90,
      "billingType": "PIX",
      "status": "RECEIVED",
      "dueDate": "2026-03-08",
      "paymentDate": "2026-03-08",
      "invoiceUrl": "https://asaas.com/invoice/test",
      "description": "Assinatura Mensal - Teste"
    }
  }' \
  -v

echo ""
echo ""
echo "🎯 Testando webhook de PAGAMENTO AVULSO..."
echo "📤 Enviando payload para: https://pagby.com.br/api/subconta-webhook"
echo ""

curl -X POST https://pagby.com.br/api/subconta-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "event": "PAYMENT_RECEIVED",
    "account": "23fa0512-1fc8-4ccc-bd79-236dd329db0e",
    "payment": {
      "id": "pay_avulso_test_002",
      "customer": "cus_test_002",
      "value": 150.00,
      "netValue": 145.50,
      "billingType": "CREDIT_CARD",
      "status": "RECEIVED",
      "dueDate": "2026-03-08",
      "paymentDate": "2026-03-08",
      "invoiceUrl": "https://asaas.com/invoice/test2",
      "description": "Pagamento Corte + Barba"
    }
  }' \
  -v

echo ""
echo ""
echo "✅ Testes enviados!"
echo "📝 Verificar logs no VPS:"
echo "   ssh -p 22022 helder@69.6.222.77 'tail -100 /var/www/pagby/storage/logs/laravel.log | grep Webhook'"
