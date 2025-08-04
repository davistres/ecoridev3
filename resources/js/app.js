import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        link.addEventListener('mouseenter', () => {
            if (!link.classList.contains('font-bold')) {
                link.classList.add('border-b-2', 'border-green-500');
            }
        });

        link.addEventListener('mouseleave', () => {
            if (!link.classList.contains('font-bold')) {
                link.classList.remove('border-b-2', 'border-green-500');
            }
        });
    });
});
