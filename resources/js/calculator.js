export function estimateOpportunity({ averageFee, inactiveMembers, monthlyCancellations }) {
    if (
        !Number.isFinite(averageFee) ||
        !Number.isFinite(inactiveMembers) ||
        !Number.isFinite(monthlyCancellations)
    ) {
        return 0;
    }

    const monthlyValue = (inactiveMembers + monthlyCancellations) * averageFee;

    return Math.round(monthlyValue * 0.5 * 100) / 100;
}

export function formatMoney(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    }).format(value);
}

function track(event, data = {}) {
    if (typeof window !== 'undefined' && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({ event, ...data });
    }
}

export function initCalculator() {
    const form = document.querySelector('[data-calculator-form]');
    if (!form) {
        return;
    }

    const submit = form.querySelector('[data-calculator-submit]');
    const result = document.querySelector('[data-calculator-result]');
    const amount = result?.querySelector('[data-calculator-amount]');
    const fieldKeys = ['members', 'average_fee', 'inactive_members', 'monthly_cancellations'];

    const field = (key) => form.querySelector(`[name="${key}"]`);

    const showError = (key, message) => {
        const input = field(key);
        const error = form.querySelector(`[data-error-for="${key}"]`);
        if (input) {
            input.classList.add('border-red-500');
        }
        if (error) {
            error.textContent = message;
            error.classList.remove('hidden');
        }
    };

    const clearError = (key) => {
        const input = field(key);
        const error = form.querySelector(`[data-error-for="${key}"]`);
        if (input) {
            input.classList.remove('border-red-500');
        }
        if (error) {
            error.classList.add('hidden');
        }
    };

    const readNumber = (key) => {
        const value = field(key)?.value.trim();
        if (value === '') {
            return null;
        }
        const number = Number(value);
        return Number.isFinite(number) ? number : null;
    };

    const setLeadField = (key, value) => {
        const leadForm = document.querySelector('[data-lead-form]');
        const input = leadForm?.querySelector(`input[name="${key}"]`);
        if (input) {
            input.value = value;
        }
    };

    fieldKeys.forEach((key) => {
        field(key)?.addEventListener('input', () => clearError(key));
    });

    if (!submit || !result || !amount) {
        return;
    }

    submit.addEventListener('click', () => {
        track('calculator_started');

        fieldKeys.forEach(clearError);

        const members = readNumber('members');
        const averageFee = readNumber('average_fee');
        const inactiveMembers = readNumber('inactive_members');
        const monthlyCancellations = readNumber('monthly_cancellations');

        let valid = true;

        if (members === null || members < 0) {
            showError('members', 'Introduce un número válido.');
            valid = false;
        }
        if (averageFee === null || averageFee < 0) {
            showError('average_fee', 'Introduce una cuota válida.');
            valid = false;
        }
        if (inactiveMembers === null || inactiveMembers < 0) {
            showError('inactive_members', 'Introduce un número válido.');
            valid = false;
        }
        if (monthlyCancellations === null || monthlyCancellations < 0) {
            showError('monthly_cancellations', 'Introduce un número válido.');
            valid = false;
        }

        if (!valid) {
            return;
        }

        const opportunity = estimateOpportunity({
            averageFee,
            inactiveMembers,
            monthlyCancellations,
        });

        amount.textContent = `${formatMoney(opportunity)} `;
        setLeadField('members', members);
        setLeadField('average_fee', averageFee);
        setLeadField('inactive_members', inactiveMembers);
        setLeadField('monthly_cancellations', monthlyCancellations);
        setLeadField('estimated_opportunity', opportunity);

        result.hidden = false;
        result.classList.remove('calc-reveal');
        void result.offsetWidth;
        result.classList.add('calc-reveal');
        result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        track('calculator_completed', { amount: opportunity });
    });

    result.addEventListener('animationend', () => {
        result.classList.remove('calc-reveal');
    }, { once: true });
}