/**
 * Google Analytics 4 ecommerce helpers for Oakter.
 * Requires the base gtag snippet in website.partials.google-analytics.
 *
 * Accepts the same Meta-style product payloads used by oakterMeta so Blade
 * can reuse MetaProductPayload without duplicating data.
 */

function trackGaEvent(eventName, params = {}) {
    if (typeof window.gtag !== 'function') {
        return;
    }

    window.gtag('event', eventName, params);
}

/**
 * @param {Record<string, any>} metaPayload MetaProductPayload::pixelData shape
 * @returns {Record<string, any>}
 */
function toGaEcommerceParams(metaPayload) {
    const itemId = metaPayload?.content_ids?.[0]
        ?? metaPayload?.contents?.[0]?.id
        ?? 'product';
    const itemName = metaPayload?.content_name ?? itemId;
    const value = Number(metaPayload?.value ?? 0);
    const currency = String(metaPayload?.currency ?? 'INR').toUpperCase();
    const quantity = Number(metaPayload?.contents?.[0]?.quantity ?? metaPayload?.num_items ?? 1);

    const params = {
        currency,
        value,
        items: [
            {
                item_id: String(itemId),
                item_name: String(itemName),
                price: value,
                quantity,
            },
        ],
    };

    if (metaPayload?.order_id) {
        params.transaction_id = String(metaPayload.order_id);
    }

    return params;
}

export function trackViewItem(metaPayload) {
    trackGaEvent('view_item', toGaEcommerceParams(metaPayload));
}

export function trackBeginCheckout(metaPayload) {
    trackGaEvent('begin_checkout', toGaEcommerceParams(metaPayload));
}

export function trackPurchase(metaPayload) {
    trackGaEvent('purchase', toGaEcommerceParams(metaPayload));
}

window.oakterGa = {
    trackViewItem,
    trackBeginCheckout,
    trackPurchase,
};
