<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Novo agendamento') }}
        </h2>
    </x-slot>
     @if (session()->has('message') || session()->has('warning'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">{{ session('message') }}</span>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    <div 
        x-data="{
            branchCount: 0,
            chosenBranch: null,
            professionalChosen: false,
            serviceChosen: false,
            showAgenda: false,
        }"
        x-init="
            window.addEventListener('branchesCount', e => { branchCount = e.detail.count });
            window.addEventListener('branchChosen', e => { chosenBranch = e.detail.branch });
            window.addEventListener('professionalChosen', e => { professionalChosen = true });
            window.addEventListener('serviceChosen', e => { serviceChosen = true });
            window.addEventListener('showAgenda', e => { showAgenda = true });
        "
        class="p-4 mx-auto max-w-4xl flex flex-col gap-4"
    >
        <!-- Card Filial -->
        <div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            @livewire('branches')
        </div>

        <!-- Card Profissional -->
        <div id="choose-employee" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto" x-cloak
            x-show="!showAgenda"
            x-transition>
            @livewire('cliente.choose-employee')
        </div>

        <!-- Card Horário -->
        <div id="agenda" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto"
            x-show="professionalChosen && serviceChosen"
            x-transition
            x-init="$watch('professionalChosen', v => { if(v && serviceChosen) showAgenda = true }); $watch('serviceChosen', v => { if(v && professionalChosen) showAgenda = true });"
        >
            @livewire('cliente.make-appointment')
        </div>
    </div>
</x-app-layout>