<x-app-layout>
<div class="hero-section bg-pink-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h1 class="text-5xl font-bold mb-4">Bem-vinda ao Nosso Salão</h1>
        <p class="text-xl mb-8">Beleza e cuidados especiais para realçar sua autoestima</p>
        <a href="#agendamento" class="bg-white text-pink-500 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
            Agendar Horário
        </a>
    </div>
</div>

<div class="services-section py-16">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nossos Serviços</h2>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Corte</h3>
                <p>Cortes modernos e personalizados</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Escova</h3>
                <p>Escova e modelagem profissional</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Coloração</h3>
                <p>Cores vibrantes e naturais</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Manicure</h3>
                <p>Cuidados completos para suas unhas</p>
            </div>
        </div>
    </div>
</div>

<div class="gallery-section py-16 bg-gray-50">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nossa Galeria</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Aqui serão carregadas as imagens dos trabalhos -->
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/escova.jpeg" class="w-full h-full object-cover rounded-lg" alt="Escova">
                </div>
                <p class="text-center">Corte e Escova</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/coloracao.jpeg" class="w-full h-full object-cover rounded-lg" alt="Coloração">
                </div>
                <p class="text-center">Coloração</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/manicure.jpeg" class="w-full h-full object-cover rounded-lg" alt="Manicure">
                </div>
                <p class="text-center">Manicure</p>
            </div>
        </div>
    </div>
</div>

<div class="contact-section bg-pink-100 py-16">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold mb-8">Entre em Contato</h2>
        <p class="text-lg mb-4">Agende seu horário e venha cuidar da sua beleza</p>
        <div class="flex justify-center space-x-8">
            <div>
                <strong>Telefone:</strong> (11) 99999-9999
            </div>
            <div>
                <strong>Endereço:</strong> Rua Example, 123
            </div>
        </div>
    </div>
</div>
</x-app-layout>