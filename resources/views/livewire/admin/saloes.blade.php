<div>
    {{-- Conteúdo principal --}}
     @if(session()->has('message'))
     <div class="bg-teal-100 border-t-4  border-b-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-4" role="alert">
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg>
            </div>
            <div>
            <p class="font-bold">{{ session('message') }}</p>
            </div>
        </div>
     </div>
     @endif

    
    <div class="flex justify-end mb-4">
        <button class="btn btn-primary" wire:click="createSalon">Novo Salão</button>   
       
       
    </div>
        
    <table class="table-auto w-full mt-4">
        <thead>
            <tr class='bg-gray-100 text-left'>
                <th></th>
                <th>Nome</th>
                <th>Plano</th>
                <th>Cidade</th>

                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saloes as $index => $salon)
            <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                <td>
                    <div class="flex w-16 items-center">
                        <img src="{{asset($salon['logo'])}}" alt="Logo">
                    </div>
                </td>
                <td>{{$salon['id']}}</td>
                @if($salon['plan'] && $salon['status'])
                <td>{{$salon['plan']}} - {{$salon['status']}}</td>
                @else
                <td></td>
                @endif
                @if($salon['city'] && $salon['state'])
                <td>{{$salon['city']}}, {{$salon['state']}}</td>
                @else
                <td></td>
                @endif
                <td class="text-center">
                    <button class="btn btn-primary" wire:click.prevent="editSalon({{$index}})">Editar</button>
                    <button class="btn btn-danger" wire:click.prevent="deleteSalon('{{$salon['id']}}')">Apagar</button>
                </td>
            </tr>
            @endforeach                
        </tbody>
    </table>
    
    
    
    {{-- Painel de edição --}}
    @if($editedSalonIndex !== null)
        <div class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-lg z-50 p-8 overflow-auto">
            <h2 class="text-xl font-bold mb-4">Editar Salão</h2>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Logo</label>
                <input type="file" class="w-full border rounded px-2 py-1" wire:model="logoFile" accept="image/png,image/jpeg,image/jpg">
                @if($logoFile)
                    <div class="mt-2">
                        <img src="{{ $logoFile->temporaryUrl() }}" alt="Prévia da logo" class="h-16 w-auto rounded shadow">
                    </div>
                @elseif(!empty($saloes[$editedSalonIndex]['logo']))
                    <div class="mt-2">
                        <img src="/{{ $saloes[$editedSalonIndex]['logo'] }}" alt="Logo atual" class="h-16 w-auto rounded shadow">
                    </div>
                @endif
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Tipo de Estabelecimento</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.type">
                    <option value="">Selecione...</option>
                    <option value="Barbearia">Barbearia</option>
                    <option value="SalaoBeleza">Salão de Beleza</option>
                    <option value="Spa">Spa</option>
                    <option value="Estetica">Clinica Estética</option>
                    <option value="PetShop">PetShop</option>
                    <option value="Veterinaria">Clínica Veterinária</option>
                    
                
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">ID</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.id">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Endereço</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.address">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Número</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.number">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Complemento</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.complement">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Bairro</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.neighborhood">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">CEP</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.cep">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Cidade</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.city">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Estado</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.state">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Email</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.email">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Plano</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.plan">
            </div>
            
            <div class="mb-4">
                <label class="block font-semibold mb-1">Status</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="saloes.{{$editedSalonIndex}}.status">
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-success" wire:click.prevent="saveSalon({{$editedSalonIndex}})">Salvar</button>
                <button class="btn btn-secondary" wire:click.prevent="$set('editedSalonIndex', null)">Cancelar</button>
            </div>
        </div>
    @endif
    
    {{-- Painel de criação --}} 
    @if($showCreateSalonPanel)
        <div class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-lg z-50 p-8 overflow-auto">
            <h2 class="text-xl font-bold mb-4">Criar Novo Salão</h2>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Logo</label>
                <input type="file" class="w-full border rounded px-2 py-1" wire:model="logoFile" accept="image/png,image/jpeg,image/jpg">
                @if($logoFile)
                    <div class="mt-2">
                        <img src="{{ $logoFile->temporaryUrl() }}" alt="Prévia da logo" class="h-16 w-auto rounded shadow">
                    </div>
                @endif
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Tipo de Estabelecimento</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.type">
                    <option value="">Selecione...</option>
                    <option value="Barbearia">Barbearia</option>
                    <option value="SalaoBeleza">Salão de Beleza</option>
                    <option value="Estetica">Clinica Estética</option>
                    <option value="Veterinaria">Clínica Veterinária</option>    
                    <option value="Spa">Spa</option>
                    <option value="PetShop">PetShop</option>               
                </select>
            </div>
            <div class="mb-4">
                <!-- Campo ID removido, será preenchido automaticamente com o valor do slug -->
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Email</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.email">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Telefone</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.phone">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Instagram</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.instagram">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Facebook</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.facebook">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Nome</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.name">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Nome Fantasia
                    <span class="ml-1 relative group">
                        <svg class="w-4 h-4 text-blue-400 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="white"/>
                            <text x="12" y="16" text-anchor="middle" font-size="12" fill="currentColor">?</text>
                        </svg>
                        <span class="absolute left-6 top-0 z-10 hidden group-hover:block bg-blue-50 text-blue-900 text-xs rounded shadow-lg px-3 py-2 w-56">
                            O nome fantasia é o nome comercial pelo qual o salão será conhecido pelos clientes. Pode ser diferente do nome legal.
                        </span>
                    </span>
                </label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.fantasy_name">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">CNPJ</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.cnpj">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">slug
                    <span class="ml-1 relative group">
                        <svg class="w-4 h-4 text-blue-400 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="white"/>
                            <text x="12" y="16" text-anchor="middle" font-size="12" fill="currentColor">?</text>
                        </svg>
                        <span class="absolute left-6 top-0 z-10 hidden group-hover:block bg-blue-50 text-blue-900 text-xs rounded shadow-lg px-3 py-2 w-56">
                            O slug é uma versão simplificada do nome, sem espaços ou acentos, usada para criar o endereço do salão no sistema (exemplo: "meu-salao").
                        </span>
                    </span>
                </label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.slug">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">CEP</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.cep">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Rua, Av. etc.</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.address">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Número</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.number">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Complemento</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.complement">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Bairro</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.neighborhood">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Cidade</label>
                <input type="text" class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.city">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Estado</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.state">
                    <option value="">Selecione...</option>
                    <option value="AC">AC</option>
                    <option value="AL">AL</option>
                    <option value="AP">AP</option>
                    <option value="AM">AM</option>
                    <option value="BA">BA</option>
                    <option value="CE">CE</option>
                    <option value="DF">DF</option>
                    <option value="ES">ES</option>
                    <option value="GO">GO</option>
                    <option value="MA">MA</option>
                    <option value="MT">MT</option>
                    <option value="MS">MS</option>
                    <option value="MG">MG</option>
                    <option value="PA">PA</option>
                    <option value="PB">PB</option>
                    <option value="PR">PR</option>
                    <option value="PE">PE</option>
                    <option value="PI">PI</option>
                    <option value="RJ">RJ</option>
                    <option value="RN">RN</option>
                    <option value="RS">RS</option>
                    <option value="RO">RO</option>
                    <option value="RR">RR</option>
                    <option value="SC">SC</option>
                    <option value="SP">SP</option>
                    <option value="SE">SE</option>
                    <option value="TO">TO</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Plano</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.plan">
                    <option value="">Selecione...</option>
                    <option value="Básico">Básico</option>
                    <option value="Intermediário">Intermediário</option>
                    <option value="Avançado">Avançado</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-1">Status</label>
                <select class="w-full border rounded px-2 py-1" wire:model.defer="newSalon.status">
                    <option value="">Selecione...</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button class="btn btn-success" wire:click.prevent="saveNewSalon">Criar</button>
                <button class="btn btn-secondary" wire:click.prevent="$set('showCreateSalonPanel', false)">Cancelar</button>
            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('livewire:salonLogoUpdated', () => {
        window.location.reload();
    });
</script>