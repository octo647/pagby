
<x-pagby-layout>
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full bg-white rounded-xl shadow-2xl p-10 fade-in">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-purple-800 mb-2">Contrato de Prestação de Serviços - Pagby</h1>
                <p class="text-gray-700">
                    Este contrato é celebrado entre
                    <strong id="contrato-nome">{{ $tenant->fantasy_name ?? 'Seu Negócio' }}</strong>,
                    inscrito sob CPF/CNPJ <span id="contrato-cnpj">{{ $tenant->cnpj ?? '00.000.000/0000-00' }}</span>,
                    doravante denominado CONTRATANTE, e
                    <span class="font-semibold">Pagby Plataforma de Gestão</span>, doravante denominada CONTRATADA.
                </p>

                <script>
                // Preenche contrato com dados do localStorage se existirem
                document.addEventListener('DOMContentLoaded', function() {
                    var nome = localStorage.getItem('pagby_tenant_name');
                    var cnpj = localStorage.getItem('pagby_cpf');
                    if (nome) document.getElementById('contrato-nome').textContent = nome;
                    if (cnpj) document.getElementById('contrato-cnpj').textContent = cnpj;
                });
                </script>
            </div>

            <div class="space-y-6 text-gray-800">
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">1. Objeto</h2>
                    <p>O presente contrato tem por objeto a prestação de serviços de gestão e automação de agendamentos, pagamentos, controle de funcionários e demais funcionalidades descritas na plataforma Pagby.</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">2. Condições de Uso</h2>
                    <ul class="list-disc list-inside ml-4">
                        <li>O CONTRATANTE concorda em utilizar a plataforma conforme os termos e políticas da CONTRATADA.</li>
                        <li>O acesso à plataforma está condicionado à assinatura de um plano vigente e ao aceite deste contrato.</li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">3. Pagamento</h2>
                    <p>O valor do serviço será conforme o plano escolhido, com preço por funcionário conforme tabela vigente na plataforma.</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">4. Vigência</h2>
                    <p>Este contrato entra em vigor na data do aceite digital e permanece válido enquanto houver assinatura ativa.</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">5. Rescisão</h2>
                    <p>O contrato poderá ser rescindido por qualquer das partes mediante aviso prévio de 30 dias ou por inadimplência.</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-purple-700 mb-1">6. Foro</h2>
                    <p>Fica eleito o foro da comarca de Juiz de Fora/MG para dirimir quaisquer dúvidas oriundas deste contrato.</p>
                </div>
            </div>

            <div class="mt-10 text-center">
                <p class="text-gray-700 font-medium">Ao clicar em "Aceitar", o CONTRATANTE concorda com todos os termos acima.</p>
                <a href="{{ url()->previous() }}" class="inline-block mt-6 px-6 py-2 bg-purple-700 text-white rounded-lg shadow hover:bg-purple-800 transition">Voltar</a>
            </div>
        </div>
    </div>
</x-pagby-layout>
