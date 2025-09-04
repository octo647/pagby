<section>
    <header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
    @csrf
    @method('PATCH')
    <div class="mt-4 flex flex-col items-center">
    

    <img
        id="photo-preview"
        src="{{ $user->photo ? tenant_asset($user->photo) : '' }}"
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
        <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $user->cep)" />
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
document.getElementById('cep').addEventListener('blur', function() {
    let cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('street').value = data.logradouro || '';
                    document.getElementById('neighborhood').value = data.bairro || '';
                    document.getElementById('city').value = data.localidade || '';
                    document.getElementById('state').value = data.uf || '';
                }
            });
    }
});
</script>


</section>
