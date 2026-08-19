@if(tenant() && !tenant()->onboarding_completed)
    <div class="mt-8 flex items-center justify-between gap-4 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
        <div>
            <p class="text-sm font-semibold text-indigo-900">Próxima etapa</p>
            <p class="text-sm text-indigo-700">{{ $description }}</p>
        </div>
        <a href="{{ $route }}" class="inline-flex shrink-0 items-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition-colors hover:bg-indigo-700">
            Prosseguir
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7m0 0H6"></path>
            </svg>
        </a>
    </div>
@endif
