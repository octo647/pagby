<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">📱 Compartilhe seu Link de Agendamento</h2>
        <p class="text-blue-100">Seus clientes escolhem filial, profissional, serviço e horário antes de fazer login!</p>
    </div>

    <!-- URL Principal -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            🔗 Link de Agendamento
        </label>
        <div class="flex gap-2">
            <input 
                type="text" 
                readonly 
                value="{{ $bookingUrl }}"
                class="flex-1 px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                id="bookingUrl"
            >
            <button 
                onclick="copyUrl()" 
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Copiar
            </button>
        </div>
        <p class="mt-2 text-sm text-green-600 hidden" id="copiedMessage">✓ Link copiado para a área de transferência!</p>
    </div>

    <!-- Botões das Redes Sociais -->
    <div class="grid md:grid-cols-2 gap-4">
        <!-- WhatsApp -->
        <a 
            href="https://api.whatsapp.com/send?text={{ $whatsappText }}"
            target="_blank"
            class="flex items-center gap-4 bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg shadow-md transition-colors group"
        >
            <div class="bg-white rounded-full p-3 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">WhatsApp</h3>
                <p class="text-sm text-green-100">Compartilhar via WhatsApp</p>
            </div>
        </a>

        <!-- Facebook -->
        <a 
            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($bookingUrl) }}&quote={{ $facebookText }}"
            target="_blank"
            class="flex items-center gap-4 bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg shadow-md transition-colors group"
        >
            <div class="bg-white rounded-full p-3 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">Facebook</h3>
                <p class="text-sm text-blue-100">Compartilhar no Facebook</p>
            </div>
        </a>

        <!-- Instagram (copiar texto) -->
        <button 
            onclick="copyInstagramText()"
            class="flex items-center gap-4 bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 hover:from-purple-600 hover:via-pink-600 hover:to-orange-600 text-white p-4 rounded-lg shadow-md transition-colors group"
        >
            <div class="bg-white rounded-full p-3 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </div>
            <div class="text-left">
                <h3 class="font-bold text-lg">Instagram</h3>
                <p class="text-sm opacity-90">Copiar texto para Story/Bio</p>
            </div>
        </button>

        <!-- Twitter/X -->
        <a 
            href="https://twitter.com/intent/tweet?text={{ $twitterText }}"
            target="_blank"
            class="flex items-center gap-4 bg-black hover:bg-gray-800 text-white p-4 rounded-lg shadow-md transition-colors group"
        >
            <div class="bg-white rounded-full p-3 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">X (Twitter)</h3>
                <p class="text-sm text-gray-300">Compartilhar no X</p>
            </div>
        </a>
    </div>

    <!-- Instruções -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <h4 class="font-semibold text-blue-900 mb-2">💡 Como funciona o agendamento:</h4>
        <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
            <li><strong>Cliente acessa o link</strong> (não precisa ter cadastro)</li>
            <li><strong>Escolhe a filial</strong> (se você tiver mais de uma)</li>
            <li><strong>Seleciona o profissional</strong> preferido</li>
            <li><strong>Escolhe o serviço</strong> desejado</li>
            <li><strong>Marca data e horário</strong> disponível</li>
            <li><strong>Faz login ou cadastro</strong> apenas no final para confirmar</li>
        </ol>
        <div class="mt-4 p-3 bg-blue-100 rounded">
            <p class="text-sm font-semibold text-blue-900">📍 Dicas de divulgação:</p>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside mt-1">
                <li>Adicione o link na bio do Instagram</li>
                <li>Compartilhe nos Stories com o texto sugerido</li>
                <li>Envie para grupos de WhatsApp dos seus clientes</li>
                <li>Fixe a publicação no Facebook</li>
                <li>Adicione na descrição de vídeos do YouTube/TikTok</li>
            </ul>
        </div>
    </div>
</div>

<script>
    function copyUrl() {
        const urlInput = document.getElementById('bookingUrl');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); // Para mobile
        
        navigator.clipboard.writeText(urlInput.value).then(() => {
            const message = document.getElementById('copiedMessage');
            message.classList.remove('hidden');
            setTimeout(() => {
                message.classList.add('hidden');
            }, 3000);
        });
    }

    function copyInstagramText() {
        const text = decodeURIComponent('{{ $instagramText }}');
        navigator.clipboard.writeText(text).then(() => {
            alert('✓ Texto copiado! Cole no Instagram e adicione o link: {{ $bookingUrl }}');
        });
    }
</script>
