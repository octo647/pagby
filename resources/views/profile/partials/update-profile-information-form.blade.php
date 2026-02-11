<section>
@php use Illuminate\Support\Str; @endphp
    <header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-0 space-y-6">
    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-3 rounded bg-green-100 border border-green-300 text-green-800 text-sm flex items-center gap-2 animate-fade-in" id="profile-success-message">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Perfil atualizado com sucesso!</span>
        </div>
        <script>
        // Rola para o topo absoluto da página ao exibir a mensagem de sucesso
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('profile-success-message')) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
        </script>
    @endif
    @csrf
    @method('PATCH')
    <div class="mt-4 flex flex-col items-center">
    @php
        $isExternalPhoto = $user->photo && (Str::startsWith($user->photo, 'http://') || Str::startsWith($user->photo, 'https://'));
    @endphp

    <img
        id="photo-preview"
        src="{{ $isExternalPhoto ? $user->photo : ($user->photo ? tenant_asset($user->photo) : '') }}"
        class="w-24 h-24 rounded-full object-cover mb-2 object-center"
        alt="Preview da foto"
        @if(!$user->photo) style="display:none;" @endif
    >
    
   
    @error('photo')
        <span class="text-red-500 text-xs">{{ $message }}</span>
    @enderror
    
    <label for="photo"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer">
            {{ __('Atualizar foto') }}
            <input id="photo" type="file" name="photo" class="hidden" accept="image/*" onchange="previewPhoto(event)">
    </label>
    </div>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informações de Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Atualize suas informações de perfil e endereço de e-mail.') }}
        </p>

    </header>

    
    
    <div>
        <x-input-label for="name" :value="__('Nome')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
   
    <div>
        <x-input-label for="phone" :value="__('Telefone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required />
    </div>
    <div class="flex items-center mt-2">
        <input type="checkbox" id="whatsapp" name="whatsapp" value="1" {{ old('whatsapp', $user->whatsapp) ? 'checked' : '' }}>
        <label for="whatsapp" class="ml-2">É WhatsApp?</label>
    </div>

    <!-- Ativar Lembretes WhatsApp -->
    <div id="whatsapp-reminders-section" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <h3 class="text-sm font-medium text-green-900 mb-2">📱 Lembretes via WhatsApp</h3>
        <p class="text-sm text-green-700 mb-3">
            Receba avisos de vencimento de planos diretamente no seu WhatsApp.
        </p>
        @if($user->whatsapp_activated)
            <div class="flex items-center text-green-700">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Lembretes ativados</span>
            </div>
        @else
            <div id="whatsapp-not-checked-warning" class="hidden mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800">
                    ⚠️ <strong>Marque "É WhatsApp?"</strong> acima e salve antes de ativar os lembretes.
                </p>
            </div>
            <a href="https://wa.me/553298294948?text=ATIVAR" 
               id="whatsapp-activate-btn"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Ativar Lembretes
            </a>
            <p class="text-xs text-green-600 mt-2">
                Clique para enviar uma mensagem e ativar os lembretes.
            </p>
        @endif
    </div>
    <div>
        <x-input-label for="birthdate" :value="__('Data de Nascimento')" />
        <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full" :value="old('birthdate', $user->birthdate)" />
    </div>
    <div>
        <x-input-label for="cpf" :value="__('CPF')" />
        <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf', $user->cpf)" />
    </div>
    <div>
        <x-input-label for="cep" :value="__('CEP')" />
        <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $user->cep)" maxlength="9" pattern="\d{5}-?\d{3}" autocomplete="postal-code" />
        <x-input-error :messages="$errors->get('cep')" class="mt-2" />
        <span id="cep-error" class="text-red-500 text-xs mt-1 hidden">CEP inválido. Verifique e tente novamente.</span>
    </div>
    <div>
        <x-input-label for="street" :value="__('Rua')" />
        <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $user->street)" />
    </div>
    <div>
        <x-input-label for="number" :value="__('Número')" />
        <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" :value="old('number', $user->number)" />
    </div>
    <div>
        <x-input-label for="complement" :value="__('Complemento')" />
        <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" :value="old('complement', $user->complement)" />
    </div>
    <div>
        <x-input-label for="city" :value="__('Cidade')" />
        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" />
    </div>
    <div>
        <x-input-label for="neighborhood" :value="__('Bairro')" />
        <x-text-input id="neighborhood" name="neighborhood" type="text" class="mt-1 block w-full" :value="old('neighborhood', $user->neighborhood)" />
    </div>
    <div>
        <x-input-label for="state" :value="__('Estado')" />
        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $user->state)" />
    </div>
    <div class="flex items-center mt-2">
        <input type="checkbox" id="notifications_enabled" name="notifications_enabled" value="1" {{ old('notifications_enabled', $user->notifications_enabled) ? 'checked' : '' }}>
        <label for="notifications_enabled" class="ml-2">Habilitar notificações</label>
    </div>
    

