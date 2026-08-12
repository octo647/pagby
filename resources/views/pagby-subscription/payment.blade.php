@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg py-8">
    <h2 class="text-2xl font-bold mb-6">Escolha a forma de pagamento</h2>
    <form method="POST" action="{{ route('pagby-subscription.payment.process') }}" id="payment-form">
        @csrf
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Forma de pagamento</label>
            <select name="payment_method" id="payment_method" class="w-full border rounded px-3 py-2">
                <option value="credit_card">Cartão de Crédito</option>
                <option value="boleto">Boleto</option>
                <option value="pix">Pix</option>
            </select>
        </div>
        <div id="credit-card-fields" class="mb-4">
            <label class="block mb-2 font-semibold">Dados do Cartão</label>
            <input type="text" name="card_number" class="w-full border rounded px-3 py-2 mb-2" placeholder="Número do cartão">
            <input type="text" name="holder_name" class="w-full border rounded px-3 py-2 mb-2" placeholder="Nome do titular">
            <div class="flex gap-2 mb-2">
                <input type="text" name="expiration" class="w-1/2 border rounded px-3 py-2" placeholder="Validade (MM/AA)">
                <input type="text" name="cvv" class="w-1/2 border rounded px-3 py-2" placeholder="CVV">
            </div>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Nome completo</label>
            <input type="text" name="customer_name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">CPF</label>
            <input type="text" name="customer_cpf" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">E-mail</label>
            <input type="email" name="customer_email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Telefone</label>
            <input type="text" name="customer_phone" class="w-full border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold">Continuar</button>
    </form>
</div>
<script>
    const paymentMethod = document.getElementById('payment_method');
    const creditCardFields = document.getElementById('credit-card-fields');
    paymentMethod.addEventListener('change', function() {
        if (this.value === 'credit_card') {
            creditCardFields.style.display = 'block';
        } else {
            creditCardFields.style.display = 'none';
        }
    });
    // Inicializa o estado correto ao carregar
    if (paymentMethod.value !== 'credit_card') {
        creditCardFields.style.display = 'none';
    }
</script>
@endsection
