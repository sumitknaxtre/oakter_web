import './password-toggle.js';
import './meta-pixel.js';

const PENDING_PAYMENT_KEY = 'oakter_pending_payment';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkout-form');
    const payButton = document.getElementById('pay-now-button');
    const errorBox = document.getElementById('checkout-error');
    const billingFields = document.getElementById('billing-fields');
    const billingRadios = form?.querySelectorAll('input[name="billing_same_as_shipping"]');
    const lookupUrl = window.oakterCheckout?.lookupUrl;
    const couponUrl = window.oakterCheckout?.couponUrl;
    const verifyUrl = window.oakterCheckout?.verifyUrl;
    const couponInput = document.getElementById('coupon-input');
    const couponApplyButton = document.getElementById('coupon-apply-button');
    const couponCodeField = document.getElementById('coupon-code');
    const couponMessage = document.getElementById('coupon-message');
    const couponApplied = document.getElementById('coupon-applied');
    const couponAppliedCode = document.getElementById('coupon-applied-code');
    const couponRemoveButton = document.getElementById('coupon-remove-button');
    const discountRow = document.getElementById('checkout-discount-row');
    const subtotalEl = document.getElementById('checkout-subtotal');
    const discountEl = document.getElementById('checkout-discount');
    const totalEl = document.getElementById('checkout-total');
    const taxEl = document.getElementById('checkout-tax');

    if (!form || !payButton) {
        return;
    }

    const csrfToken = () => form.querySelector('input[name="_token"]')?.value ?? '';

    const savePendingPayment = (payment) => {
        try {
            sessionStorage.setItem(PENDING_PAYMENT_KEY, JSON.stringify(payment));
        } catch {
            // Ignore storage failures.
        }
    };

    const clearPendingPayment = () => {
        try {
            sessionStorage.removeItem(PENDING_PAYMENT_KEY);
        } catch {
            // Ignore storage failures.
        }
    };

    const verifyPayment = async (paymentResponse) => {
        if (!verifyUrl) {
            throw new Error('Payment verification is not configured.');
        }

        savePendingPayment(paymentResponse);

        const verifyResponse = await fetch(verifyUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                razorpay_order_id: paymentResponse.razorpay_order_id,
                razorpay_payment_id: paymentResponse.razorpay_payment_id,
                razorpay_signature: paymentResponse.razorpay_signature,
                fbp: window.oakterMeta?.getFbp?.() ?? null,
                fbc: window.oakterMeta?.getFbc?.() ?? null,
            }),
        });

        const verifyPayload = await verifyResponse.json();

        if (!verifyResponse.ok) {
            throw new Error(verifyPayload.message ?? 'Payment verification failed.');
        }

        clearPendingPayment();
        window.location.href = verifyPayload.redirect;
    };

    const retryPendingPayment = async () => {
        if (!verifyUrl) {
            return;
        }

        let pending = null;

        try {
            const raw = sessionStorage.getItem(PENDING_PAYMENT_KEY);
            pending = raw ? JSON.parse(raw) : null;
        } catch {
            clearPendingPayment();

            return;
        }

        if (!pending?.razorpay_order_id || !pending?.razorpay_payment_id || !pending?.razorpay_signature) {
            return;
        }

        payButton.disabled = true;
        payButton.textContent = 'Confirming payment...';
        showError('');

        try {
            await verifyPayment(pending);
        } catch (error) {
            showError(error.message ?? 'We received your payment but could not confirm it. Please refresh or contact support.');
            payButton.disabled = false;
            payButton.textContent = 'Pay now';
        }
    };

    const setFieldValue = (name, value) => {
        if (value === null || value === undefined || value === '') {
            return;
        }

        const field = form.querySelector(`[name="${name}"]`);
        if (!field) {
            return;
        }

        field.value = value;
        field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const toggleBillingFields = () => {
        const useDifferentBilling = form.querySelector('input[name="billing_same_as_shipping"][value="0"]')?.checked;
        if (billingFields) {
            billingFields.hidden = !useDifferentBilling;
        }
    };

    billingRadios?.forEach((radio) => {
        radio.addEventListener('change', toggleBillingFields);
    });
    toggleBillingFields();

    const emailInput = form.querySelector('[name="email"]');
    let lastLookupEmail = '';

    const lookupCustomer = async () => {
        if (!lookupUrl || !emailInput) {
            return;
        }

        const email = emailInput.value.trim();
        if (!email || email === lastLookupEmail || !email.includes('@')) {
            return;
        }

        lastLookupEmail = email;

        try {
            const response = await fetch(`${lookupUrl}?email=${encodeURIComponent(email)}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            if (!payload.found) {
                return;
            }

            if (payload.user) {
                setFieldValue('first_name', payload.user.first_name);
                setFieldValue('last_name', payload.user.last_name);
                setFieldValue('phone', payload.user.phone);
            }

            if (payload.shipping_address) {
                const address = payload.shipping_address;
                setFieldValue('first_name', address.first_name);
                setFieldValue('last_name', address.last_name);
                setFieldValue('address_line1', address.address_line1);
                setFieldValue('address_line2', address.address_line2);
                setFieldValue('city', address.city);
                setFieldValue('state', address.state);
                setFieldValue('pincode', address.pincode);
                setFieldValue('country', address.country);
            }
        } catch {
            // Ignore lookup failures and let the customer continue manually.
        }
    };

    emailInput?.addEventListener('blur', lookupCustomer);

    const showCouponMessage = (message, type = '') => {
        if (!couponMessage) {
            return;
        }

        couponMessage.textContent = message;
        couponMessage.hidden = !message;
        couponMessage.classList.remove('is-success', 'is-error');

        if (type) {
            couponMessage.classList.add(type);
        }
    };

    const showAppliedCoupon = (code) => {
        if (couponAppliedCode) {
            couponAppliedCode.textContent = code;
        }

        if (couponApplied) {
            couponApplied.hidden = false;
        }

        if (couponInput) {
            couponInput.value = '';
        }
    };

    const hideAppliedCoupon = () => {
        if (couponApplied) {
            couponApplied.hidden = true;
        }

        if (couponAppliedCode) {
            couponAppliedCode.textContent = '';
        }
    };

    const resetCouponTotals = () => {
        const subtotalPaise = window.oakterCheckout?.subtotalPaise ?? 0;
        const subtotal = (subtotalPaise / 100).toFixed(2);
        const tax = (subtotalPaise / 100 - (subtotalPaise / 100 / 1.18)).toFixed(2);

        if (subtotalEl) subtotalEl.textContent = `₹${subtotal}`;
        if (discountRow) discountRow.hidden = true;
        if (discountEl) discountEl.textContent = '-₹0.00';
        if (totalEl) totalEl.textContent = `₹${subtotal}`;
        if (taxEl) taxEl.textContent = tax;
        if (couponCodeField) couponCodeField.value = '';
        if (couponApplyButton) couponApplyButton.classList.remove('is-applied');
        hideAppliedCoupon();
    };

    const applyCouponTotals = (payload) => {
        if (subtotalEl) subtotalEl.textContent = `₹${payload.subtotal}`;
        if (discountRow) discountRow.hidden = false;
        if (discountEl) discountEl.textContent = `-₹${payload.discount}`;
        if (totalEl) totalEl.textContent = `₹${payload.total}`;
        if (taxEl) taxEl.textContent = payload.tax;
        if (couponCodeField) couponCodeField.value = payload.code;
        if (couponApplyButton) couponApplyButton.classList.add('is-applied');
        showAppliedCoupon(payload.code);
        showCouponMessage('');
    };

    const removeCoupon = () => {
        resetCouponTotals();
        showCouponMessage('');
        couponInput?.focus();
    };

    const applyCoupon = async () => {
        if (!couponUrl || !couponInput) {
            return;
        }

        const code = couponInput.value.trim();
        if (!code) {
            resetCouponTotals();
            showCouponMessage('Enter a discount code to apply.', 'is-error');
            return;
        }

        showCouponMessage('');

        try {
            const response = await fetch(couponUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ coupon_code: code }),
            });

            const payload = await response.json();

            if (!response.ok) {
                resetCouponTotals();
                showCouponMessage(payload.message ?? 'This discount code is not valid.', 'is-error');
                return;
            }

            applyCouponTotals(payload);
        } catch {
            resetCouponTotals();
            showCouponMessage('Unable to apply discount code. Please try again.', 'is-error');
        }
    };

    couponApplyButton?.addEventListener('click', applyCoupon);
    couponRemoveButton?.addEventListener('click', removeCoupon);

    couponInput?.addEventListener('input', () => {
        if (couponApplied && !couponApplied.hidden) {
            return;
        }

        if (couponCodeField && couponInput.value.trim() !== couponCodeField.value) {
            resetCouponTotals();
            showCouponMessage('');
        }
    });

    const showError = (message) => {
        if (!errorBox) {
            return;
        }

        errorBox.textContent = message;
        errorBox.hidden = !message;
    };

    if (errorBox?.textContent?.trim()) {
        errorBox.hidden = false;
    }

    void retryPendingPayment();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');

        if (!form.reportValidity()) {
            return;
        }

        payButton.disabled = true;
        payButton.textContent = 'Processing...';

        try {
            const formData = new FormData(form);
            const fbp = window.oakterMeta?.getFbp?.();
            const fbc = window.oakterMeta?.getFbc?.();

            if (fbp) {
                formData.set('fbp', fbp);
            }

            if (fbc) {
                formData.set('fbc', fbc);
            }

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok) {
                const message = payload.message
                    ?? Object.values(payload.errors ?? {})[0]?.[0]
                    ?? 'Unable to start payment. Please check your details and try again.';
                throw new Error(message);
            }

            if (typeof Razorpay === 'undefined') {
                throw new Error('Razorpay checkout could not be loaded. Please refresh and try again.');
            }

            const callbackUrl = new URL(payload.callback_url, window.location.origin);
            const metaFbp = window.oakterMeta?.getFbp?.();
            const metaFbc = window.oakterMeta?.getFbc?.();

            if (metaFbp) {
                callbackUrl.searchParams.set('fbp', metaFbp);
            }

            if (metaFbc) {
                callbackUrl.searchParams.set('fbc', metaFbc);
            }

            const options = {
                key: payload.key,
                amount: payload.amount,
                currency: payload.currency,
                name: payload.name,
                description: payload.description,
                order_id: payload.razorpay_order_id,
                prefill: payload.prefill,
                notes: payload.notes,
                theme: payload.theme,
                callback_url: callbackUrl.toString(),
                redirect: true,
                modal: {
                    ondismiss: () => {
                        payButton.disabled = false;
                        payButton.textContent = 'Pay now';
                    },
                },
                handler: async (paymentResponse) => {
                    payButton.disabled = true;
                    payButton.textContent = 'Confirming payment...';

                    try {
                        await verifyPayment(paymentResponse);
                    } catch (error) {
                        showError(error.message ?? 'Payment verification failed. Please refresh or contact support.');
                        payButton.disabled = false;
                        payButton.textContent = 'Pay now';
                    }
                },
            };

            const razorpay = new Razorpay(options);
            razorpay.on('payment.failed', (response) => {
                showError(response.error?.description ?? 'Payment failed. Please try again.');
                payButton.disabled = false;
                payButton.textContent = 'Pay now';
            });
            razorpay.open();
        } catch (error) {
            showError(error.message ?? 'Something went wrong. Please try again.');
            payButton.disabled = false;
            payButton.textContent = 'Pay now';
        }
    });
});
