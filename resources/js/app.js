import './bootstrap';
import './popup.js';
import './covoit-form-validation.js';
import './driverinfo-form-validation.js';
import './edit-preferences-form-validation.js';
import './addcovoit-addvehicle-validation.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        if (link.classList.contains('font-bold')) {
            link.style.pointerEvents = 'none';
        }

        link.addEventListener('mouseenter', () => {
            if (!link.classList.contains('font-bold')) {
                link.classList.add('transition', 'duration-1000', 'ease-in-out', 'border-b-2', 'border-green-500');
            }
        });

        link.addEventListener('mouseleave', () => {
            if (!link.classList.contains('font-bold')) {
                link.classList.remove('border-b-2', 'border-green-500');
            }
        });
    });
});
