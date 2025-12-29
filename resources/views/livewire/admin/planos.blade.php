<div>
    <h2 class="text-2xl font-bold mb-6">Controle de Pagamentos dos Planos</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Salão</th>
                    <th class="px-4 py-2 border">Plano</th>
                    <th class="px-4 py-2 border">Início</th>
                    <th class="px-4 py-2 border">Valor</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">ID Pagamento</th>
                    
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="px-4 py-2 border text-center">{{ $payment->id }}</td>
                    <td class="px-4 py-2 border">{{ $payment->tenant->name ?? '-' }}</td>
                    <td class="px-4 py-2 border">{{ $payment->plan }}</td>
                    <td class="px-4 py-2 border">{{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-2 border text-right">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border">{{ $payment->status }}</td>
                    <td class="px-4 py-2 border">{{ $payment->asaas_payment_id }}</td>
                    
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-2 border text-center">Nenhum pagamento encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
