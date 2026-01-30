@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Status do Pagamento Asaas</h2>
    <div class="mb-2">
        <strong>ID do Pagamento Asaas:</strong> {{ $asaas_payment_id }}
    </div>
    <div class="mb-2">
        <strong>Status na Asaas:</strong> {{ $status['status'] ?? 'Indisponível' }}
    </div>
    <div class="mb-2">
        <strong>Valor:</strong> R$ {{ $pagamento ? number_format($pagamento->amount, 2, ',', '.') : '-' }}
    </div>
    <div class="mb-2">
        <strong>Status Local:</strong> {{ $pagamento->status ?? '-' }}
    </div>
    <div class="mb-2">
        <strong>Dados completos da Asaas:</strong>
        <pre class="bg-gray-100 p-2 rounded text-xs">{{ json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    <a href="{{ url()->previous() }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Voltar</a>
</div>
@endsection
