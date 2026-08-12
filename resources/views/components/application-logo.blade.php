

@if(tenant() && tenant()->logo)
    <img src="/{{ tenant()->logo }}" alt="Logo do Salão" {{ $attributes->merge(['class' => 'object-cover rounded-full shadow-lg']) }}>

@else
    <img src="/images/logo.png" alt="Logo padrão" {{ $attributes->merge(['class' => 'object-cover rounded-full shadow-lg']) }}>
@endif