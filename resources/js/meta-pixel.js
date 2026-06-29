/**
 * Meta Pixel browser helpers for Oakter ecommerce events.
 * Requires the base pixel snippet in website.partials.meta-pixel.
 */

function readCookie(name) {
    const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.$?*|{}()[\]\\/+^]/g, '\\$&') + '=([^;]*)'));

    return match ? decodeURIComponent(match[1]) : null;
}

function trackMetaEvent(eventName, data = {}, eventId = null) {
    if (typeof window.fbq !== 'function') {
        return;
    }

    if (eventId) {
        window.fbq('track', eventName, data, { eventID: eventId });

        return;
    }

    window.fbq('track', eventName, data);
}

export function trackViewContent(payload) {
    trackMetaEvent('ViewContent', payload);
}

export function trackInitiateCheckout(payload) {
    trackMetaEvent('InitiateCheckout', payload);
}

/** Purchase must use the same event_id as server CAPI for deduplication. */
export function trackPurchase(payload, eventId) {
    trackMetaEvent('Purchase', payload, eventId);
}

export function getFbp() {
    return readCookie('_fbp');
}

export function getFbc() {
    return readCookie('_fbc');
}

window.oakterMeta = {
    trackViewContent,
    trackInitiateCheckout,
    trackPurchase,
    getFbp,
    getFbc,
};
