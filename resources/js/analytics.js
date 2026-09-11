export function track(event, data = {}) {
    if (typeof window !== 'undefined' && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({ event, ...data });
    }
}

function trackViewOnce(selector, eventName) {
    const element = document.querySelector(selector);
    if (!element || !('IntersectionObserver' in window)) {
        return;
    }

    let fired = false;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting && !fired) {
                fired = true;
                track(eventName);
                observer.disconnect();
            }
        });
    }, { threshold: 0.4 });

    observer.observe(element);
}

export function initAnalytics() {
    if (typeof window === 'undefined' || !Array.isArray(window.dataLayer)) {
        return;
    }

    track('page_view', { path: window.location.pathname });

    // Eventos de vista por página para las etapas del funnel.
    const path = window.location.pathname;

    if (path === '/precios') {
        track('pricing_view');
    } else if (path === '/como-funciona') {
        track('how_it_works_view');
    } else if (path === '/gracias') {
        // Proxy del lead creado: la página solo debe alcanzarse tras enviar el formulario.
        track('lead_created');
    } else if (path.startsWith('/analisis/')) {
        track('analysis_view');
    }

    // Visibilidad de calculadora y formulario (etapas intermedias del funnel).
    trackViewOnce('#calculadora', 'calculator_view');
    trackViewOnce('#lead-form', 'lead_form_view');

    // CTAs: genéricos -> cta_clicked; el CTA del resultado -> calculator_cta_click.
    document.querySelectorAll('[data-track-cta]').forEach((element) => {
        element.addEventListener('click', () => {
            const label = element.getAttribute('data-track-cta');

            if (label === 'result') {
                const amountEl = document.querySelector('[data-calculator-result]');
                const amount = amountEl?.dataset.amount;
                track('calculator_cta_click', {
                    label,
                    amount: amount ? Number(amount) : undefined,
                });

                return;
            }

            track('cta_clicked', { label });
        });
    });

    const leadForm = document.querySelector('[data-lead-form]');
    if (leadForm) {
        leadForm.addEventListener('focusin', () => {
            track('lead_form_start');
        }, { once: true });

        leadForm.addEventListener('submit', () => {
            track('lead_form_submit');
        }, { once: true });
    }
}