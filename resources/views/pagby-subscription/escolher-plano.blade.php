<x-pagby-layout>
    <div class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-3xl mx-auto">
            <!-- Plano Selecionado -->
            <div id="planos" class="fade-in mt-16 max-w-6xl w-full">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 text-white">
                    Escolha seu plano Pagby
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Selecione a periodicidade e o número de funcionários para ver o valor do plano.
                </p>
            </div>

            <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl p-8">
                <form id="form-plano" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div>
                        <label for="periodicidade" class="block font-bold mb-2 text-gray-800">Periodicidade</label>
                        <select id="periodicidade" name="periodicidade" class="w-40 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base">
                            <option value="mensal">Mensal</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                    <div>
                        <label for="numFuncionarios" class="block font-bold mb-2 text-gray-800">Nº de Funcionários</label>
                        <input type="number" id="numFuncionarios" name="numFuncionarios" min="1" max="7" value="1" class="w-32 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base" />
                    </div>
                </form>
                <div class="mt-8 text-center">
                                        <div id="avisoFuncionarios" class="hidden text-red-600 text-lg font-semibold mb-4">
                                            Para mais de 7 funcionários, consulte valores pelo WhatsApp <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="underline text-green-700" target="_blank">{{ config('pagby.whatsapp_display') }}</a>.
                                        </div>
                    <div id="equivalenteMensalLabel" class="text-3xl font-extrabold text-pink-600 mb-2">
                        Equivalente mensal:
                    </div>
                    <div id="valorPlano" class="text-5xl font-bold text-purple-700 mb-2">
                        R$ 60,00/mês
                    </div>
                    <div id="valorTotalPlano" class="text-lg text-gray-700 mb-4">
                        Valor total: R$ 60,00
                    </div>
                    <a id="btn-assinar" href="#" class="inline-block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-10 py-4 rounded-full text-xl font-bold shadow-lg hover:from-pink-700 hover:to-purple-700 transition-all transform hover:scale-105">
                        Assinar este plano
                    </a>
                </div>
            </div>

            <div class="text-center mt-12 space-y-4">
                <div class="flex flex-col md:flex-row justify-center items-center gap-6 text-white/90">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Dados 100% seguros</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Suporte dedicado</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Atualizações constantes</span>
                    </div>
                </div>
            </div>
        </div>

            <!-- Garantia e Segurança -->
            <div class="grid md:grid-cols-3 gap-6 text-white">
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-shield-alt text-2xl text-green-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Segurança Total</h4>
                    <p class="text-sm text-gray-300">Seus dados protegidos com criptografia de ponta</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-undo text-2xl text-blue-400 mb-3"></i>
                    <h4 class="font-bold mb-2">7 Dias Grátis</h4>
                    <p class="text-sm text-gray-300">Teste sem compromisso por uma semana</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-headset text-2xl text-purple-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Suporte Dedicado</h4>
                    <p class="text-sm text-gray-300">Equipe especializada para te ajudar</p>
                </div>
            </div>
        </div>
    </div>
</x-pagby-layout>