export function track(event, data = {}) {
    if (typeof window !== 'undefined' && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({ event, ...data });
    }
}

export function initAnalytics() {
    if (typeof window === 'undefined' || !Array.isArray(window.dataLayer)) {
        return;
    }

    track('page_view', { path: window.location.pathname });

    document.querySelectorAll('[data-track-cta]').forEach((element) => {
        element.addEventListener('click', () => {
            track('cta_clicked', { label: element.getAttribute('data-track-cta') });
        });
    });

    const leadForm = document.querySelector('[data-lead-form]');
    if (leadForm) {
        leadForm.addEventListener('focusin', () => {
            track('lead_form_started');
        }, { once: true });

        leadForm.addEventListener('submit', () => {
            track('lead_submitted');
        }, { once: true });
    }
}