<script>
function previewPhoto(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('photo-preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
</script>


<div class="flex items-center gap-4 mt-4">
    
    <x-primary-button type="submit">
        {{ __('Salvar') }}
    </x-primary-button>
</div>

</form>

<script>
function previewPhoto(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('photo-preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    const cepError = document.getElementById('cep-error');
    const whatsappCheckbox = document.getElementById('whatsapp');
    const whatsappActivateBtn = document.getElementById('whatsapp-activate-btn');
    const whatsappWarning = document.getElementById('whatsapp-not-checked-warning');
    const whatsappSection = document.getElementById('whatsapp-reminders-section');
    
    // Validação WhatsApp
    function checkWhatsAppStatus() {
        if (!whatsappCheckbox || !whatsappActivateBtn) return;
        
        const isChecked = whatsappCheckbox.checked;
        
        if (isChecked) {
            whatsappActivateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            whatsappActivateBtn.classList.add('hover:bg-green-700');
            if (whatsappWarning) whatsappWarning.classList.add('hidden');
            whatsappSection.classList.remove('bg-yellow-50', 'border-yellow-200');
            whatsappSection.classList.add('bg-green-50', 'border-green-200');
        } else {
            whatsappActivateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            whatsappActivateBtn.classList.remove('hover:bg-green-700');
            if (whatsappWarning) whatsappWarning.classList.remove('hidden');
            whatsappSection.classList.remove('bg-green-50', 'border-green-200');
            whatsappSection.classList.add('bg-yellow-50', 'border-yellow-200');
        }
    }
    
    // Previne clique se não estiver marcado
    if (whatsappActivateBtn) {
        whatsappActivateBtn.addEventListener('click', function(e) {
            if (!whatsappCheckbox.checked) {
                e.preventDefault();
                alert('⚠️ Marque "É WhatsApp?" acima e salve suas alterações antes de ativar os lembretes.');
                whatsappCheckbox.focus();
            }
        });
    }
    
    if (whatsappCheckbox) {
        whatsappCheckbox.addEventListener('change', checkWhatsAppStatus);
        checkWhatsAppStatus(); // Verifica no carregamento
    }
    
    // CEP validation
    function validarCep(cep) {
        cep = cep.replace(/\D/g, '');
        if (cep.length !== 8) return false;
        // Consulta API para validar existência real
        return fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(res => res.json())
            .then(data => !data.erro);
    }
    cepInput.addEventListener('blur', function() {
        const valor = cepInput.value;
        if (!/^\d{5}-?\d{3}$/.test(valor)) {
            cepError.textContent = 'Formato de CEP inválido.';
            cepError.classList.remove('hidden');
            return;
        }
        const cep = valor.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('street').value = data.logradouro || '';
                        document.getElementById('neighborhood').value = data.bairro || '';
                        document.getElementById('city').value = data.localidade || '';
                        document.getElementById('state').value = data.uf || '';
                        cepError.classList.add('hidden');
                    } else {
                        cepError.textContent = 'CEP não encontrado. Verifique e tente novamente.';
                        cepError.classList.remove('hidden');
                    }
                });
        }
    });
    cepInput.addEventListener('input', function() {
        cepError.classList.add('hidden');
    });
});



</section>
