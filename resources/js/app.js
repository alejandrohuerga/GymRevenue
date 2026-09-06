import { initCalculator } from './calculator';
import { initAnalytics } from './analytics';

initCalculator();
initAnalytics();

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

// Previene el doble envío del formulario de captación y comunica el estado.
const leadForm = document.querySelector('[data-lead-form]');
if (leadForm) {
    leadForm.addEventListener('submit', () => {
        const submit = leadForm.querySelector('button[type="submit"]');
        if (!submit) {
            return;
        }
        const csvInput = leadForm.querySelector('input[type="file"][name="csv"]');
        const hasCsv = csvInput instanceof HTMLInputElement && Boolean(csvInput.files?.length);
        submit.disabled = true;
        submit.textContent = hasCsv ? 'Analizando...' : 'Enviando...';
    });
}