import { track } from './analytics';

const RECOVERY_RATE_INACTIVE = 0.2;
const RECOVERY_RATE_CANCELLATION = 0.15;

export function estimateOpportunity({ averageFee, inactiveMembers, monthlyCancellations }) {
    if (
        !Number.isFinite(averageFee) ||
        !Number.isFinite(inactiveMembers) ||
        !Number.isFinite(monthlyCancellations)
    ) {
        return null;
    }

    const inactiveOpportunity = inactiveMembers * averageFee * RECOVERY_RATE_INACTIVE;
    const cancellationOpportunity = monthlyCancellations * averageFee * RECOVERY_RATE_CANCELLATION;
    const monthlyOpportunity = inactiveOpportunity + cancellationOpportunity;

    return {
        monthly: Math.round(monthlyOpportunity * 100) / 100,
        annual: Math.round(monthlyOpportunity * 12 * 100) / 100,
        inactive: Math.round(inactiveOpportunity * 100) / 100,
        cancellation: Math.round(cancellationOpportunity * 100) / 100,
    };
}

export function formatMoney(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    }).format(value);
}

export function initCalculator() {
    const form = document.querySelector('[data-calculator-form]');
    if (!form) {
        return;
    }

    const submit = form.querySelector('[data-calculator-submit]');
    const result = document.querySelector('[data-calculator-result]');
    const amount = result?.querySelector('[data-calculator-amount]');
    const annual = result?.querySelector('[data-calculator-annual]');
    const breakdownInactive = result?.querySelector('[data-calculator-inactive]');
    const breakdownCancellation = result?.querySelector('[data-calculator-cancellation]');

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

    const readInteger = (key) => {
        const value = field(key)?.value.trim();
        if (value === '') {
            return null;
        }
        const number = Number(value);
        if (!Number.isFinite(number) || !Number.isInteger(number)) {
            return null;
        }
        return number;
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

    if (!submit || !result || !amount || !annual || !breakdownInactive || !breakdownCancellation) {
        return;
    }

    submit.addEventListener('click', () => {
        track('calculator_started');

        fieldKeys.forEach(clearError);

        const members = readInteger('members');
        const averageFee = readNumber('average_fee');
        const inactiveMembers = readInteger('inactive_members');
        const monthlyCancellations = readInteger('monthly_cancellations');

        let valid = true;

        if (members === null || members < 1) {
            showError('members', 'Introduce un número entero mayor que cero.');
            valid = false;
        }
        if (averageFee === null || averageFee <= 0) {
            showError('average_fee', 'Introduce una cuota mayor que cero.');
            valid = false;
        }
        if (inactiveMembers === null || inactiveMembers < 0) {
            showError('inactive_members', 'Introduce un número entero mayor o igual que cero.');
            valid = false;
        }
        if (monthlyCancellations === null || monthlyCancellations < 0) {
            showError('monthly_cancellations', 'Introduce un número entero mayor o igual que cero.');
            valid = false;
        }
        if (members !== null && inactiveMembers !== null && inactiveMembers > members) {
            showError('inactive_members', 'Los socios inactivos no pueden superar el total de socios.');
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

        if (opportunity === null) {
            return;
        }

        amount.textContent = `${formatMoney(opportunity.monthly)} `;
        annual.textContent = `≈ ${formatMoney(opportunity.annual)}/año`;
        breakdownInactive.textContent = formatMoney(opportunity.inactive);
        breakdownCancellation.textContent = formatMoney(opportunity.cancellation);

        setLeadField('members', members);
        setLeadField('average_fee', averageFee);
        setLeadField('inactive_members', inactiveMembers);
        setLeadField('monthly_cancellations', monthlyCancellations);
        setLeadField('estimated_opportunity', opportunity.monthly);

        result.hidden = false;
        result.classList.remove('calc-reveal');
        void result.offsetWidth;
        result.classList.add('calc-reveal');
        result.focus({ preventScroll: true });
        result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        track('calculator_completed', { amount: opportunity.monthly });
    });

    result.addEventListener('animationend', () => {
        result.classList.remove('calc-reveal');
    }, { once: true });
}