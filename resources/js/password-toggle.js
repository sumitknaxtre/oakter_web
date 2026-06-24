const EYE_OPEN_ICON = `
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
  <circle cx="12" cy="12" r="3" />
</svg>`;

const EYE_CLOSED_ICON = `
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94" />
  <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19" />
  <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24" />
  <line x1="1" y1="1" x2="23" y2="23" />
</svg>`;

function enhancePasswordInput(input) {
    if (input.closest('.password-toggle-wrap')) {
        return;
    }

    const wrap = document.createElement('div');
    wrap.className = 'password-toggle-wrap';

    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'password-toggle-button';
    button.setAttribute('aria-label', 'Show password');
    button.setAttribute('aria-pressed', 'false');
    button.innerHTML = EYE_OPEN_ICON;

    button.addEventListener('click', () => {
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        button.setAttribute('aria-pressed', String(!isVisible));
        button.innerHTML = isVisible ? EYE_OPEN_ICON : EYE_CLOSED_ICON;
    });

    wrap.appendChild(button);
}

export function initPasswordToggles(root = document) {
    root.querySelectorAll('input[type="password"]').forEach(enhancePasswordInput);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initPasswordToggles());
} else {
    initPasswordToggles();
}
