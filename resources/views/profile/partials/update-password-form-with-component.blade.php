{{-- Exemplo de uso do componente password-input --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Alterar Senha') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Mantenha sua conta segura usando uma senha forte e única.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <x-password-input 
            id="update_password_current_password" 
            name="current_password" 
            :label="__('Senha Atual')"
            autocomplete="current-password"
        >
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </x-password-input>

        <x-password-input 
            id="update_password_password" 
            name="password" 
            :label="__('Nova Senha')"
            autocomplete="new-password"
        >
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <div class="mt-1 text-xs text-gray-500 space-y-1">
                <p>Sua senha deve conter:</p>
                <ul class="list-disc list-inside pl-2 space-y-0.5">
                    <li>Pelo menos 8 caracteres</li>
                    <li>Pelo menos uma letra</li>
                    <li>Pelo menos um número</li>
                </ul>
            </div>
        </x-password-input>

        <x-password-input 
            id="update_password_password_confirmation" 
            name="password_confirmation" 
            :label="__('Confirmar Nova Senha')"
            autocomplete="new-password"
        >
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </x-password-input>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Atualizar Senha') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-medium"
                >{{ __('Senha atualizada com sucesso!') }}</p>
            @endif
        </div>
    </form>
</section>
