<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil - TESTE
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold mb-4">Dados Pessoais</h2>
                    <p>Formulário de dados pessoais aqui...</p>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold text-red-600 mb-4">TESTE DIRETO - Alterar Senha</h2>
                    
                    <form method="post" action="{{ route('tenant.password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label for="current_password" class="block font-medium text-sm text-gray-700">Senha Atual</label>
                            <input id="current_password" name="current_password" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" />
                        </div>

                        <div>
                            <label for="password" class="block font-medium text-sm text-gray-700">Nova Senha</label>
                            <input id="password" name="password" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Nova Senha</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Atualizar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold mb-4">Deletar Conta</h2>
                    <p>Formulário de deletar conta aqui...</p>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
