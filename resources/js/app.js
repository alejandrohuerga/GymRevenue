import { initCalculator } from './calculator';

initCalculator();

const menuToggle = document.querySelector('[data-menu-toggle]');
if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        const menu = document.querySelector('[data-menu]');
        if (!menu) {
            return;
        }
        const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', String(!expanded));
        menu.hidden = expanded;
    });
}