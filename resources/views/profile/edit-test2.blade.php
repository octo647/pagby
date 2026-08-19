<x-app-layout>
    <x-slot name="header">
        <h2>TESTE 2 - Perfil</h2>
    </x-slot>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: blue; margin-bottom: 20px;">CARD 1 - Dados Pessoais</h1>
        <p>Conteúdo do card 1</p>
    </div>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: red; margin-bottom: 20px;">CARD 2 - Alterar Senha</h1>
        
        <form method="post" action="{{ route('tenant.password.update') }}">
            @csrf
            @method('put')

            <div style="margin-bottom: 15px;">
                <label>Senha Atual</label><br>
                <input name="current_password" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <div style="margin-bottom: 15px;">
                <label>Nova Senha</label><br>
                <input name="password" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <div style="margin-bottom: 15px;">
                <label>Confirmar Senha</label><br>
                <input name="password_confirmation" type="password" style="border: 1px solid #ccc; padding: 8px; width: 300px;" />
            </div>

            <button type="submit" style="background: #333; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                ATUALIZAR SENHA
            </button>
        </form>
    </div>

    <div style="background: white; padding: 20px; margin: 20px;">
        <h1 style="font-size: 24px; color: green; margin-bottom: 20px;">CARD 3 - Deletar Conta</h1>
        <p>Conteúdo do card 3</p>
    </div>

</x-app-layout>
