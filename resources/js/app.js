import './bootstrap';

import mask from '@alpinejs/mask';

// Registrar plugin mask no Alpine do Livewire
document.addEventListener('livewire:init', () => {
    if (window.Alpine) {
        window.Alpine.plugin(mask);
    }
});

