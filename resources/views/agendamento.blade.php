<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Novo agendamento') }}
        </h2>
    </x-slot>

    <div class="p-4 mx-auto max-w-4xl flex flex-col gap-4">
        <div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            @livewire('branches')
        </div>
        <div id="choose-employee" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            @livewire('choose-employee')
        </div>
        <div id="choose-service" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            @livewire('choose-service')
        </div>
        <div id="agenda" class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
            @livewire('make-appointment')
        </div>
    </div>
</x-app-layout>