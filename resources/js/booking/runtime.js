export const bookingPageConfig = window.bookingPageConfig || {};
export const bookingPageRoutes = bookingPageConfig.routes || {};

export function bookingSeatHeaders() {
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': bookingPageConfig.csrfToken || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (window.Echo && typeof window.Echo.socketId === 'function') {
        const socketId = window.Echo.socketId();
        if (socketId) {
            headers['X-Socket-ID'] = socketId;
        }
    }

    return headers;
}
