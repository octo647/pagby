function previewPhoto(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('photo-preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    const cepError = document.getElementById('cep-error');
    const street = document.getElementById('street');
    const neighborhood = document.getElementById('neighborhood');
    const city = document.getElementById('city');
    const state = document.getElementById('state');
    const whatsappCheckbox = document.getElementById('whatsapp');
    const whatsappActivateBtn = document.getElementById('whatsapp-activate-btn');
    const whatsappWarning = document.getElementById('whatsapp-not-checked-warning');
    const whatsappSection = document.getElementById('whatsapp-reminders-section');

    // Validação WhatsApp
    function checkWhatsAppStatus() {
        if (!whatsappCheckbox || !whatsappActivateBtn) return;

        const isChecked = whatsappCheckbox.checked;

        if (isChecked) {
            whatsappActivateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            whatsappActivateBtn.classList.add('hover:bg-green-700');
            if (whatsappWarning) whatsappWarning.classList.add('hidden');
            whatsappSection.classList.remove('bg-yellow-50', 'border-yellow-200');
            whatsappSection.classList.add('bg-green-50', 'border-green-200');
        } else {
            whatsappActivateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            whatsappActivateBtn.classList.remove('hover:bg-green-700');
            if (whatsappWarning) whatsappWarning.classList.remove('hidden');
            whatsappSection.classList.remove('bg-green-50', 'border-green-200');
            whatsappSection.classList.add('bg-yellow-50', 'border-yellow-200');
        }
    }

    // Previne clique se não estiver marcado
    if (whatsappActivateBtn) {
        whatsappActivateBtn.addEventListener('click', function(e) {
            if (!whatsappCheckbox.checked) {
                e.preventDefault();
                alert('⚠️ Marque "É WhatsApp?" acima e salve suas alterações antes de ativar os lembretes.');
                whatsappCheckbox.focus();
            }
        });
    }

    if (whatsappCheckbox) {
        whatsappCheckbox.addEventListener('change', checkWhatsAppStatus);
        checkWhatsAppStatus(); // Verifica no carregamento
    }

    // CEP validation
    function validarCep(cep) {
        cep = cep.replace(/\D/g, '');
        if (cep.length !== 8) return false;
        // Consulta API para validar existência real
        return fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(res => res.json())
            .then(data => !data.erro);
    }
    if (cepInput && street && neighborhood && city && state) {
        cepInput.addEventListener('blur', function() {
            const valor = cepInput.value;
            // Limpa campos antes de buscar
            street.value = '';
            neighborhood.value = '';
            city.value = '';
            state.value = '';
            if (!/^\d{5}-?\d{3}$/.test(valor)) {
                cepError.textContent = 'Formato de CEP inválido.';
                cepError.classList.remove('hidden');
                return;
            }
            const cep = valor.replace(/\D/g, '');
            if (cep.length === 8) {
                // Mostra carregando
                street.value = city.value = neighborhood.value = state.value = '...';
                fetch('https://viacep.com.br/ws/' + cep + '/json/')
                    .then(response => response.json())
                    .then(data => {
                        alert('ViaCEP resposta: ' + JSON.stringify(data)); // TESTE VISUAL
                        if (!data.erro) {
                            street.value = data.logradouro || '';
                            neighborhood.value = data.bairro || '';
                            city.value = data.localidade || '';
                            state.value = data.uf || '';
                            cepError.classList.add('hidden');
                        } else {
                            street.value = city.value = neighborhood.value = state.value = '';
                            cepError.textContent = 'CEP não encontrado. Verifique e tente novamente.';
                            cepError.classList.remove('hidden');
                        }
                    })
                    .catch(() => {
                        alert('Erro ao buscar CEP!'); // TESTE VISUAL
                        street.value = city.value = neighborhood.value = state.value = '';
                        cepError.textContent = 'Erro ao buscar CEP. Tente novamente.';
                        cepError.classList.remove('hidden');
                    });
            }
        });
        cepInput.addEventListener('input', function() {
            cepError.classList.add('hidden');
        });
    }
});
