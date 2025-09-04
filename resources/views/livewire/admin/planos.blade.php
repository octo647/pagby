<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="bg-white py-16">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Planos de Assinatura</h2>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($planos as $plano)
                <div class="plan-card border p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                    <h3 class="text-xl font-semibold mb-4">{{ $plano->nome }}</h3>
                    <p class="text-2xl font-bold mb-4">R$ {{ number_format($plano->preco, 2, ',', '.') }} / mês</p>
                    <ul class="mb-6 text-left">
                        @foreach(explode(',', $plano->beneficios) as $beneficio)
                        <li class="mb-2">• {{ $beneficio }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
