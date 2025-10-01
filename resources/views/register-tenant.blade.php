<x-pagby-layout>
<!-- resources/views/register-tenant.blade.php -->
<!-- Faz o registro de um novo salão -->

<div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Exibir erros gerais --}}
        @if ($errors->has('general'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $errors->first('general') }}
            </div>
        @endif

        {{-- Exibir todos os erros de validação --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-2xl font-bold mb-6">Registre Seu Salão</h2>
        <form action="{{ route('register-tenant') }}" method="POST">
            @csrf
            <!-- Campos do formulário -->
            
            <div class="mb-4">
                <label for="owner_name" class="block text-sm font-medium text-gray-700">Nome do Proprietário</label>
                <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('owner_name') border-red-500 @enderror">
                @error('owner_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email do Proprietário</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone do Proprietário</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <h2 class="text-lg font-semibold mb-4">Dados do Salão</h2>
            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <div class="space-y-2">
                    <div>
                        <input type="radio" id="barbearia" name="tipo" value="Barbearia" 
                               {{ old('tipo') == 'Barbearia' ? 'checked' : '' }} class="mr-2">
                        <label for="barbearia" class="mr-4">Barbearia</label>
                    </div>
                    <div>
                        <input type="radio" id="salao-beleza" name="tipo" value="Salão de Beleza" 
                               {{ old('tipo') == 'Salão de Beleza' ? 'checked' : '' }} class="mr-2">
                        <label for="salao-beleza" class="mr-4">Salão de Beleza</label>
                    </div>
                    <div>
                        <input type="radio" id="outro" name="tipo" value="Outro" 
                               {{ old('tipo') == 'Outro' ? 'checked' : '' }} class="mr-2">
                        <label for="outro" class="mr-4">Outro</label>
                    </div>
                </div>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="salon_name" class="block text-sm font-medium text-gray-700">Nome do Salão</label>
                <input type="text" id="salon_name" name="salon_name" value="{{ old('salon_name') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('salon_name') border-red-500 @enderror">
                @error('salon_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Rua e número</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-500 @enderror">
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="neighborhood" class="block text-sm font-medium text-gray-700">Bairro</label>
                <input type="text" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('neighborhood') border-red-500 @enderror">
                @error('neighborhood')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" id="city" name="city" value="{{ old('city') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('city') border-red-500 @enderror">
                @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" id="state" name="state" value="{{ old('state') }}" 
                       required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state') border-red-500 @enderror">
                @error('state')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">Registrar</button>
            </div>  
        </form>
    </div>
</div>
 

</x-pagby-layout>
