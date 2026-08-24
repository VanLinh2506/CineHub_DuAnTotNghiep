(function () {
    'use strict';

    var serverPrices = null;
    var originalFetch = window.fetch;

    function formatMoney(value) {
        return new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + ' ₫';
    }

    function selectedSeats() {
        if (Array.isArray(window.selectedSeats)) {
            return Array.from(new Set(window.selectedSeats));
        }

        return Array.from(document.querySelectorAll('.seat.seat-selected'))
            .map(function (seat) { return seat.getAttribute('data-seat'); })
            .filter(Boolean);
    }

    function foodTotal() {
        var total = 0;
        document.querySelectorAll('input[name^="food_items["]').forEach(function (input) {
            var quantity = parseInt(input.value, 10) || 0;
            var match = input.name.match(/\[(\d+)\]/);
            var card = match
                ? document.querySelector('.food-item-card-compact[data-food-id="' + match[1] + '"]')
                : null;
            var price = card ? (parseInt(card.getAttribute('data-food-price'), 10) || 0) : 0;
            total += quantity * price;
        });
        return total;
    }

    function syncPriceDisplay() {
        if (!serverPrices) return;

        var normal = Number(serverPrices.normal || serverPrices.base || 0);
        var vip = Number(serverPrices.vip || normal * 1.3);
        var couple = Number(serverPrices.couple || normal * 1.5);
        var ticketTotal = 0;

        selectedSeats().forEach(function (seat) {
            var row = String(seat).charAt(0).toUpperCase();
            if (row === 'D' || row === 'E' || row === 'F') {
                ticketTotal += vip;
            } else if (row === 'J' || row === 'K' || row === 'L') {
                ticketTotal += couple;
            } else {
                ticketTotal += normal;
            }
        });

        var values = {
            normalPriceDisplay: normal,
            vipPriceDisplay: vip,
            couplePriceDisplay: couple,
            unitPrice: normal,
            seatsTotal: ticketTotal,
            totalPrice: ticketTotal + foodTotal()
        };

        Object.keys(values).forEach(function (id) {
            var element = document.getElementById(id);
            if (element) element.textContent = formatMoney(values[id]);
        });
    }

    window.fetch = function () {
        var requestUrl = typeof arguments[0] === 'string'
            ? arguments[0]
            : (arguments[0] && arguments[0].url) || '';
        var result = originalFetch.apply(this, arguments);

        if (requestUrl.indexOf('/api/booking/seat-map') === -1) {
            return result;
        }

        return result.then(function (response) {
            response.clone().json().then(function (data) {
                if (!data || !data.prices) return;

                serverPrices = data.prices;
                if (window.bookingPageConfig) {
                    window.bookingPageConfig.basePrice = Number(data.prices.base || 0);
                    window.bookingPageConfig.screenSurcharge = Number(data.prices.normal || 0) - Number(data.prices.base || 0);
                }

                setTimeout(syncPriceDisplay, 0);
                setTimeout(syncPriceDisplay, 100);
            }).catch(function () {});

            return response;
        });
    };

    document.addEventListener('click', function (event) {
        if (event.target.closest('.seat, .btn-quantity-compact, #confirmSeatsBtn, #reselectSeatsBtn')) {
            setTimeout(syncPriceDisplay, 0);
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.matches('input[name^="food_items["]')) {
            setTimeout(syncPriceDisplay, 0);
        }
    });
})();
