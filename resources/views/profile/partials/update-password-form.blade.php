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

        <div>
            <x-input-label for="update_password_current_password" :value="__('Senha Atual')" />
            <div class="relative">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full pr-10" autocomplete="current-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 focus:outline-none focus:text-gray-600" onclick="togglePassword('update_password_current_password')" title="Mostrar/Ocultar senha">
                    <svg id="eye-open-current" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed-current" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nova Senha')" />
            <div class="relative">
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 focus:outline-none focus:text-gray-600" onclick="togglePassword('update_password_password')" title="Mostrar/Ocultar senha">
                    <svg id="eye-open-new" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed-new" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <div class="mt-1 text-xs text-gray-500 space-y-1">
                <p>Sua senha deve conter:</p>
                <ul class="list-disc list-inside pl-2 space-y-0.5">
                    <li>Pelo menos 8 caracteres</li>
                    <li>Pelo menos uma letra</li>
                    <li>Pelo menos um número</li>
                </ul>
                
                <!-- Indicador de força da senha -->
                <div class="mt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-700">Força da senha:</span>
                        <span id="password-strength-text" class="text-xs font-medium"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Nova Senha')" />
            <div class="relative">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full pr-10" autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 focus:outline-none focus:text-gray-600" onclick="togglePassword('update_password_password_confirmation')" title="Mostrar/Ocultar senha">
                    <svg id="eye-open-confirm" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed-confirm" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

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

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const isPassword = field.type === 'password';
            
            // Toggle field type
            field.type = isPassword ? 'text' : 'password';
            
            // Get the appropriate eye icons based on field
            let openEyeId, closedEyeId;
            
            if (fieldId.includes('current')) {
                openEyeId = 'eye-open-current';
                closedEyeId = 'eye-closed-current';
            } else if (fieldId.includes('confirmation')) {
                openEyeId = 'eye-open-confirm';
                closedEyeId = 'eye-closed-confirm';
            } else {
                openEyeId = 'eye-open-new';
                closedEyeId = 'eye-closed-new';
            }
            
            const openEye = document.getElementById(openEyeId);
            const closedEye = document.getElementById(closedEyeId);
            
            // Toggle eye icons
            if (isPassword) {
                openEye.classList.add('hidden');
                closedEye.classList.remove('hidden');
            } else {
                openEye.classList.remove('hidden');
                closedEye.classList.add('hidden');
            }
        }

        // Função para verificar a força da senha
        function checkPasswordStrength(password) {
            let score = 0;

            // Critérios de força
            if (password.length >= 8) score += 25;
            if (/[a-z]/.test(password)) score += 25;
            if (/[A-Z]/.test(password)) score += 25;
            if (/[0-9]/.test(password)) score += 25;
            if (/[^A-Za-z0-9]/.test(password)) score += 25; // Símbolos
            if (password.length >= 12) score += 25; // Bônus para senhas longas

            // Classificação
            let strength = '';
            let color = '';
            
            if (score <= 25) {
                strength = 'Muito fraca';
                color = 'bg-red-500';
            } else if (score <= 50) {
                strength = 'Fraca';
                color = 'bg-orange-500';
            } else if (score <= 75) {
                strength = 'Boa';
                color = 'bg-yellow-500';
            } else if (score <= 100) {
                strength = 'Forte';
                color = 'bg-green-500';
            } else {
                strength = 'Muito forte';
                color = 'bg-green-600';
            }

            return { score: Math.min(score, 100), strength, color };
        }

        // Event listener para o campo de nova senha
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('update_password_password');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            if (passwordField && strengthBar && strengthText) {
                passwordField.addEventListener('input', function() {
                    const password = this.value;
                    const result = checkPasswordStrength(password);
                    
                    // Atualizar barra de progresso
                    strengthBar.style.width = result.score + '%';
                    strengthBar.className = `h-2 rounded-full transition-all duration-300 ${result.color}`;
                    
                    // Atualizar texto
                    strengthText.textContent = password.length > 0 ? result.strength : '';
                    strengthText.className = `text-xs font-medium ${result.color.replace('bg-', 'text-')}`;
                });
            }
        });
    </script>
</section>
