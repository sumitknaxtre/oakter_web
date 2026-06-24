import './password-toggle.js';

function initAdminCustomerDialog() {
    const dialog = document.getElementById('admin-customer-dialog');

    if (!dialog) {
        return;
    }

    const body = dialog.querySelector('[data-customer-dialog-body]');
    const subtitle = dialog.querySelector('[data-customer-dialog-subtitle]');

    document.querySelectorAll('[data-customer-dialog-target]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const template = document.getElementById(trigger.getAttribute('data-customer-dialog-target'));

            if (!template || !body) {
                return;
            }

            body.replaceChildren(template.content.cloneNode(true));

            if (subtitle) {
                subtitle.textContent = trigger.getAttribute('data-order-label') ?? '';
            }

            dialog.showModal();
        });
    });

    dialog.querySelectorAll('[data-customer-dialog-close]').forEach((control) => {
        control.addEventListener('click', () => dialog.close());
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
}

initAdminCustomerDialog();
