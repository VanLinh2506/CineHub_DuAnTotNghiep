import './bootstrap';
import { bookingPageConfig, bookingPageRoutes, bookingSeatHeaders } from './booking/runtime';
import {
    calculateDistanceKm,
    formatLocalDate,
    getSeatGroupsFromRow,
    isSeatOccupied,
    uniqueSeatList,
} from './booking/utils';

// ============== DECLARE ALL FUNCTIONS FIRST ==============
// Global variables
var userLat = null;
var userLng = null;
var currentMovieId = bookingPageConfig.currentMovieId || null;
var selectedTheaterId = null;
var selectedDate = null;
var selectedShowtimeId = null;
var currentBookedSeats = [];
var currentReservedSeats = [];
var currentMyReservedSeats = [];
var currentSeatLayout = null;
var currentSeatPrices = null;
var currentSeatMapShowtimeId = null;
var currentSeatRealtimeChannelName = null;
var reservationHeartbeatInterval = null;
var seatStatusRefreshInterval = null;
var reservationExpiresAtMs = null;
var reservationServerOffsetMs = 0;
var reservationTimerMode = null;
var reservationRequestInFlight = false;
window.selectedSeats = window.selectedSeats || [];
window.seatsConfirmed = window.seatsConfirmed || false;

function updateBookingUrlState(showtimeId) {
    if (!showtimeId || !window.history || !window.URL) {
        return;
    }

    var url = new URL(window.location.href);
    if (currentMovieId) {
        url.searchParams.set('movie', currentMovieId);
    }
    if (window.selectedTheaterId) {
        url.searchParams.set('theater', window.selectedTheaterId);
    }
    if (window.selectedDate) {
        url.searchParams.set('date', window.selectedDate);
    }
    url.searchParams.set('showtime_id', showtimeId);
    window.history.replaceState({}, '', url.toString());
}

function startServerReservationCountdown(payload) {
    if (!payload || !payload.reservationExpiresAt || Number(payload.remainingSeconds || 0) <= 0) {
        return;
    }

    startReservationCountdown({
        serverNow: payload.serverNow,
        reservationExpiresAt: payload.reservationExpiresAt,
        remainingSeconds: payload.remainingSeconds,
        localPurchaseCountdown: true
    });
}

function setPostSeatSectionsVisible(visible) {
    var displayValue = visible ? 'block' : 'none';
    var ids = ['foodModalLauncher', 'emailSection', 'priceInfoBox'];

    for (var i = 0; i < ids.length; i++) {
        var element = document.getElementById(ids[i]);
        if (element) {
            element.style.display = displayValue;
        }
    }

    if (!visible && typeof window.closeFoodModal === 'function') {
        window.closeFoodModal();
    }
}

function getUnavailableSeatsForCurrentUser() {
    var myReserved = uniqueSeatList(currentMyReservedSeats || []);

    return uniqueSeatList((currentBookedSeats || []).concat(currentReservedSeats || [])).filter(function(seat) {
        return myReserved.indexOf(seat) === -1;
    });
}

window.requestUserLocation = function() {
        var badge = document.getElementById('userLocationBadge');
        var text = document.getElementById('userLocationText');
        if (badge) badge.style.display = 'inline-flex';
        if (text) text.textContent = 'Đang lấy vị trí...';

        if (!navigator.geolocation) {
            if (text) text.textContent = 'Trình duyệt không hỗ trợ vị trí';
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            userLat = position.coords.latitude;
            userLng = position.coords.longitude;
            if (text) text.textContent = 'Đã ưu tiên rạp gần nhất';
            updateTheaterDistances();

            fetch(bookingPageRoutes.bookingLocation, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': bookingPageConfig.csrfToken || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        latitude: userLat,
                        longitude: userLng
                    })
                }).catch(function(error) {
                console.error('Could not save user location:', error);
            });
        }, function(error) {
            if (text) text.textContent = 'Không lấy được vị trí';
            console.warn('Geolocation error:', error);
        }, {
            enableHighAccuracy: true,
            timeout: 8000,
            maximumAge: 300000
        });
    };

    function updateTheaterDistances() {
        if (userLat === null || userLng === null) return;

        var container = document.getElementById('theatersContainer');
        if (!container) return;

        var cards = Array.from(container.querySelectorAll('.theater-card'));
        cards.forEach(function(card) {
            var lat = parseFloat(card.dataset.lat);
            var lng = parseFloat(card.dataset.lng);
            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                card.dataset.distance = '999999';
                return;
            }

            var distance = calculateDistanceKm(userLat, userLng, lat, lng);
            card.dataset.distance = distance.toString();

            var distanceBox = card.querySelector('.theater-distance');
            var distanceText = card.querySelector('.distance-text');
            if (distanceBox && distanceText) {
                distanceBox.style.display = 'block';
                distanceText.textContent = distance < 1 ?
                    Math.round(distance * 1000) + ' m' :
                    distance.toFixed(1) + ' km';
            }
        });

        cards.sort(function(a, b) {
            return parseFloat(a.dataset.distance || '999999') - parseFloat(b.dataset.distance || '999999');
        }).forEach(function(card) {
            container.appendChild(card);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.theater-card')) {
            requestUserLocation();
        }

        var theaterContainer = document.getElementById('theatersContainer');
        if (theaterContainer) {
            theaterContainer.addEventListener('click', function(event) {
                var theaterCard = event.target.closest('.theater-card');
                if (!theaterCard || !theaterContainer.contains(theaterCard)) {
                    return;
                }

                event.preventDefault();
                event.bookingTheaterHandled = true;
                var theaterId = theaterCard.getAttribute('data-theater-id');
                if (theaterId) {
                    window.selectTheaterDirect(theaterId);
                }
            });
        }
    });

    // Declare selectDate function FIRST (before it's used)
    window.selectDate = function(dateValue) {
        console.log('Date selected:', dateValue);

        // Remove previous selection
        var dateTabs = document.querySelectorAll('.date-tab');
        for (var i = 0; i < dateTabs.length; i++) {
            dateTabs[i].classList.remove('selected');
        }

        // Select clicked date
        var selectedTab = document.querySelector('.date-tab[data-date="' + dateValue + '"]');
        if (selectedTab) {
            selectedTab.classList.add('selected');
            console.log('Date tab selected');
        }

        window.selectedDate = dateValue;
        window.selectedShowtimeId = null;
        currentSeatMapShowtimeId = null;
        currentMyReservedSeats = [];
        window.selectedSeats = [];
        window.seatsConfirmed = false;
        clearSeatReservationTimers();
        setPostSeatSectionsVisible(false);

        var showtimeInput = document.getElementById('showtimeIdInput');
        if (showtimeInput) showtimeInput.value = '';

        // Hide seat map
        var seatMap = document.getElementById('seatMap');
        if (seatMap) seatMap.innerHTML = '<p class="text-center text-muted">Vui lòng chọn khung giờ chiếu</p>';

        // Load showtimes
        console.log('Loading showtimes for date:', dateValue);
        loadShowtimesNow();
    };

    // Declare loadShowtimes function
    function loadShowtimesNow() {
        if (!window.selectedTheaterId || !window.selectedDate || !currentMovieId) {
            console.log('Missing required data:', {
                selectedTheaterId: window.selectedTheaterId,
                selectedDate: window.selectedDate,
                currentMovieId: currentMovieId
            });
            return;
        }

        var showtimesSection = document.getElementById('showtimeSelectionSection');
        var showtimesContainer = document.getElementById('showtimesContainer');

        if (!showtimesSection || !showtimesContainer) {
            console.error('Showtime elements not found!');
            return;
        }

        // Show loading
        showtimesSection.style.display = 'block';
        showtimesContainer.innerHTML = '<p class="text-center text-muted">Đang tải...</p>';

        // Fetch showtimes using Laravel route
        var url = bookingPageRoutes.bookingShowtimes + '?movie_id=' + currentMovieId +
            '&theater_id=' + window.selectedTheaterId +
            '&date=' + window.selectedDate;
        console.log('Fetching showtimes from:', url);

        fetch(url)
            .then(function(response) {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(function(data) {
                console.log('Showtimes data:', data);
                if (data.showtimes && data.showtimes.length > 0) {
                    var html = '';
                    for (var i = 0; i < data.showtimes.length; i++) {
                        var showtime = data.showtimes[i];
                        html += '<div class="showtime-btn" onclick="selectShowtime(' + showtime.id + ')" data-showtime-id="' + showtime.id + '">';
                        html += '<div>' + showtime.show_time + '</div>';
                        html += '<div class="screen-info">' + (showtime.screen_name || 'N/A') + ' - ' + (showtime.screen_type || '2D') + '</div>';
                        html += '</div>';
                    }
                    showtimesContainer.innerHTML = html;
                } else {
                    showtimesContainer.innerHTML = '<p class="text-center text-warning">Không có suất chiếu nào cho ngày này</p>';
                }
            })
            .catch(function(error) {
                console.error('Error loading showtimes:', error);
                showtimesContainer.innerHTML = '<p class="text-center text-danger">Lỗi khi tải lịch chiếu</p>';
            });
    }

    // Declare selectShowtime function
    window.selectShowtime = function(showtimeId) {
        console.log('=== SHOWTIME SELECTION ===');
        console.log('Showtime selected:', showtimeId);

        // Remove previous selection
        var showtimeBtns = document.querySelectorAll('.showtime-btn');
        for (var i = 0; i < showtimeBtns.length; i++) {
            showtimeBtns[i].classList.remove('selected');
        }

        // Select clicked showtime
        var selectedBtn = document.querySelector('.showtime-btn[data-showtime-id="' + showtimeId + '"]');
        if (selectedBtn) {
            selectedBtn.classList.add('selected');
            console.log('Showtime button marked as selected');
        }

        window.selectedShowtimeId = showtimeId;
        console.log('Set window.selectedShowtimeId =', showtimeId);
        updateBookingUrlState(showtimeId);

        var showtimeInput = document.getElementById('showtimeIdInput');
        console.log('Found showtimeIdInput element:', !!showtimeInput);

        if (showtimeInput) {
            showtimeInput.value = showtimeId;
            console.log('Set showtimeIdInput.value =', showtimeInput.value);
        } else {
            console.error('showtimeIdInput element not found!');
        }

        // Load seat map and start the ticket purchase countdown for this screen/showtime.
        loadSeatMapNow(showtimeId, {
            startPurchaseCountdown: true
        });
    };

    // Generate default seat layout
    function generateDefaultSeatLayoutNowLegacy(bookedSeats) {
        bookedSeats = bookedSeats || [];
        currentBookedSeats = bookedSeats;

        // Default 10 rows x 12 seats layout
        var rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        var seatsPerRow = 12;
        var seatMapContainer = document.getElementById('seatMap');

        var html = '';

        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            html += '<div class="seat-row">';
            html += '<div class="seat-row-label">' + row + '</div>';

            // For row J (couple seats), show only 6 couple seats
            if (row === 'J') {
                for (var col = 1; col <= 6; col++) {
                    var seatNumber = row + col;
                    var seatClass = 'seat seat-couple';

                    // Check if booked
                    if (bookedSeats.indexOf(seatNumber) !== -1) {
                        seatClass += ' seat-booked';
                    }

                    // Aisle space after 3rd couple seat
                    if (col === 4) {
                        html += '<div class="seat-space"></div>';
                    }

                    var onclick = (bookedSeats.indexOf(seatNumber) !== -1) ? '' : 'onclick="toggleSeat(\'' + seatNumber + '\')"';

                    html += '<div class="' + seatClass + '" data-seat="' + seatNumber + '" ' + onclick + '>' + col + '</div>';
                }
            } else {
                // Normal rows
                for (var col = 1; col <= seatsPerRow; col++) {
                    var seatNumber = row + col;
                    var seatClass = 'seat';

                    // Check if booked
                    if (bookedSeats.indexOf(seatNumber) !== -1) {
                        seatClass += ' seat-booked';
                    }

                    // VIP rows (middle rows D, E, F)
                    if (row === 'D' || row === 'E' || row === 'F') {
                        seatClass += ' seat-vip';
                    }

                    // Aisle spaces (middle gap after seat 6)
                    if (col === 7) {
                        html += '<div class="seat-space"></div>';
                    }

                    var onclick = (bookedSeats.indexOf(seatNumber) !== -1) ? '' : 'onclick="toggleSeat(\'' + seatNumber + '\')"';

                    html += '<div class="' + seatClass + '" data-seat="' + seatNumber + '" ' + onclick + '>' + col + '</div>';
                }
            }

            html += '</div>';
        }

        seatMapContainer.innerHTML = html;
    }

    // Render seat map from custom layout
    function renderSeatMapFullLegacy(layout, bookedSeats, prices) {
        bookedSeats = bookedSeats || [];
        currentBookedSeats = bookedSeats;
        var seatMapContainer = document.getElementById('seatMap');
        var html = '';

        if (!layout || !Array.isArray(layout)) {
            generateDefaultSeatLayoutNow(bookedSeats);
            return;
        }

        for (var i = 0; i < layout.length; i++) {
            var row = layout[i];
            html += '<div class="seat-row">';
            html += '<div class="seat-row-label">' + row.row + '</div>';

            for (var j = 0; j < row.seats.length; j++) {
                var seat = row.seats[j];
                var seatClass = 'seat';

                if (seat.type === 'space' || seat.type === 'aisle') {
                    html += '<div class="seat-space"></div>';
                    continue;
                }

                if (seat.type === 'vip') seatClass += ' seat-vip';
                if (seat.type === 'couple') seatClass += ' seat-couple';
                if (seat.type === 'disabled' || !seat.available) seatClass += ' seat-disabled';
                if (bookedSeats.indexOf(seat.number) !== -1) seatClass += ' seat-booked';

                var onclick = (seat.type !== 'disabled' && seat.available && bookedSeats.indexOf(seat.number) === -1) ?
                    'onclick="toggleSeat(\'' + seat.number + '\')"' :
                    '';

                html += '<div class="' + seatClass + '" data-seat="' + seat.number + '" ' + onclick + '>';
                html += (seat.label || seat.number || '');
                html += '</div>';

                if (seat.number) {
                    var colNum = parseInt(String(seat.number).substring(1), 10);
                    var nextSeat = row.seats[j + 1];
                    var nextCol = nextSeat && nextSeat.number ? parseInt(String(nextSeat.number).substring(1), 10) : null;
                    if (nextCol !== null && nextCol - colNum > 1) {
                        html += '<div class="seat-space"></div>';
                    } else if (colNum === 6 && nextCol === 7) {
                        html += '<div class="seat-space"></div>';
                    } else if (row.row === 'J' && colNum === 3 && nextCol === 4) {
                        html += '<div class="seat-space"></div>';
                    }
                }
            }

            html += '</div>';
        }

        seatMapContainer.innerHTML = html;
    }

    // Show error message
    function showErrorMsg(message) {
        alert(message); // Simple alert for now
        console.error(message);
    }

    // Show success message
    function showSuccessMsg(message) {
        alert(message); // Simple alert for now
        console.log(message);
    }

    function splitGroupColsByBooked(groupCols, rowName, booked) {
        var segments = [];
        var current = [];

        for (var i = 0; i < groupCols.length; i++) {
            var col = groupCols[i];
            var seatNo = rowName + col;
            if (booked.indexOf(seatNo) !== -1) {
                if (current.length > 0) {
                    segments.push(current.slice());
                    current = [];
                }
                continue;
            }
            current.push(col);
        }

        if (current.length > 0) {
            segments.push(current);
        }

        return segments.length ? segments : [groupCols.slice()];
    }

    function countFreeSeatsFromEdge(segmentCols, rowName, selected, booked, fromLeft) {
        var count = 0;
        var cols = fromLeft ? segmentCols.slice() : segmentCols.slice().reverse();

        for (var i = 0; i < cols.length; i++) {
            var seatNo = rowName + cols[i];
            if (isSeatOccupied(seatNo, selected, booked)) {
                break;
            }
            count++;
        }

        return count;
    }

    function isSelectionAnchoredToBooked(selectedCols, rowName, booked, side) {
        if (!selectedCols.length) {
            return false;
        }

        var minSel = Math.min.apply(null, selectedCols);
        var maxSel = Math.max.apply(null, selectedCols);

        if (side === 'left') {
            return booked.indexOf(rowName + (minSel - 1)) !== -1;
        }

        return booked.indexOf(rowName + (maxSel + 1)) !== -1;
    }

    function isSelectionTouchingSegmentEdge(selectedCols, segmentCols, side) {
        if (!selectedCols.length || !segmentCols.length) {
            return false;
        }

        var minSel = Math.min.apply(null, selectedCols);
        var maxSel = Math.max.apply(null, selectedCols);
        var minSeg = Math.min.apply(null, segmentCols);
        var maxSeg = Math.max.apply(null, segmentCols);

        return side === 'left' ? minSel === minSeg : maxSel === maxSeg;
    }

    function validateGroupOrphanSeats(groupSeats, selected, booked, rowName) {
        if (groupSeats.length < 2) {
            return {
                valid: true,
                message: ''
            };
        }

        var groupCols = groupSeats.map(function(seatEl) {
            return parseInt(seatEl.dataset.seat.substring(1), 10);
        }).sort(function(a, b) {
            return a - b;
        });

        var selectedCols = groupSeats
            .map(function(seatEl) {
                return seatEl.dataset.seat;
            })
            .filter(function(seatNo) {
                return selected.indexOf(seatNo) !== -1;
            })
            .map(function(seatNo) {
                return parseInt(seatNo.substring(1), 10);
            })
            .sort(function(a, b) {
                return a - b;
            });

        if (selectedCols.length === 0) {
            return {
                valid: true,
                message: ''
            };
        }

        var segments = splitGroupColsByBooked(groupCols, rowName, booked);

        for (var s = 0; s < segments.length; s++) {
            var segmentCols = segments[s];
            var selectedInSegment = selectedCols.filter(function(col) {
                return segmentCols.indexOf(col) !== -1;
            });

            if (selectedInSegment.length === 0) {
                continue;
            }

            if (selectedInSegment.length > 2) {
                var leftFree = countFreeSeatsFromEdge(segmentCols, rowName, selected, booked, true);
                var rightFree = countFreeSeatsFromEdge(segmentCols, rowName, selected, booked, false);
                var anchoredLeft = isSelectionAnchoredToBooked(selectedInSegment, rowName, booked, 'left');
                var anchoredRight = isSelectionAnchoredToBooked(selectedInSegment, rowName, booked, 'right');
                var touchesLeft = isSelectionTouchingSegmentEdge(selectedInSegment, segmentCols, 'left') || anchoredLeft;
                var touchesRight = isSelectionTouchingSegmentEdge(selectedInSegment, segmentCols, 'right') || anchoredRight;

                if (!touchesRight && leftFree === 1) {
                    return {
                        valid: false,
                        message: 'Khi đặt hơn 2 ghế ở hàng ' + rowName + ', phải để trống 0 hoặc ít nhất 2 ghế ở đầu cụm. Hiện đang để lẻ 1 ghế.'
                    };
                }

                if (!touchesLeft && rightFree === 1) {
                    return {
                        valid: false,
                        message: 'Khi đặt hơn 2 ghế ở hàng ' + rowName + ', phải để trống 0 hoặc ít nhất 2 ghế ở cuối cụm. Hiện đang để lẻ 1 ghế.'
                    };
                }
            }

            var existingSingles = findSingleFreeColsInGroup(segmentCols, rowName, [], booked);
            var newSingles = findSingleFreeColsInGroup(segmentCols, rowName, selected, booked);

            for (var i = 0; i < newSingles.length; i++) {
                var col = newSingles[i];
                if (existingSingles.indexOf(col) !== -1) {
                    continue;
                }

                if (isInvalidSingleOrphanCol(col, segmentCols, rowName, selectedInSegment, selected, booked)) {
                    return {
                        valid: false,
                        message: 'Không được để lẻ 1 ghế trống ở hàng ' + rowName + ' (ghế ' + rowName + col + '). Vui lòng chọn thêm ghế đó, chọn từ phía ghế đã đặt, hoặc chừa tối thiểu 2 ghế trống liền nhau.'
                    };
                }
            }
        }

        return {
            valid: true,
            message: ''
        };
    }

    function findSingleFreeColsInGroup(groupCols, rowName, selected, booked) {
        var singles = [];
        var freeRun = [];

        for (var i = 0; i < groupCols.length; i++) {
            var col = groupCols[i];
            var seatNo = rowName + col;
            var occupied = isSeatOccupied(seatNo, selected, booked);

            if (!occupied) {
                freeRun.push(col);
                continue;
            }

            if (freeRun.length === 1) {
                singles.push(freeRun[0]);
            }
            freeRun = [];
        }

        if (freeRun.length === 1) {
            singles.push(freeRun[0]);
        }

        return singles;
    }

    function isInvalidSingleOrphanCol(col, groupCols, rowName, selectedCols, selected, booked) {
        var idx = groupCols.indexOf(col);
        if (idx === -1) {
            return false;
        }

        var leftCol = idx > 0 ? groupCols[idx - 1] : null;
        var rightCol = idx < groupCols.length - 1 ? groupCols[idx + 1] : null;
        var leftOcc = leftCol !== null && isSeatOccupied(rowName + leftCol, selected, booked);
        var rightOcc = rightCol !== null && isSeatOccupied(rowName + rightCol, selected, booked);

        if (leftOcc && rightOcc) {
            return true;
        }

        var minSel = Math.min.apply(null, selectedCols);
        var maxSel = Math.max.apply(null, selectedCols);

        if (!leftOcc && rightOcc && col < minSel) {
            if (maxSel === Math.max.apply(null, groupCols)) {
                return false;
            }
            if (booked.indexOf(rowName + (maxSel + 1)) !== -1) {
                return false;
            }
            if (selectedCols.length >= 2) {
                return true;
            }
        }

        if (leftOcc && !rightOcc && col > maxSel) {
            if (minSel === Math.min.apply(null, groupCols)) {
                return false;
            }
            if (booked.indexOf(rowName + (minSel - 1)) !== -1) {
                return false;
            }
            if (selectedCols.length >= 2) {
                var leftSeat = rowName + leftCol;
                if (booked.indexOf(leftSeat) !== -1) {
                    return false;
                }
                if (selected.indexOf(leftSeat) !== -1) {
                    return true;
                }
            }
        }

        return false;
    }

    // Email input handler
    document.addEventListener('change', function(event) {
        if (event.target && event.target.name === 'payment_method') {
            var bookBtn = document.getElementById('bookBtn');
            if (bookBtn) {
                bookBtn.innerHTML = event.target.value === 'wallet' ?
                    '<i class="fas fa-wallet"></i> Thanh toán bằng ví CineHub' :
                    '<i class="fas fa-credit-card"></i> Tiếp tục thanh toán VNPay';
            }
        }
    });

    // Update booking summary (full)
    function updateBookingSummaryFull() {
        console.log('updateBookingSummaryFull called, seatsConfirmed:', window.seatsConfirmed);

        if (!window.seatsConfirmed) {
            calculateSeatPriceNow();
            return;
        }

        calculateSeatPriceNow();

        var basePrice = Number(bookingPageConfig.basePrice || 90000);
        var normalPrice = basePrice;
        var vipPrice = Math.round(basePrice * 1.3);
        var couplePrice = Math.round(basePrice * 1.5);

        var seatsTotal = 0;

        if (window.selectedSeats) {
            for (var i = 0; i < window.selectedSeats.length; i++) {
                var seat = window.selectedSeats[i];
                var row = seat.charAt(0);
                if (row === 'D' || row === 'E' || row === 'F') {
                    seatsTotal += vipPrice;
                } else if (row === 'J') {
                    seatsTotal += couplePrice;
                } else {
                    seatsTotal += normalPrice;
                }
            }
        }

        console.log('Seat total:', seatsTotal);

        // Update seats total display
        var seatsTotalEl = document.getElementById('seatsTotal');
        if (seatsTotalEl) seatsTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(seatsTotal) + ' ₫';

        // Add food items to total (from inline inputs)
        var foodInputs = document.querySelectorAll('input[name^="food_items["]');
        var foodTotal = 0;
        var foodSummaryHtml = '';
        var hasFoodItems = false;

        console.log('Found food inputs:', foodInputs.length);

        for (var i = 0; i < foodInputs.length; i++) {
            var qty = parseInt(foodInputs[i].value) || 0;
            if (qty > 0) {
                hasFoodItems = true;
                // Get price from data attribute of corresponding food card
                var foodId = foodInputs[i].name.match(/\[(\d+)\]/)[1];
                var foodCard = document.querySelector('.food-item-card-compact[data-food-id="' + foodId + '"]');
                if (foodCard) {
                    var price = parseInt(foodCard.getAttribute('data-food-price')) || 0;
                    var subtotal = price * qty;
                    foodTotal += subtotal;

                    // Get food name
                    var foodName = foodCard.querySelector('h6') ? foodCard.querySelector('h6').textContent.trim() : 'Món ăn #' + foodId;

                    foodSummaryHtml += '<div class="price-row" style="font-size: 13px; color: #ddd;">';
                    foodSummaryHtml += '<span>' + foodName + ' x' + qty + '</span>';
                    foodSummaryHtml += '<span>' + new Intl.NumberFormat('vi-VN').format(subtotal) + ' ₫</span>';
                    foodSummaryHtml += '</div>';

                    console.log('Food item:', {
                        foodId,
                        foodName,
                        qty,
                        price,
                        subtotal
                    });
                }
            }
        }

        // Update food summary section
        var foodSummaryRows = document.getElementById('foodSummaryRows');
        if (foodSummaryRows) {
            if (hasFoodItems) {
                foodSummaryRows.innerHTML = '<div class="price-row" style="font-weight: 600; margin-bottom: 5px;"><span>Đồ ăn & nước:</span><span></span></div>' + foodSummaryHtml;
                foodSummaryRows.style.display = 'block';
            } else {
                foodSummaryRows.style.display = 'none';
            }
        }

        console.log('Food total:', foodTotal);
        var totalPrice = seatsTotal + foodTotal;
        console.log('Grand total:', totalPrice);

        var totalEl = document.getElementById('totalPrice');
        if (totalEl) totalEl.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' ₫';

        updateFoodModalSummary();
        updatePayButtonState();
    }

    function updatePayButtonState() {
        var bookBtn = document.getElementById('bookBtn');
        if (!bookBtn) return;

        var terms = document.querySelector('input[name="accept_terms"]');
        var emailInput = document.getElementById('customerEmail');
        var emailOk = emailInput && emailInput.value.trim().length > 0;
        var termsOk = terms && terms.checked;
        var seatsOk = window.seatsConfirmed === true;
        var reservationReady = reservationRequestInFlight !== true;

        bookBtn.disabled = !(seatsOk && termsOk && emailOk && reservationReady);
    }

    // Update booking summary
    function updateBookingSummaryNow() {
        var selectedSeats = document.querySelectorAll('.seat-selected');
        var seatNumbers = [];

        for (var i = 0; i < selectedSeats.length; i++) {
            var seatNum = selectedSeats[i].getAttribute('data-seat');
            if (seatNum) seatNumbers.push(seatNum);
        }

        console.log('Selected seats:', seatNumbers);

        // Update hidden input
        var seatsInput = document.getElementById('selectedSeatsInput');
        if (seatsInput) {
            seatsInput.value = seatNumbers.join(',');
        }

        // Show/hide summary sections
        var selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');
        var emailSection = document.getElementById('emailSection');

        if (seatNumbers.length > 0) {
            if (selectedSeatsDisplay) selectedSeatsDisplay.style.display = 'block';
            if (emailSection) emailSection.style.display = 'block';

            // Update display text
            var seatsText = document.getElementById('seatsText');
            if (seatsText) {
                seatsText.textContent = seatNumbers.join(', ');
            }
        } else {
            if (selectedSeatsDisplay) selectedSeatsDisplay.style.display = 'none';
            if (emailSection) emailSection.style.display = 'none';
        }
    }

    // Declare renderSeatMap function (will be overridden by full implementation later)
    function renderSeatMap(layout, bookedSeats, prices) {
        console.log('Rendering seat map...', {
            layout,
            bookedSeats,
            prices
        });
        renderSeatMapFull(layout, bookedSeats, prices);
    }

    // Early declaration - available immediately
    window.selectTheaterDirect = function(theaterId) {
        console.log('Theater clicked! ID:', theaterId);

        // Will implement full logic after DOM loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                doSelectTheater(theaterId);
            });
        } else {
            doSelectTheater(theaterId);
        }
    };

    function doSelectTheater(theaterId) {
        console.log('Executing doSelectTheater for ID:', theaterId);

        // Remove previous selection
        var cards = document.querySelectorAll('.theater-card');
        for (var i = 0; i < cards.length; i++) {
            cards[i].classList.remove('selected');
        }

        // Select clicked theater
        var selectedCard = document.querySelector('.theater-card[data-theater-id="' + theaterId + '"]');
        if (selectedCard) {
            selectedCard.classList.add('selected');
            console.log('Theater card selected');
        }

        // Set values
        var theaterInput = document.getElementById('theaterIdInput');
        if (theaterInput) {
            theaterInput.value = theaterId;
            console.log('Set theaterIdInput');
        }

        window.selectedTheaterId = theaterId;
        window.selectedDate = null;
        window.selectedShowtimeId = null;
        currentSeatMapShowtimeId = null;
        currentMyReservedSeats = [];
        window.selectedSeats = [];
        window.seatsConfirmed = false;
        clearSeatReservationTimers();
        setPostSeatSectionsVisible(false);

        // Reset other inputs
        var showtimeInput = document.getElementById('showtimeIdInput');
        if (showtimeInput) showtimeInput.value = '';

        // Hide sections
        var seatMap = document.getElementById('seatMap');
        if (seatMap) seatMap.innerHTML = '<p class="text-center text-muted">Vui lòng chọn lịch chiếu</p>';

        var seatSection = document.getElementById('seatSelectionSection');
        if (seatSection) seatSection.style.display = 'none';

        var showtimeSection = document.getElementById('showtimeSelectionSection');
        if (showtimeSection) showtimeSection.style.display = 'none';

        // Show date selection immediately
        var dateSection = document.getElementById('dateSelectionSection');
        if (dateSection) {
            dateSection.style.display = 'block';
            console.log('Date section shown');
        } else {
            console.error('dateSelectionSection not found!');
        }

        // Load dates using direct implementation (don't rely on loadDates function)
        console.log('Loading dates directly...');
        var datesContainer = document.getElementById('datesContainer');
        if (datesContainer) {
            // Generate 7 days from today
            var html = '';
            var dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

            for (var i = 0; i < 7; i++) {
                var date = new Date();
                date.setDate(date.getDate() + i);

                var dateStr = formatLocalDate(date);
                var dayOfWeek = date.getDay();
                var day = String(date.getDate()).padStart(2, '0');
                var month = String(date.getMonth() + 1).padStart(2, '0');

                html += '<div class="date-tab" onclick="selectDate(\'' + dateStr + '\')" data-date="' + dateStr + '">';
                html += '<div class="day-name">' + dayNames[dayOfWeek] + (i === 0 ? ' (Hôm nay)' : '') + '</div>';
                html += '<div class="date-text">' + day + '/' + month + '</div>';
                html += '</div>';
            }

            datesContainer.innerHTML = html;
            console.log('Dates loaded!');
        } else {
            console.error('datesContainer not found!');
        }
    }

    // Validate form before submit
    function validateFormBeforeSubmit() {
        console.log('=== FORM VALIDATION ===');

        // Force get fresh value
        var showtimeInput = document.getElementById('showtimeIdInput');
        var showtimeId = showtimeInput ? showtimeInput.value : null;

        var seats = document.querySelectorAll('input[name="seats[]"]');
        var email = document.getElementById('customerEmail') ? document.getElementById('customerEmail').value : '';

        console.log('Showtime Input Element:', showtimeInput);
        console.log('Showtime ID from input:', showtimeId);
        console.log('Showtime ID from window:', window.selectedShowtimeId);
        console.log('Seats count:', seats.length);
        console.log('Seats:', Array.from(seats).map(s => s.value));
        console.log('Email:', email);
        console.log('Seats confirmed:', window.seatsConfirmed);

        // If showtime_id is empty but window.selectedShowtimeId has value, set it
        if ((!showtimeId || showtimeId === '') && window.selectedShowtimeId) {
            console.warn('showtime_id input was empty, setting from window.selectedShowtimeId');
            if (showtimeInput) {
                showtimeInput.value = window.selectedShowtimeId;
                showtimeId = window.selectedShowtimeId;
                console.log('Set showtime_id to:', showtimeId);
            }
        }

        if (!showtimeId || showtimeId === '') {
            alert('Vui lòng chọn suất chiếu!');
            console.error('Validation failed: No showtime selected');
            return false;
        }

        if (seats.length === 0) {
            alert('Vui lòng chọn ghế!');
            console.error('Validation failed: No seats selected');
            return false;
        }

        if (!window.seatsConfirmed) {
            alert('Vui lòng xác nhận chọn ghế!');
            console.error('Validation failed: Seats not confirmed');
            return false;
        }

        var paymentSection = document.getElementById('paymentSection');
        var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentSection || paymentSection.getAttribute('data-payment-confirmed') !== 'true') {
            if (paymentSection) {
                paymentSection.style.display = 'flex';
                paymentSection.classList.add('payment-options-open');
            }
            return false;
        }

        if (!selectedPayment) {
            if (paymentSection) {
                paymentSection.style.display = 'flex';
                paymentSection.classList.add('payment-options-open');
            }
            alert('Vui lòng chọn phương thức thanh toán.');
            return false;
        }

        if (!email || !email.trim()) {
            alert('Vui lòng nhập email nhận vé!');
            return false;
        }

        var terms = document.querySelector('input[name="accept_terms"]');
        if (!terms || !terms.checked) {
            alert('Vui lòng đồng ý với điều khoản và chính sách!');
            return false;
        }

        var bookBtn = document.getElementById('bookBtn');
        if (bookBtn) {
            bookBtn.disabled = true;
            bookBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý thanh toán...';
        }

        console.log('✅ Form validation passed! Submitting...');
        console.log('Final form data:');
        console.log('- showtime_id:', showtimeId);
        console.log('- seats:', Array.from(seats).map(s => s.value));
        console.log('- email:', email);

        return true;
    }

    window.validateFormBeforeSubmit = validateFormBeforeSubmit;

    document.addEventListener('DOMContentLoaded', function() {
        var terms = document.querySelector('input[name="accept_terms"]');
        var emailInput = document.getElementById('customerEmail');

        if (terms) {
            terms.addEventListener('change', updatePayButtonState);
        }
        if (emailInput) {
            emailInput.addEventListener('input', updatePayButtonState);
        }

        updatePayButtonState();

        if (bookingPageConfig.flashError) {
            alert(bookingPageConfig.flashError);
        }
        if (bookingPageConfig.validationError) {
            alert(bookingPageConfig.validationError);
        }
    });

    function clearSeatReservationTimers() {
        if (reservationHeartbeatInterval) {
            clearInterval(reservationHeartbeatInterval);
            reservationHeartbeatInterval = null;
        }

        reservationExpiresAtMs = null;
        reservationTimerMode = null;
        hideReservationTimer();
    }

    function hideReservationTimer() {
        var timerBox = document.getElementById('reservationTimerBox');
        if (timerBox) timerBox.style.display = 'none';
    }

    function setReservationTimerLabel(text) {
        var timerBox = document.getElementById('reservationTimerBox');
        var label = timerBox ? timerBox.querySelector('span') : null;
        if (label && text) {
            label.textContent = text;
        }
    }

    function formatRemainingTime(seconds) {
        seconds = Math.max(0, Number(seconds || 0));
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        return String(minutes).padStart(2, '0') + ':' + String(remainingSeconds).padStart(2, '0');
    }

    function showReservationTimerNotice(message) {
        var notice = document.getElementById('seatRealtimeNotice');
        var seatSelectionSection = document.getElementById('seatSelectionSection');

        if (!notice && seatSelectionSection) {
            notice = document.createElement('div');
            notice.id = 'seatRealtimeNotice';
            notice.className = 'alert alert-warning';
            notice.style.marginTop = '12px';
            notice.style.marginBottom = '0';
            notice.style.maxWidth = '600px';
            notice.style.marginLeft = 'auto';
            notice.style.marginRight = 'auto';

            var seatMap = document.getElementById('seatMap');
            if (seatMap && seatMap.parentNode) {
                seatMap.parentNode.insertBefore(notice, seatMap.nextSibling);
            } else {
                seatSelectionSection.appendChild(notice);
            }
        }

        if (notice) {
            notice.className = 'alert alert-warning';
            notice.textContent = message;
            notice.style.display = 'block';
        }
    }

    function resetExpiredReservationUi(showtimeId, message) {
        window.selectedSeats = [];
        window.seatsConfirmed = false;
        currentMyReservedSeats = [];

        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'inline-block';
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Xác nhận chọn ghế';
        }

        if (typeof window.resetSeatActionButtonUi === 'function') {
            window.resetSeatActionButtonUi(true);
        }

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) reselectBtn.style.display = 'none';

        var foodSection = document.getElementById('foodSection');
        if (foodSection) foodSection.style.display = 'none';
        var foodLauncher = document.getElementById('foodModalLauncher');
        if (foodLauncher) foodLauncher.style.display = 'none';

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) paymentSection.style.display = 'none';

        var emailSection = document.getElementById('emailSection');
        if (emailSection) emailSection.style.display = 'none';

        var priceInfoBox = document.getElementById('priceInfoBox');
        if (priceInfoBox) priceInfoBox.style.display = 'none';

        updateSeatSummaryNow();
        showReservationTimerNotice('Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');

        if (message) {
            showReservationTimerNotice(message);
        } else {
            showReservationTimerNotice('Thoi gian giu ghe da het. Vui long chon lai ghe.');
        }

        if (showtimeId) {
            loadSeatMapNow(showtimeId, {
                startPurchaseCountdown: false
            });
        }
    }

    function renderReservationTimer() {
        var timerBox = document.getElementById('reservationTimerBox');
        var timerText = document.getElementById('reservationTimerText');
        if (!timerBox || !timerText || !reservationExpiresAtMs) {
            hideReservationTimer();
            return;
        }

        var nowMs = Date.now() + reservationServerOffsetMs;
        var remainingSeconds = Math.max(0, Math.ceil((reservationExpiresAtMs - nowMs) / 1000));
        timerText.textContent = formatRemainingTime(remainingSeconds);
        timerBox.style.display = 'block';

        if (remainingSeconds <= 0) {
            var expiredMode = reservationTimerMode;
            var expiredMessage = expiredMode === 'purchase'
                ? 'Thoi gian mua ve da het. Vui long chon lai gio chieu/phong.'
                : null;

            resetExpiredReservationUi(currentSeatMapShowtimeId || window.selectedShowtimeId, expiredMessage);
        }
    }

    function startReservationCountdown(payload) {
        payload = payload || {};
        var isPurchaseCountdown = payload.localPurchaseCountdown === true;

        if (!isPurchaseCountdown) {
            return;
        }

        if (reservationHeartbeatInterval) {
            clearInterval(reservationHeartbeatInterval);
            reservationHeartbeatInterval = null;
        }

        if (!payload.reservationExpiresAt || Number(payload.remainingSeconds || 0) <= 0) {
            reservationExpiresAtMs = null;
            reservationTimerMode = null;
            hideReservationTimer();
            return;
        }

        reservationTimerMode = 'purchase';
        setReservationTimerLabel('Thoi gian mua ve con lai:');

        var serverNowMs = payload.serverNow ? Date.parse(payload.serverNow) : NaN;
        reservationServerOffsetMs = Number.isNaN(serverNowMs) ? 0 : serverNowMs - Date.now();
        reservationExpiresAtMs = Date.parse(payload.reservationExpiresAt);

        if (Number.isNaN(reservationExpiresAtMs)) {
            reservationExpiresAtMs = Date.now() + reservationServerOffsetMs + (Number(payload.remainingSeconds || 0) * 1000);
        }

        renderReservationTimer();
        reservationHeartbeatInterval = setInterval(renderReservationTimer, 1000);
    }

    function getTicketPurchaseCountdownSeconds() {
        var configuredSeconds = Number(bookingPageConfig.ticketPurchaseCountdownSeconds || 600);
        return configuredSeconds > 0 ? configuredSeconds : 600;
    }

    function startTicketPurchaseCountdown(showtimeId) {
        if (!showtimeId) {
            return;
        }

        var seconds = getTicketPurchaseCountdownSeconds();
        startReservationCountdown({
            serverNow: new Date().toISOString(),
            reservationExpiresAt: new Date(Date.now() + seconds * 1000).toISOString(),
            remainingSeconds: seconds,
            localPurchaseCountdown: true
        });
    }

    function syncSeatState(bookedSeats, reservedSeats) {
        currentBookedSeats = uniqueSeatList(bookedSeats || []);
        currentReservedSeats = uniqueSeatList(reservedSeats || []);
        currentMyReservedSeats = uniqueSeatList(currentMyReservedSeats || []).filter(function(seat) {
            return currentReservedSeats.indexOf(seat) !== -1;
        });
    }

    function applySeatRealtimeUpdate(data) {
        if (!data) {
            return;
        }

        var showtimeId = data.showtimeId || currentSeatMapShowtimeId || window.selectedShowtimeId;
        if (!showtimeId) {
            return;
        }

        currentSeatMapShowtimeId = showtimeId;
        currentSeatPrices = data.prices || currentSeatPrices;
        syncSeatState(data.bookedSeats || [], data.reservedSeats || []);

        if (window.seatsConfirmed) {
            var stillReserved = uniqueSeatList(currentMyReservedSeats || []).filter(function(seat) {
                return currentReservedSeats.indexOf(seat) !== -1;
            });

            if (stillReserved.length !== uniqueSeatList(currentMyReservedSeats || []).length) {
                window.seatsConfirmed = false;
                currentMyReservedSeats = [];
                window.selectedSeats = [];

                if (reservationHeartbeatInterval) {
                    clearInterval(reservationHeartbeatInterval);
                    reservationHeartbeatInterval = null;
                }

                var confirmBtn = document.getElementById('confirmSeatsBtn');
                if (confirmBtn) {
                    confirmBtn.style.display = 'inline-block';
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Xác nhận chọn ghế';
                }

                var reselectBtn = document.getElementById('reselectSeatsBtn');
                if (reselectBtn) {
                    reselectBtn.style.display = 'none';
                }

                var foodSection = document.getElementById('foodSection');
                if (foodSection) foodSection.style.display = 'none';
                var foodLauncher = document.getElementById('foodModalLauncher');
                if (foodLauncher) foodLauncher.style.display = 'none';

                var paymentSection = document.getElementById('paymentSection');
                if (paymentSection) paymentSection.style.display = 'none';

                showErrorMsg('Ghế đã giữ chỗ của bạn vừa hết hạn hoặc đã thay đổi. Vui lòng chọn lại ghế.');
            }
        } else {
            var unavailableSeats = [];
            window.selectedSeats = uniqueSeatList(window.selectedSeats || []).filter(function(seat) {
                var locked = getUnavailableSeatsForCurrentUser().indexOf(seat) !== -1;
                if (locked) {
                    unavailableSeats.push(seat);
                    return false;
                }
                return true;
            });

            if (unavailableSeats.length > 0) {
                showErrorMsg('Có ghế vừa được người khác giữ chỗ: ' + unavailableSeats.join(', '));
            }
        }

        if (currentSeatLayout) {
            renderSeatMapFull(currentSeatLayout, data.bookedSeats || [], data.reservedSeats || [], data.prices || currentSeatPrices || {}, true);
        } else {
            generateDefaultSeatLayoutNow(data.bookedSeats || [], data.reservedSeats || [], true);
        }

        updateSeatSummaryNow();
    }

    function renderSeatMapWithState(layout, bookedSeats, reservedSeats, prices, preserveSelection) {
        currentSeatLayout = layout || null;
        currentSeatPrices = prices || null;
        syncSeatState(bookedSeats, reservedSeats);

        if (!preserveSelection) {
            window.selectedSeats = [];
            window.seatsConfirmed = false;
        }

        if (!layout || !Array.isArray(layout)) {
            generateDefaultSeatLayoutNow(bookedSeats, reservedSeats, true);
            return;
        }

        renderSeatMapFull(layout, bookedSeats, reservedSeats, prices, true);
    }

    function startSeatRealtimeSubscription(showtimeId) {
        if (currentSeatRealtimeChannelName && window.Echo && typeof window.Echo.leave === 'function') {
            window.Echo.leave(currentSeatRealtimeChannelName);
        }
        currentSeatRealtimeChannelName = null;

        if (seatStatusRefreshInterval) {
            clearInterval(seatStatusRefreshInterval);
            seatStatusRefreshInterval = null;
        }

        if (!showtimeId) {
            return;
        }

        seatStatusRefreshInterval = setInterval(function() {
            refreshSeatStatusNow(showtimeId);
        }, 10000);

        if (!window.Echo || typeof window.Echo.channel !== 'function') {
            return;
        }

        currentSeatRealtimeChannelName = 'booking.showtime.' + showtimeId;

        var channel = window.Echo.private(currentSeatRealtimeChannelName);
        var handleRealtimeEvent = function(event) {
            if (!event || String(event.showtimeId) !== String(showtimeId)) {
                return;
            }

            applySeatRealtimeUpdate(event);

        };

        channel
            .listen('.seat:selected', handleRealtimeEvent)
            .listen('.seat:released', handleRealtimeEvent)
            .listen('.seat:paid', handleRealtimeEvent)
            .listen('.seat:expired', function(event) {
                if (!event || String(event.showtimeId) !== String(showtimeId)) {
                    return;
                }

                var expiredSeats = uniqueSeatList(event.seats || []);
                var affectedMine = uniqueSeatList(currentMyReservedSeats || []).filter(function(seat) {
                    return expiredSeats.indexOf(seat) !== -1;
                });

                applySeatRealtimeUpdate(event);

                if (affectedMine.length > 0) {
                    resetExpiredReservationUi(showtimeId);
                }
            })
            .listen('.booking:timer', handleRealtimeEvent);

    }

    function refreshSeatStatusNow(showtimeId, force) {
        if (reservationRequestInFlight && !force) {
            return;
        }

        if (!showtimeId) {
            showtimeId = currentSeatMapShowtimeId || window.selectedShowtimeId;
        }

        if (!showtimeId) {
            return;
        }

        var url = bookingPageRoutes.bookingSeatMap + '?showtime_id=' + showtimeId;

        fetch(url, {
                headers: bookingSeatHeaders()
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data || data.error) {
                    return;
                }

                currentSeatMapShowtimeId = showtimeId;
                syncSeatState(data.bookedSeats || [], data.reservedSeats || []);
                currentSeatPrices = data.prices || currentSeatPrices;
                currentMyReservedSeats = uniqueSeatList(data.myReservedSeats || []);

                if (currentMyReservedSeats.length > 0 && Number(data.remainingSeconds || 0) > 0) {
                    window.seatsConfirmed = true;
                    window.selectedSeats = currentMyReservedSeats.slice();
                } else if (window.seatsConfirmed) {
                    resetExpiredReservationUi(showtimeId);
                    return;
                }

                if (!window.seatsConfirmed) {
                    var unavailableSeats = [];
                    window.selectedSeats = uniqueSeatList(window.selectedSeats || []).filter(function(seat) {
                    var locked = getUnavailableSeatsForCurrentUser().indexOf(seat) !== -1;
                        if (locked) {
                            unavailableSeats.push(seat);
                            return false;
                        }
                        return true;
                    });

                    if (unavailableSeats.length > 0) {
                        showErrorMsg('Có ghế vừa được người khác giữ chỗ: ' + unavailableSeats.join(', '));
                        updateSeatSummaryNow();
                    }

                    if (currentSeatLayout) {
                        renderSeatMapFull(currentSeatLayout, data.bookedSeats || [], data.reservedSeats || [], data.prices || {}, true);
                    } else {
                        generateDefaultSeatLayoutNow(data.bookedSeats || [], data.reservedSeats || [], true);
                    }
                }
            })
            .catch(function(error) {
                console.error('Error refreshing seat status:', error);
            });
    }

    function reserveCurrentSelectionNow(showtimeId) {
        var seats = uniqueSeatList(window.selectedSeats || []);

        return fetch(bookingPageRoutes.bookingReserveSeats, {
            method: 'POST',
            headers: bookingSeatHeaders(),
            body: JSON.stringify({
                showtime_id: showtimeId,
                seats: seats
            })
        }).then(function(response) {
            return response.json().then(function(data) {
                return {
                    status: response.status,
                    data: data
                };
            });
        });
    }

    function releaseCurrentSelectionNow(showtimeId, seats, requestOptions) {
        requestOptions = requestOptions || {};

        return fetch(bookingPageRoutes.bookingReleaseSeats, {
            method: 'POST',
            headers: bookingSeatHeaders(),
            body: JSON.stringify({
                showtime_id: showtimeId,
                seats: uniqueSeatList(seats || [])
            }),
            keepalive: requestOptions.keepalive === true
        }).then(function(response) {
            return response.json().then(function(data) {
                return {
                    status: response.status,
                    data: data
                };
            });
        });
    }

    function getCurrentSeatMapShowtimeId() {
        var showtimeInput = document.getElementById('showtimeIdInput');
        var showtimeId = showtimeInput ? showtimeInput.value : null;
        return showtimeId || window.selectedShowtimeId || currentSeatMapShowtimeId;
    }

    // --- EARLY VERSION of toggleSeat (used BEFORE IIFE runs) ---
    window.toggleSeat = function(seatNumber) {
        console.log('Toggle seat:', seatNumber);

        // Check if seats already confirmed
        if (window.seatsConfirmed) {
            showErrorMsg('Bạn đã xác nhận ghế rồi! Nếu muốn chọn lại, vui lòng nhấn "Chọn lại ghế"');
            return;
        }

        var seatElement = document.querySelector('.seat[data-seat="' + seatNumber + '"]');
        if (!seatElement) return;

        // Don't allow toggle if booked or disabled
        if (seatElement.classList.contains('seat-booked') || seatElement.classList.contains('seat-disabled')) {
            return;
        }

        // Initialize selectedSeats if not exists
        if (!window.selectedSeats) window.selectedSeats = [];

        // Toggle selection
        if (seatElement.classList.contains('seat-selected')) {
            seatElement.classList.remove('seat-selected');
            window.selectedSeats = window.selectedSeats.filter(function(s) {
                return s !== seatNumber;
            });
        } else {
            // Check max 8 seats
            if (window.selectedSeats.length >= 8) {
                showErrorMsg('Chỉ được đặt tối đa 8 ghế!');
                return;
            }

            seatElement.classList.add('seat-selected');
            window.selectedSeats.push(seatNumber);
        }

        // Update summary
        updateSeatSummaryNow();
    };

    // --- EARLY VERSION of updateFoodQuantity (used BEFORE IIFE runs) ---
    function getFoodSelectionSummary() {
        var foodInputs = document.querySelectorAll('input[name^="food_items["]');
        var totalQty = 0;
        var totalPrice = 0;

        for (var i = 0; i < foodInputs.length; i++) {
            var qty = parseInt(foodInputs[i].value) || 0;
            if (qty <= 0) {
                continue;
            }

            totalQty += qty;
            var foodIdMatch = foodInputs[i].name.match(/\[(\d+)\]/);
            var foodId = foodIdMatch ? foodIdMatch[1] : null;
            var foodCard = foodId ? document.querySelector('.food-item-card-compact[data-food-id="' + foodId + '"]') : null;
            var price = foodCard ? (parseInt(foodCard.getAttribute('data-food-price')) || 0) : 0;
            totalPrice += price * qty;
        }

        return {
            quantity: totalQty,
            total: totalPrice,
            text: totalQty > 0
                ? totalQty + ' combo - ' + new Intl.NumberFormat('vi-VN').format(totalPrice) + ' d'
                : 'Chua chon combo nao'
        };
    }

    function updateFoodModalSummary() {
        var summary = getFoodSelectionSummary();
        var launcherSummary = document.getElementById('foodLauncherSummary');
        if (launcherSummary) launcherSummary.textContent = summary.text;

        var modalSummary = document.getElementById('foodModalSummary');
        if (modalSummary) modalSummary.textContent = summary.text;
    }

    window.openFoodModal = function() {
        var foodSection = document.getElementById('foodSection');
        if (!foodSection) return;

        foodSection.style.display = 'block';
        document.body.classList.add('food-modal-open');
        updateFoodModalSummary();
    };

    window.closeFoodModal = function() {
        var foodSection = document.getElementById('foodSection');
        if (foodSection) foodSection.style.display = 'none';
        document.body.classList.remove('food-modal-open');
        updateFoodModalSummary();
    };

    function ensureFoodModalControls() {
        var header = document.querySelector('#foodSection .food-iframe-header');
        if (header && !header.querySelector('.food-modal-close-btn')) {
            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'food-modal-close-btn';
            closeBtn.setAttribute('aria-label', 'Dong combo');
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.addEventListener('click', window.closeFoodModal);
            header.appendChild(closeBtn);
        }

        var shell = document.querySelector('#foodSection .food-iframe-shell');
        if (shell && !shell.querySelector('.food-modal-actions')) {
            var actions = document.createElement('div');
            actions.className = 'food-modal-actions';
            actions.innerHTML = '<div><span>Combo da chon</span><strong id="foodModalSummary">Chua chon combo nao</strong></div><button type="button" class="food-modal-done-btn">Xong</button>';
            actions.querySelector('button').addEventListener('click', window.closeFoodModal);
            shell.appendChild(actions);
        }
    }

    window.updateFoodQuantity = function(foodId, change) {
        console.log('updateFoodQuantity called:', {
            foodId,
            change
        });
        var input = document.getElementById('food_' + foodId);
        if (!input) {
            console.error('Food input not found for ID:', foodId);
            return;
        }

        var currentValue = parseInt(input.value) || 0;
        var newValue = currentValue + change;

        if (newValue < 0) newValue = 0;
        if (newValue > 10) newValue = 10;

        console.log('Updating food quantity:', {
            foodId,
            currentValue,
            newValue
        });
        input.value = newValue;

        // Update total price immediately
        updateBookingSummaryFull();
        updateFoodModalSummary();
    };

    // Also make it available without window prefix
    var updateFoodQuantity = window.updateFoodQuantity;

    document.addEventListener('DOMContentLoaded', function() {
        ensureFoodModalControls();
        updateFoodModalSummary();

        var foodSection = document.getElementById('foodSection');
        if (foodSection && !foodSection.dataset.overlayCloseBound) {
            foodSection.dataset.overlayCloseBound = '1';
            foodSection.addEventListener('click', function(event) {
                if (event.target === foodSection) {
                    window.closeFoodModal();
                }
            });
        }
    });

    // --- EARLY VERSION of confirmSeats - SYNC version (immediate UI update) ---
    // This is the version called from onclick="confirmSeats()" BEFORE IIFE runs.
    // The IIFE will overwrite it with async version, but we keep this for reference.
    window.confirmSeats = function() {
        console.log('Confirm seats clicked');

        // Validate seats strictly
        var validation = validateSeatSelectionNow(true);

        if (!validation.valid) {
            return false;
        }

        // Mark as confirmed
        window.seatsConfirmed = true;

        // Hide confirm button, show reselect button
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) confirmBtn.style.display = 'none';

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) reselectBtn.style.display = 'inline-block';

        // Show food/payment/email sections
        var foodLauncher = document.getElementById('foodModalLauncher');
        if (foodLauncher) foodLauncher.style.display = 'block';

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) paymentSection.style.display = 'block';

        var emailSection = document.getElementById('emailSection');
        if (emailSection) emailSection.style.display = 'block';

        var priceInfoBox = document.getElementById('priceInfoBox');
        if (priceInfoBox) priceInfoBox.style.display = 'block';

        // Disable seat selection
        var seats = document.querySelectorAll('.seat');
        for (var i = 0; i < seats.length; i++) {
            if (!seats[i].classList.contains('seat-booked')) {
                seats[i].style.opacity = '0.6';
                seats[i].style.cursor = 'not-allowed';
            }
        }

        updateBookingSummaryFull();

        var emailSection = document.getElementById('emailSection');
        if (emailSection) {
            setTimeout(function() {
                emailSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 120);
        }

        return true;
    };

    // --- EARLY VERSION of reselectSeats ---
    window.reselectSeats = function() {
        window.seatsConfirmed = false;

        // Show confirm button, hide reselect button
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) confirmBtn.style.display = 'inline-block';

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) reselectBtn.style.display = 'none';

        // Hide food/payment sections
        var foodSection = document.getElementById('foodSection');
        if (foodSection) foodSection.style.display = 'none';
        var foodLauncher = document.getElementById('foodModalLauncher');
        if (foodLauncher) foodLauncher.style.display = 'none';

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) paymentSection.style.display = 'none';

        // Enable seat selection
        var seats = document.querySelectorAll('.seat');
        for (var i = 0; i < seats.length; i++) {
            if (!seats[i].classList.contains('seat-booked')) {
                seats[i].style.opacity = '1';
                seats[i].style.cursor = 'pointer';
            }
        }

        updateBookingSummaryFull();
    };

    // Validate seat selection
    function validateSeatSelectionNow(showAlert) {
        return validateSeatSelectionForSeats(window.selectedSeats || [], showAlert);
    }

    function validateSeatSelectionForSeatsLegacy(seats, showAlert) {
        if (!seats || seats.length === 0) {
            if (showAlert) showErrorMsg('Vui lòng chọn ít nhất 1 ghế!');
            return {
                valid: false,
                message: 'Vui lòng chọn ít nhất 1 ghế!'
            };
        }

        if (seats.length > 8) {
            if (showAlert) showErrorMsg('Chỉ được đặt tối đa 8 ghế!');
            return {
                valid: false,
                message: 'Chỉ được đặt tối đa 8 ghế!'
            };
        }

        var booked = getUnavailableSeatsForCurrentUser();
        var rows = document.querySelectorAll('.seat-row');

        for (var r = 0; r < rows.length; r++) {
            var rowEl = rows[r];
            var groups = getSeatGroupsFromRow(rowEl);
            if (!groups.length) continue;

            var rowName = groups[0][0].dataset.seat.charAt(0);

            for (var g = 0; g < groups.length; g++) {
                var groupSeats = groups[g];
                var groupSeatNumbers = groupSeats.map(function(seatEl) {
                    return seatEl.dataset.seat;
                });
                var selectedInGroup = seats.filter(function(seat) {
                    return groupSeatNumbers.indexOf(seat) !== -1;
                });

                if (selectedInGroup.length === 0) continue;

                var cols = selectedInGroup.map(function(seat) {
                    return parseInt(seat.substring(1), 10);
                }).sort(function(a, b) {
                    return a - b;
                });

                if (cols.length === 1) {
                    var allGroupCols = groupSeats.map(function(seatEl) {
                        return parseInt(seatEl.dataset.seat.substring(1), 10);
                    }).sort(function(a, b) {
                        return a - b;
                    });
                    var selectedCol = cols[0];
                    var minCol = allGroupCols[0];
                    var maxCol = allGroupCols[allGroupCols.length - 1];
                    var leftEdgeBooked = booked.indexOf(rowName + minCol) !== -1;
                    var rightEdgeBooked = booked.indexOf(rowName + maxCol) !== -1;

                    if (selectedCol === minCol + 1 && !leftEdgeBooked) {
                        var leftMessage = 'Không được chọn một ghế ở vị trí thứ 2 từ đầu cụm ' + rowName + '. Hãy chọn ghế ngoài cùng hoặc chọn thêm ghế liền kề.';
                        if (showAlert) showErrorMsg(leftMessage);
                        return { valid: false, message: leftMessage };
                    }

                    if (selectedCol === maxCol - 1 && !rightEdgeBooked) {
                        var rightMessage = 'Không được chọn một ghế ở vị trí thứ 2 từ cuối cụm ' + rowName + '. Hãy chọn ghế ngoài cùng hoặc chọn thêm ghế liền kề.';
                        if (showAlert) showErrorMsg(rightMessage);
                        return { valid: false, message: rightMessage };
                    }
                }

                for (var i = 0; i < cols.length - 1; i++) {
                    if (cols[i + 1] - cols[i] > 1) {
                        var hasFreeGap = false;
                        for (var gapCol = cols[i] + 1; gapCol < cols[i + 1]; gapCol++) {
                            var gapSeat = rowName + gapCol;
                            if (booked.indexOf(gapSeat) === -1) {
                                hasFreeGap = true;
                                break;
                            }
                        }
                        if (hasFreeGap) {
                            var message = 'Ghế trong cụm hàng ' + rowName + ' phải liền kề nhau! Không được bỏ trống ghế ở giữa (ví dụ: ' + rowName + cols[i] + ' và ' + rowName + cols[i + 1] + ').';
                            if (showAlert) showErrorMsg(message);
                            return {
                                valid: false,
                                message: message
                            };
                        }
                    }
                }

                var orphanValidation = validateGroupOrphanSeats(groupSeats, seats, booked, rowName);
                if (!orphanValidation.valid) {
                    if (showAlert) showErrorMsg(orphanValidation.message);
                    return orphanValidation;
                }
            }
        }

        return {
            valid: true,
            message: ''
        };
    }

    // Update seat summary
    function updateSeatSummaryNow() {
        var quantity = window.selectedSeats ? window.selectedSeats.length : 0;

        // Update selected seats inputs (multiple hidden inputs for array submission)
        var container = document.getElementById('seatsInputContainer');
        if (!container) {
            // Create container if not exists
            container = document.createElement('div');
            container.id = 'seatsInputContainer';
            container.style.display = 'none';
            var form = document.getElementById('bookingForm');
            if (form) form.appendChild(container);
        }

        // Clear old inputs
        container.innerHTML = '';

        // Create new inputs for each seat
        if (window.selectedSeats && window.selectedSeats.length > 0) {
            for (var i = 0; i < window.selectedSeats.length; i++) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'seats[]';
                input.value = window.selectedSeats[i];
                container.appendChild(input);
            }
        }

        // Show seat summary and confirm button
        if (quantity > 0) {
            var display = document.getElementById('selectedSeatsDisplay');
            if (display) display.style.display = 'block';

            var seatsText = document.getElementById('seatsText');
            if (seatsText) seatsText.textContent = window.selectedSeats.join(', ');

            var confirmBtn = document.getElementById('confirmSeatsBtn');
            if (confirmBtn) confirmBtn.disabled = false;

            // Calculate and show price
            calculateSeatPriceNow();
        } else {
            var display = document.getElementById('selectedSeatsDisplay');
            if (display) display.style.display = 'none';

            var confirmBtn = document.getElementById('confirmSeatsBtn');
            if (confirmBtn) confirmBtn.disabled = true;

            var qty = document.getElementById('quantity');
            if (qty) qty.textContent = '0';

            var total = document.getElementById('totalPrice');
            if (total) total.textContent = '0 ₫';
        }
    }

    // Calculate seat price
    function calculateSeatPriceNow() {
        var basePrice = Number(bookingPageConfig.basePrice || 90000);
        var normalPrice = basePrice;
        var vipPrice = Math.round(basePrice * 1.3);
        var couplePrice = Math.round(basePrice * 1.5);

        // Update price display
        var normalEl = document.getElementById('normalPriceDisplay');
        if (normalEl) normalEl.textContent = new Intl.NumberFormat('vi-VN').format(normalPrice) + 'đ';

        var vipEl = document.getElementById('vipPriceDisplay');
        if (vipEl) vipEl.textContent = new Intl.NumberFormat('vi-VN').format(vipPrice) + 'đ';

        var coupleEl = document.getElementById('couplePriceDisplay');
        if (coupleEl) coupleEl.textContent = new Intl.NumberFormat('vi-VN').format(couplePrice) + 'đ';

        var totalPrice = 0;
        var seatBreakdown = {
            normal: 0,
            vip: 0,
            couple: 0
        };

        if (window.selectedSeats) {
            for (var i = 0; i < window.selectedSeats.length; i++) {
                var seat = window.selectedSeats[i];
                var row = seat.charAt(0);
                if (row === 'D' || row === 'E' || row === 'F') {
                    totalPrice += vipPrice;
                    seatBreakdown.vip++;
                } else if (row === 'J') {
                    totalPrice += couplePrice;
                    seatBreakdown.couple++;
                } else {
                    totalPrice += normalPrice;
                    seatBreakdown.normal++;
                }
            }
        }

        // Build quantity text
        var quantityText = (window.selectedSeats ? window.selectedSeats.length : 0) + ' ghế';
        if (seatBreakdown.normal > 0) quantityText += ' (' + seatBreakdown.normal + ' thường';
        if (seatBreakdown.vip > 0) quantityText += (seatBreakdown.normal > 0 ? ', ' : ' (') + seatBreakdown.vip + ' VIP';
        if (seatBreakdown.couple > 0) quantityText += ((seatBreakdown.normal > 0 || seatBreakdown.vip > 0) ? ', ' : ' (') + seatBreakdown.couple + ' đôi';
        if (seatBreakdown.normal > 0 || seatBreakdown.vip > 0 || seatBreakdown.couple > 0) quantityText += ')';

        var qtyEl = document.getElementById('quantity');
        if (qtyEl) qtyEl.textContent = quantityText;

        var unitEl = document.getElementById('unitPrice');
        if (unitEl) unitEl.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + ' ₫';

        var totalEl = document.getElementById('totalPrice');
        if (totalEl) totalEl.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' ₫';
    }

function loadSeatMapNow(showtimeId, options) {
    options = options || {};

    var seatMapContainer = document.getElementById('seatMap');
    var seatSelectionSection = document.getElementById('seatSelectionSection');
    var previousShowtimeId = currentSeatMapShowtimeId;
    var previousSeats = uniqueSeatList(window.selectedSeats || []);
    var shouldStartPurchaseCountdown = options.startPurchaseCountdown === true;
    var shouldPreservePurchaseCountdown = options.preservePurchaseCountdown === true;

    if (previousShowtimeId && previousShowtimeId !== showtimeId && previousSeats.length > 0) {
        releaseCurrentSelectionNow(previousShowtimeId, previousSeats).catch(function(error) {
            console.error('Release previous seats error:', error);
        });
    }

    currentSeatMapShowtimeId = showtimeId;
    currentReservedSeats = [];
        currentBookedSeats = [];
        currentSeatLayout = null;
        currentSeatPrices = null;
        currentMyReservedSeats = [];
        window.selectedSeats = [];
        window.seatsConfirmed = false;
        if (!shouldPreservePurchaseCountdown) {
            clearSeatReservationTimers();
        }

        if (seatSelectionSection) {
            seatSelectionSection.style.display = 'block';
        }

        if (seatMapContainer) {
            seatMapContainer.innerHTML = '<p class="text-center text-muted">Đang tải sơ đồ ghế...</p>';
        }

        var url = bookingPageRoutes.bookingSeatMap + '?showtime_id=' + showtimeId;

        fetch(url, {
                headers: bookingSeatHeaders()
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data || data.error) {
                    var message = data && data.message ? data.message : 'Ban khong the vao phong nay.';
                    if (seatMapContainer) {
                        seatMapContainer.innerHTML = '<p class="text-center text-danger">' + message + '</p>';
                    }
                    showErrorMsg(message);
                    clearSeatReservationTimers();
                    setPostSeatSectionsVisible(false);
                    return;
                }

                currentSeatLayout = data.layout || null;
                currentSeatPrices = data.prices || null;
                syncSeatState(data.bookedSeats || [], data.reservedSeats || []);
                currentMyReservedSeats = uniqueSeatList(data.myReservedSeats || []);

                if (currentMyReservedSeats.length > 0 && Number(data.remainingSeconds || 0) > 0) {
                    window.seatsConfirmed = true;
                    window.selectedSeats = currentMyReservedSeats.slice();
                    startServerReservationCountdown(data);

                    var confirmButton = document.getElementById('confirmSeatsBtn');
                    if (confirmButton) {
                        confirmButton.style.display = 'inline-block';
                        confirmButton.disabled = false;
                        confirmButton.innerHTML = '<i class="fas fa-redo"></i> Ch&#7885;n l&#7841;i gh&#7871;';
                        confirmButton.style.background = '#6c757d';
                        confirmButton.style.color = '#fff';
                        confirmButton.setAttribute('data-seat-action', 'reselect');
                    }

                    var reselectButton = document.getElementById('reselectSeatsBtn');
                    if (reselectButton) reselectButton.style.display = 'none';

                    var foodLauncher = document.getElementById('foodModalLauncher');
                    if (foodLauncher) foodLauncher.style.display = 'block';

                    var paymentSection = document.getElementById('paymentSection');
                    if (paymentSection) paymentSection.style.display = 'block';

                    var emailSection = document.getElementById('emailSection');
                    if (emailSection) emailSection.style.display = 'block';

                    var priceInfoBox = document.getElementById('priceInfoBox');
                    if (priceInfoBox) priceInfoBox.style.display = 'block';
                }

                if (data.screen && data.screen.name) {
                    var screenDisplay = document.getElementById('screenNameDisplay');
                    if (screenDisplay) {
                        screenDisplay.textContent = '(' + data.screen.name + ' - ' + (data.screen.type || '2D') + ')';
                    }
                }

                if (data.layout) {
                    renderSeatMapFull(data.layout, data.bookedSeats || [], data.reservedSeats || [], data.prices || {});
                } else {
                    generateDefaultSeatLayoutNow(data.bookedSeats || [], data.reservedSeats || []);
                }

                updateSeatSummaryNow();
                updatePayButtonState();
                if (shouldStartPurchaseCountdown) {
                    startTicketPurchaseCountdown(showtimeId);
                }
                startSeatRealtimeSubscription(showtimeId);
            })
            .catch(function(error) {
                console.error('Error loading seat map:', error);
                generateDefaultSeatLayoutNow([], []);
                if (shouldStartPurchaseCountdown) {
                    startTicketPurchaseCountdown(showtimeId);
                }
                startSeatRealtimeSubscription(showtimeId);
            });
    }

    function generateDefaultSeatLayoutNow(bookedSeats, reservedSeats) {
        bookedSeats = uniqueSeatList(bookedSeats || []);
        reservedSeats = uniqueSeatList(reservedSeats || []);
        syncSeatState(bookedSeats, reservedSeats);

        var rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        var seatsPerRow = 12;
        var seatMapContainer = document.getElementById('seatMap');
        var html = '';

        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            html += '<div class="seat-row">';
            html += '<div class="seat-row-label">' + row + '</div>';

            if (row === 'J') {
                for (var col = 1; col <= 6; col++) {
                    var seatNumber = row + col;
                    var seatClass = 'seat seat-couple';
                    var isBooked = bookedSeats.indexOf(seatNumber) !== -1;
                    var isReserved = reservedSeats.indexOf(seatNumber) !== -1;
                    var isMyReserved = currentMyReservedSeats.indexOf(seatNumber) !== -1;

                    if (isBooked) {
                        seatClass += ' seat-booked';
                    } else if (isMyReserved) {
                        seatClass += ' seat-my-reserved';
                    } else if (isReserved) {
                        seatClass += ' seat-reserved';
                    } else if (window.selectedSeats.indexOf(seatNumber) !== -1) {
                        seatClass += ' seat-selected';
                    }

                    if (col === 4) {
                        html += '<div class="seat-space"></div>';
                    }

                    var onclick = (isBooked || isReserved || isMyReserved) ? '' : 'onclick="toggleSeat(\'' + seatNumber + '\')"';
                    html += '<div class="' + seatClass + '" data-seat="' + seatNumber + '" ' + onclick + '>' + col + '</div>';
                }
            } else {
                for (var seatCol = 1; seatCol <= seatsPerRow; seatCol++) {
                    var seatNo = row + seatCol;
                    var seatClassNormal = 'seat';
                    var booked = bookedSeats.indexOf(seatNo) !== -1;
                    var reserved = reservedSeats.indexOf(seatNo) !== -1;
                    var isMyReservedSeat = currentMyReservedSeats.indexOf(seatNo) !== -1;

                    if (booked) {
                        seatClassNormal += ' seat-booked';
                    } else if (row === 'D' || row === 'E' || row === 'F') {
                        seatClassNormal += ' seat-vip';
                    }

                    if (isMyReservedSeat) {
                        seatClassNormal += ' seat-my-reserved';
                    } else if (reserved) {
                        seatClassNormal += ' seat-reserved';
                    } else if (!booked && window.selectedSeats.indexOf(seatNo) !== -1) {
                        seatClassNormal += ' seat-selected';
                    }

                    if (seatCol === 7) {
                        html += '<div class="seat-space"></div>';
                    }

                    var seatOnclick = (booked || reserved || isMyReservedSeat) ? '' : 'onclick="toggleSeat(\'' + seatNo + '\')"';
                    html += '<div class="' + seatClassNormal + '" data-seat="' + seatNo + '" ' + seatOnclick + '>' + seatCol + '</div>';
                }
            }

            html += '</div>';
        }

        if (seatMapContainer) {
            seatMapContainer.innerHTML = html;
        }

        updateSeatSummaryNow();
    }

    function renderSeatMapFull(layout, bookedSeats, reservedSeats, prices) {
        bookedSeats = uniqueSeatList(bookedSeats || []);
        reservedSeats = uniqueSeatList(reservedSeats || []);
        syncSeatState(bookedSeats, reservedSeats);

        var seatMapContainer = document.getElementById('seatMap');
        var html = '';

        if (!layout || !Array.isArray(layout)) {
            generateDefaultSeatLayoutNow(bookedSeats, reservedSeats);
            return;
        }

        for (var i = 0; i < layout.length; i++) {
            var row = layout[i];
            html += '<div class="seat-row">';
            html += '<div class="seat-row-label">' + row.row + '</div>';

            for (var j = 0; j < row.seats.length; j++) {
                var seat = row.seats[j];

                if (seat.type === 'space' || seat.type === 'aisle') {
                    html += '<div class="seat-space"></div>';
                    continue;
                }

                var seatClass = 'seat';
                var seatNumber = seat.number || '';
                var isBookedSeat = seatNumber && bookedSeats.indexOf(seatNumber) !== -1;
                var isReservedSeat = seatNumber && reservedSeats.indexOf(seatNumber) !== -1;
                var isMyReservedSeat = seatNumber && currentMyReservedSeats.indexOf(seatNumber) !== -1;
                var isSelectedSeat = seatNumber && window.selectedSeats.indexOf(seatNumber) !== -1;

                if (seat.type === 'vip') seatClass += ' seat-vip';
                if (seat.type === 'couple') seatClass += ' seat-couple';
                if (seat.type === 'disabled' || !seat.available) seatClass += ' seat-disabled';
                if (isBookedSeat) seatClass += ' seat-booked';
                if (isMyReservedSeat) seatClass += ' seat-my-reserved';
                else if (isReservedSeat) seatClass += ' seat-reserved';
                if (!isBookedSeat && !isReservedSeat && !isMyReservedSeat && isSelectedSeat) seatClass += ' seat-selected';

                var onclick = (!isBookedSeat && !isReservedSeat && !isMyReservedSeat && seat.type !== 'disabled' && seat.available) ?
                    'onclick="toggleSeat(\'' + seat.number + '\')"' :
                    '';

                html += '<div class="' + seatClass + '" data-seat="' + seat.number + '" ' + onclick + '>';
                html += (seat.label || seat.number || '');
                html += '</div>';

                if (seat.number) {
                    var colNum = parseInt(String(seat.number).substring(1), 10);
                    var nextSeat = row.seats[j + 1];
                    var nextCol = nextSeat && nextSeat.number ? parseInt(String(nextSeat.number).substring(1), 10) : null;
                    if (nextCol !== null && nextCol - colNum > 1) {
                        html += '<div class="seat-space"></div>';
                    } else if (colNum === 6 && nextCol === 7) {
                        html += '<div class="seat-space"></div>';
                    } else if (row.row === 'J' && colNum === 3 && nextCol === 4) {
                        html += '<div class="seat-space"></div>';
                    }
                }
            }

            html += '</div>';
        }

        if (seatMapContainer) {
            seatMapContainer.innerHTML = html;
        }

        updateSeatSummaryNow();
    }

    function validateSeatSelectionForSeats(seats, showAlert) {
        var booked = getUnavailableSeatsForCurrentUser();
        var selected = uniqueSeatList(seats || []);

        if (!selected || selected.length === 0) {
            if (showAlert) showErrorMsg('Vui lòng chọn ít nhất 1 ghế!');
            return {
                valid: false,
                message: 'Vui lòng chọn ít nhất 1 ghế!'
            };
        }

        if (selected.length > 8) {
            if (showAlert) showErrorMsg('Chỉ được đặt tối đa 8 ghế!');
            return {
                valid: false,
                message: 'Chỉ được đặt tối đa 8 ghế!'
            };
        }

        for (var i = 0; i < selected.length; i++) {
            if (booked.indexOf(selected[i]) !== -1) {
                var msg = 'Ghế ' + selected[i] + ' đã được người khác giữ chỗ hoặc đã được đặt.';
                if (showAlert) showErrorMsg(msg);
                return {
                    valid: false,
                    message: msg
                };
            }
        }

        return validateSeatSelectionForSeatsLegacy(selected, showAlert);
    }

(function() {
    var confirmSeatLabel = 'Xác nhận chọn ghế';
    var confirmSeatHtml = '<i class="fas fa-check-circle"></i> ' + confirmSeatLabel;
    var reselectSeatHtml = '<i class="fas fa-redo"></i> Ch&#7885;n l&#7841;i gh&#7871;';
    var holdingSeatHtml = '<i class="fas fa-spinner fa-spin"></i> Đang giữ ghế...';

    function resetSeatActionButton(disabled) {
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'inline-block';
            confirmBtn.disabled = typeof disabled === 'boolean'
                ? disabled
                : uniqueSeatList(window.selectedSeats || []).length === 0;
            confirmBtn.innerHTML = confirmSeatHtml;
            confirmBtn.style.background = '#ffc107';
            confirmBtn.style.color = '#000';
            confirmBtn.setAttribute('data-seat-action', 'confirm');
        }

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) {
            reselectBtn.style.display = 'none';
        }
    }

    function setSeatActionButtonAsReselect() {
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'inline-block';
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = reselectSeatHtml;
            confirmBtn.style.background = '#6c757d';
            confirmBtn.style.color = '#fff';
            confirmBtn.setAttribute('data-seat-action', 'reselect');
        }

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) {
            reselectBtn.style.display = 'none';
        }
    }

    function isSeatActionButtonReselect() {
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        return !!(
            window.seatsConfirmed ||
            (confirmBtn && (
                confirmBtn.getAttribute('data-seat-action') === 'reselect' ||
                confirmBtn.textContent.indexOf('Chọn lại ghế') !== -1
            ))
        );
    }

    window.resetSeatActionButtonUi = resetSeatActionButton;
    window.setSeatActionButtonReselectUi = setSeatActionButtonAsReselect;

    function isSeatLockedNow(seatNumber) {
        return getUnavailableSeatsForCurrentUser().indexOf(seatNumber) !== -1;
    }

    function ensureSeatRealtimeNotice() {
        var notice = document.getElementById('seatRealtimeNotice');
        if (notice) {
            return notice;
        }

        var seatSelectionSection = document.getElementById('seatSelectionSection');
        if (!seatSelectionSection) {
            return null;
        }

        notice = document.createElement('div');
        notice.id = 'seatRealtimeNotice';
        notice.className = 'alert alert-info';
        notice.style.display = 'none';
        notice.style.marginTop = '12px';
        notice.style.marginBottom = '0';
        notice.style.maxWidth = '600px';
        notice.style.marginLeft = 'auto';
        notice.style.marginRight = 'auto';

        var seatMap = document.getElementById('seatMap');
        if (seatMap && seatMap.parentNode) {
            seatMap.parentNode.insertBefore(notice, seatMap.nextSibling);
        } else {
            seatSelectionSection.appendChild(notice);
        }

        return notice;
    }

    function showSeatRealtimeNotice(message, tone) {
        var notice = ensureSeatRealtimeNotice();
        if (!notice) {
            return;
        }

        if (!message) {
            notice.style.display = 'none';
            notice.textContent = '';
            notice.className = 'alert alert-info';
            return;
        }

        var alertClass = 'alert-info';
        if (tone === 'warning') {
            alertClass = 'alert-warning';
        } else if (tone === 'success') {
            alertClass = 'alert-success';
        }

        notice.className = 'alert ' + alertClass;
        notice.textContent = message;
        notice.style.display = 'block';
    }

    function renderSeatStateFromPayload(data, preserveSelection) {
        currentSeatPrices = data.prices || currentSeatPrices;

        var bookedSeats = uniqueSeatList(data.bookedSeats || []);
        var payloadMyReservedSeats = firstNonEmptySeatList(data.myReservedSeats, data.lockedSeats, currentMyReservedSeats);
        if (payloadMyReservedSeats.length > 0) {
            currentMyReservedSeats = payloadMyReservedSeats;
        }

        var reservedSeats = mergeSeatLists(data.reservedSeats || [], currentMyReservedSeats);

        syncSeatState(bookedSeats, reservedSeats);

        if (!preserveSelection) {
            window.selectedSeats = [];
            window.seatsConfirmed = false;
        }

        if (currentSeatLayout) {
            renderSeatMapWithState(currentSeatLayout, bookedSeats, reservedSeats, data.prices || currentSeatPrices || {}, true);
        } else {
            generateDefaultSeatLayoutNow(bookedSeats, reservedSeats);
        }

        updateSeatSummaryNow();
        updatePayButtonState();
    }

    applySeatRealtimeUpdate = function(data) {
        if (!data) {
            return;
        }

        var showtimeId = data.showtimeId || currentSeatMapShowtimeId || window.selectedShowtimeId;
        if (!showtimeId) {
            return;
        }

        currentSeatMapShowtimeId = showtimeId;

        var previousSelectedSeats = uniqueSeatList(window.selectedSeats || []);
        var eventSeats = uniqueSeatList(data.seats || []);
        var isCurrentUserEvent = Number(data.userId || 0) === Number(bookingPageConfig.currentUserId || 0);

        if (isCurrentUserEvent && (data.action === 'selected' || data.action === 'reserved' || data.action === 'timer')) {
            currentMyReservedSeats = uniqueSeatList((currentMyReservedSeats || []).concat(eventSeats.length ? eventSeats : previousSelectedSeats));
        } else if (isCurrentUserEvent && (data.action === 'released' || data.action === 'expired' || data.action === 'paid' || data.action === 'booked')) {
            currentMyReservedSeats = uniqueSeatList(currentMyReservedSeats || []).filter(function(seat) {
                return eventSeats.indexOf(seat) === -1;
            });
        }

        if (window.seatsConfirmed) {
            var currentReservedBeforeRender = uniqueSeatList(data.reservedSeats || []);
            var stillReserved = uniqueSeatList(currentMyReservedSeats || []).filter(function(seat) {
                return currentReservedBeforeRender.indexOf(seat) !== -1;
            });

            if (stillReserved.length !== uniqueSeatList(currentMyReservedSeats || []).length) {
                window.seatsConfirmed = false;
                currentMyReservedSeats = [];
                window.selectedSeats = [];

                if (reservationHeartbeatInterval) {
                    clearInterval(reservationHeartbeatInterval);
                    reservationHeartbeatInterval = null;
                }

                resetSeatActionButton();

                var foodSection = document.getElementById('foodSection');
                if (foodSection) foodSection.style.display = 'none';
                var foodLauncher = document.getElementById('foodModalLauncher');
                if (foodLauncher) foodLauncher.style.display = 'none';

                var paymentSection = document.getElementById('paymentSection');
                if (paymentSection) paymentSection.style.display = 'none';

                showSeatRealtimeNotice('Ghế giữ chỗ của bạn đã thay đổi. Vui lòng chọn lại ghế.', 'warning');
            }

            renderSeatStateFromPayload(data, true);
            if (window.seatsConfirmed) {
                showConfirmedSeatUi(false);
            }
            return;
        }

        var myReservedFromEvent = isCurrentUserEvent
            ? uniqueSeatList((currentMyReservedSeats || []).concat(eventSeats))
            : uniqueSeatList(currentMyReservedSeats || []);
        var availableAfterSync = uniqueSeatList(data.bookedSeats || []).concat(uniqueSeatList(data.reservedSeats || []))
            .filter(function(seat) {
                return myReservedFromEvent.indexOf(seat) === -1;
            });
        var lockedSeats = previousSelectedSeats.filter(function(seat) {
            return availableAfterSync.indexOf(seat) !== -1;
        });

        window.selectedSeats = previousSelectedSeats.filter(function(seat) {
            return availableAfterSync.indexOf(seat) === -1;
        });

        renderSeatStateFromPayload(data, true);

        if (lockedSeats.length > 0) {
            showSeatRealtimeNotice('Ghế ' + lockedSeats.join(', ') + ' đã chuyển sang trạng thái đang giữ chỗ.', 'warning');
        } else {
            showSeatRealtimeNotice('', 'info');
        }
    };

    refreshSeatStatusNow = function(showtimeId, force) {
        if (reservationRequestInFlight && !force) {
            return;
        }

        if (!showtimeId) {
            showtimeId = currentSeatMapShowtimeId || window.selectedShowtimeId;
        }

        if (!showtimeId) {
            return;
        }

        var url = bookingPageRoutes.bookingSeatMap + '?showtime_id=' + showtimeId;

        fetch(url, {
                headers: bookingSeatHeaders()
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data || data.error) {
                    return;
                }

                currentSeatMapShowtimeId = showtimeId;

                var previousSelectedSeats = uniqueSeatList(window.selectedSeats || []);
                var responseMyReservedSeats = uniqueSeatList(data.myReservedSeats || []);
                var lockedSeats = uniqueSeatList((data.bookedSeats || []).concat(data.reservedSeats || [])).filter(function(seat) {
                    return previousSelectedSeats.indexOf(seat) !== -1 && responseMyReservedSeats.indexOf(seat) === -1;
                });

                syncSeatState(data.bookedSeats || [], data.reservedSeats || []);
                currentMyReservedSeats = responseMyReservedSeats;

                if (currentMyReservedSeats.length > 0 && Number(data.remainingSeconds || 0) > 0) {
                    window.seatsConfirmed = true;
                    window.selectedSeats = currentMyReservedSeats.slice();
                    showConfirmedSeatUi(false);
                } else if (window.seatsConfirmed) {
                    resetExpiredReservationUi(showtimeId);
                    return;
                }

                if (!window.seatsConfirmed) {
                    window.selectedSeats = previousSelectedSeats.filter(function(seat) {
                        return getUnavailableSeatsForCurrentUser().indexOf(seat) === -1;
                    });
                }

                renderSeatStateFromPayload(data, true);

                if (lockedSeats.length > 0) {
                    showSeatRealtimeNotice('Ghế ' + lockedSeats.join(', ') + ' đã chuyển sang trạng thái đang giữ chỗ.', 'warning');
                } else {
                    showSeatRealtimeNotice('', 'info');
                }
            })
            .catch(function(error) {
                console.error('Error refreshing seat status:', error);
            });
    };

    updateSeatSummaryNow = function() {
        var selected = uniqueSeatList(window.selectedSeats || []);

        var container = document.getElementById('seatsInputContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'seatsInputContainer';
            container.style.display = 'none';
            var form = document.getElementById('bookingForm');
            if (form) form.appendChild(container);
        }

        container.innerHTML = '';

        for (var i = 0; i < selected.length; i++) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'seats[]';
            input.value = selected[i];
            container.appendChild(input);
        }

        var display = document.getElementById('selectedSeatsDisplay');
        var seatsText = document.getElementById('seatsText');
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        var qty = document.getElementById('quantity');
        var total = document.getElementById('totalPrice');

        if (selected.length > 0) {
            if (display) display.style.display = 'block';
            if (seatsText) seatsText.textContent = selected.join(', ');
            if (confirmBtn) confirmBtn.disabled = false;
            calculateSeatPriceNow();
            return;
        }

        if (display) display.style.display = 'none';
        if (confirmBtn) confirmBtn.disabled = true;
        if (qty) qty.textContent = '0';
        if (total) total.textContent = '0 ₫';
    };

    calculateSeatPriceNow = function() {
        var basePrice = Number(bookingPageConfig.basePrice || 90000);
        var normalPrice = basePrice;
        var vipPrice = Math.round(basePrice * 1.3);
        var couplePrice = Math.round(basePrice * 1.5);
        var selected = uniqueSeatList(window.selectedSeats || []);

        var normalEl = document.getElementById('normalPriceDisplay');
        if (normalEl) normalEl.textContent = new Intl.NumberFormat('vi-VN').format(normalPrice) + ' ₫';

        var vipEl = document.getElementById('vipPriceDisplay');
        if (vipEl) vipEl.textContent = new Intl.NumberFormat('vi-VN').format(vipPrice) + ' ₫';

        var coupleEl = document.getElementById('couplePriceDisplay');
        if (coupleEl) coupleEl.textContent = new Intl.NumberFormat('vi-VN').format(couplePrice) + ' ₫';

        var totalPrice = 0;
        var breakdown = {
            normal: 0,
            vip: 0,
            couple: 0
        };

        for (var i = 0; i < selected.length; i++) {
            var seat = selected[i];
            var row = seat.charAt(0);
            if (row === 'D' || row === 'E' || row === 'F') {
                totalPrice += vipPrice;
                breakdown.vip++;
            } else if (row === 'J') {
                totalPrice += couplePrice;
                breakdown.couple++;
            } else {
                totalPrice += normalPrice;
                breakdown.normal++;
            }
        }

        var quantityText = selected.length + ' ghế';
        if (breakdown.normal > 0) quantityText += ' (' + breakdown.normal + ' thường';
        if (breakdown.vip > 0) quantityText += (breakdown.normal > 0 ? ', ' : ' (') + breakdown.vip + ' VIP';
        if (breakdown.couple > 0) quantityText += ((breakdown.normal > 0 || breakdown.vip > 0) ? ', ' : ' (') + breakdown.couple + ' đôi';
        if (breakdown.normal > 0 || breakdown.vip > 0 || breakdown.couple > 0) quantityText += ')';

        var qtyEl = document.getElementById('quantity');
        if (qtyEl) qtyEl.textContent = quantityText;

        var unitEl = document.getElementById('unitPrice');
        if (unitEl) unitEl.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + ' ₫';

        var totalEl = document.getElementById('totalPrice');
        if (totalEl) totalEl.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' ₫';

        updatePayButtonState();
    };

    validateSeatSelectionNow = function(showAlert) {
        return validateSeatSelectionForSeats(uniqueSeatList(window.selectedSeats || []), showAlert);
    };

    function firstNonEmptySeatList() {
        for (var i = 0; i < arguments.length; i++) {
            var seats = uniqueSeatList(arguments[i] || []);
            if (seats.length > 0) {
                return seats;
            }
        }

        return [];
    }

    function mergeSeatLists() {
        var merged = [];
        for (var i = 0; i < arguments.length; i++) {
            merged = merged.concat(arguments[i] || []);
        }

        return uniqueSeatList(merged);
    }

    function setElementDisplay(id, displayValue) {
        var element = document.getElementById(id);
        if (element) {
            element.style.display = displayValue;
        }
    }

    function setPostSeatSectionsVisible(visible) {
        var displayValue = visible ? 'block' : 'none';
        setElementDisplay('foodModalLauncher', displayValue);
        setElementDisplay('emailSection', displayValue);
        setElementDisplay('priceInfoBox', displayValue);

        if (!visible && typeof window.closeFoodModal === 'function') {
            window.closeFoodModal();
        }

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) {
            paymentSection.style.display = 'none';
            paymentSection.classList.remove('payment-options-open');
            paymentSection.setAttribute('data-payment-confirmed', 'false');
        }
    }

    function setAvailableSeatsInteractive(interactive) {
        var seats = document.querySelectorAll('.seat');
        for (var i = 0; i < seats.length; i++) {
            if (seats[i].classList.contains('seat-booked') ||
                seats[i].classList.contains('seat-reserved') ||
                seats[i].classList.contains('seat-my-reserved') ||
                seats[i].classList.contains('seat-disabled')) {
                continue;
            }

            seats[i].style.opacity = interactive ? '' : '0.6';
            seats[i].style.cursor = interactive ? '' : 'not-allowed';
        }
    }

    function showEditableSeatUi(message, tone, keepFoodVisible) {
        window.seatsConfirmed = false;

        resetSeatActionButton();
        setPostSeatSectionsVisible(false);
        if (keepFoodVisible) {
            setElementDisplay('emailSection', 'block');
            setElementDisplay('priceInfoBox', 'block');
            setElementDisplay('foodModalLauncher', 'block');
        }
        setAvailableSeatsInteractive(true);
        updateSeatSummaryNow();
        updateBookingSummaryFull();
        updatePayButtonState();

        if (message) {
            showSeatRealtimeNotice(message, tone || 'warning');
        }
    }

    function showConfirmedSeatUi(shouldScroll) {
        window.seatsConfirmed = true;

        setSeatActionButtonAsReselect();
        setPostSeatSectionsVisible(true);
        setAvailableSeatsInteractive(false);
        updateBookingSummaryFull();
        updatePayButtonState();

        if (shouldScroll) {
            var scrollTarget = document.getElementById('foodModalLauncher') || document.getElementById('emailSection');
            if (scrollTarget) {
                setTimeout(function() {
                    scrollTarget.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 120);
            }
        }
    }

    // ============================================================
    // FIXED: window.toggleSeat - Cập nhật UI ngay lập tức
    // ============================================================
    window.toggleSeat = function(seatNumber) {
        if (reservationRequestInFlight || window.seatsConfirmed || !seatNumber) {
            return;
        }

        var seatElement = document.querySelector('.seat[data-seat="' + seatNumber + '"]');
        if (!seatElement) {
            return;
        }

        if (isSeatLockedNow(seatNumber) ||
            seatElement.classList.contains('seat-booked') ||
            seatElement.classList.contains('seat-reserved') ||
            seatElement.classList.contains('seat-disabled')) {
            showSeatRealtimeNotice('Ghế này đã được giữ chỗ bởi người khác.', 'warning');
            return;
        }

        window.selectedSeats = uniqueSeatList(window.selectedSeats || []);

        if (seatElement.classList.contains('seat-selected')) {
            seatElement.classList.remove('seat-selected');
            window.selectedSeats = window.selectedSeats.filter(function(seat) {
                return seat !== seatNumber;
            });
        } else {
            if (window.selectedSeats.length >= 8) {
                showErrorMsg('Chỉ được đặt tối đa 8 ghế!');
                return;
            }

            seatElement.classList.add('seat-selected');
            window.selectedSeats.push(seatNumber);
        }

        updateSeatSummaryNow();
    };

    // ============================================================
    // FIXED: window.confirmSeats - UI update ngay lập tức, KHÔNG chờ API
    // ============================================================
    window.confirmSeats = function() {
        var validation = validateSeatSelectionNow(true);
        if (!validation.valid) {
            return false;
        }

        var showtimeId = getCurrentSeatMapShowtimeId();
        if (!showtimeId) {
            showErrorMsg('Vui lòng chọn suất chiếu trước.');
            return false;
        }

        // === IMMEDIATE UI UPDATE: Cập nhật giao diện NGAY, không chờ API ===
        window.seatsConfirmed = true;

        // Ẩn nút "Xác nhận chọn ghế", hiện nút "Chọn lại ghế"
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'none';
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = confirmSeatHtml;
        }

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) {
            reselectBtn.style.display = 'inline-block';
        }

        // Hiển thị các sections: Đồ ăn, Thanh toán, Email
        var foodLauncher = document.getElementById('foodModalLauncher');
        if (foodLauncher) foodLauncher.style.display = 'block';

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) paymentSection.style.display = 'block';

        var emailSection = document.getElementById('emailSection');
        if (emailSection) emailSection.style.display = 'block';

        var priceInfoBox = document.getElementById('priceInfoBox');
        if (priceInfoBox) priceInfoBox.style.display = 'block';

        // Vô hiệu hóa chọn ghế
        var seats = document.querySelectorAll('.seat');
        for (var i = 0; i < seats.length; i++) {
            if (!seats[i].classList.contains('seat-booked') &&
                !seats[i].classList.contains('seat-reserved') &&
                !seats[i].classList.contains('seat-my-reserved')) {
                seats[i].style.opacity = '0.6';
                seats[i].style.cursor = 'not-allowed';
            }
        }

        updateBookingSummaryFull();

        // Scroll xuống email section
        var emailSectionEl = document.getElementById('emailSection');
        if (emailSectionEl) {
            setTimeout(function() {
                emailSectionEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 120);
        }

        // === ASYNC API CALL: Giữ ghế trên server (không block UI) ===
        var confirmButtonElement = document.getElementById('confirmSeatsBtn');
        if (confirmButtonElement) {
            confirmButtonElement.style.display = 'none'; // Giữ ẩn
        }

        reservationRequestInFlight = true;

        reserveCurrentSelectionNow(showtimeId)
            .then(function(result) {
                if (result.status >= 200 && result.status < 300 && result.data && result.data.success) {
                    // Cập nhật thông tin giữ ghế từ server
                    currentMyReservedSeats = uniqueSeatList(result.data.myReservedSeats || result.data.lockedSeats || window.selectedSeats || []);
                    currentReservedSeats = uniqueSeatList(result.data.reservedSeats || currentMyReservedSeats || []);
                    window.selectedSeats = currentMyReservedSeats.slice();
                    currentBookedSeats = uniqueSeatList(result.data.bookedSeats || []);

                    return true;
                }

                // Nếu API thất bại, vẫn GIỮ NGUYÊN UI đã cập nhật (không revert)
                console.warn('Reservation API returned non-success, but UI already updated:', result.data);
                return true;
            })
            .catch(function(error) {
                console.error('Reserve seats error (non-blocking):', error);
                // KHÔNG revert UI - vẫn giữ nguyên các sections đã hiển thị
                // Chỉ hiển thị cảnh báo nhẹ
                showSeatRealtimeNotice('Không thể giữ ghế trên server. Tuy nhiên, bạn vẫn có thể tiếp tục đặt vé. Nếu ghế đã có người khác đặt, hệ thống sẽ báo lỗi khi thanh toán.', 'warning');
            })
            .finally(function() {
                reservationRequestInFlight = false;

                var btn = document.getElementById('confirmSeatsBtn');
                if (btn && !window.seatsConfirmed) {
                    btn.disabled = false;
                    btn.innerHTML = confirmSeatHtml;
                }
            });

        return false;
    };

    // ============================================================
    // FIXED: window.reselectSeats - Cập nhật UI ngay lập tức
    // ============================================================
    window.reselectSeats = function() {
        var showtimeId = getCurrentSeatMapShowtimeId();
        var seatsToRelease = uniqueSeatList((currentMyReservedSeats && currentMyReservedSeats.length ? currentMyReservedSeats : window.selectedSeats) || []);

        clearSeatReservationTimers();

        // === IMMEDIATE UI UPDATE ===
        window.selectedSeats = [];
        window.seatsConfirmed = false;
        currentReservedSeats = [];
        currentMyReservedSeats = [];
        currentBookedSeats = [];

        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'inline-block';
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = confirmSeatHtml;
        }

        var reselectBtn = document.getElementById('reselectSeatsBtn');
        if (reselectBtn) {
            reselectBtn.style.display = 'none';
        }

        var foodSection = document.getElementById('foodSection');
        if (foodSection) foodSection.style.display = 'none';
        var foodLauncher = document.getElementById('foodModalLauncher');
        if (foodLauncher) foodLauncher.style.display = 'none';

        var paymentSection = document.getElementById('paymentSection');
        if (paymentSection) paymentSection.style.display = 'none';

        updateSeatSummaryNow();

        // === ASYNC API CALL (non-blocking) ===
        if (showtimeId && seatsToRelease.length > 0) {
            releaseCurrentSelectionNow(showtimeId, seatsToRelease)
                .catch(function(error) {
                    console.error('Release seats error (non-blocking):', error);
                })
                .finally(function() {
                    loadSeatMapNow(showtimeId);
                });
        } else {
            loadSeatMapNow(showtimeId || window.selectedShowtimeId);
        }
    };

    // Superseded by the ASCII-safe handler below.
    var unusedConfirmSeatsOverride = function() {
        var validation = validateSeatSelectionNow(true);
        if (!validation.valid) {
            return false;
        }

        var showtimeId = getCurrentSeatMapShowtimeId();
        if (!showtimeId) {
            showErrorMsg('Vui lĂ²ng chọn suất chiếu trước.');
            return false;
        }

        var selectedBeforeReserve = uniqueSeatList(window.selectedSeats || []);
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = holdingSeatHtml;
        }

        setAvailableSeatsInteractive(false);
        reservationRequestInFlight = true;

        reserveCurrentSelectionNow(showtimeId)
            .then(function(result) {
                if (result.status >= 200 && result.status < 300 && result.data && result.data.success) {
                    currentMyReservedSeats = firstNonEmptySeatList(result.data.myReservedSeats, result.data.lockedSeats, selectedBeforeReserve);
                    currentReservedSeats = mergeSeatLists(result.data.reservedSeats || [], currentMyReservedSeats);
                    currentBookedSeats = uniqueSeatList(result.data.bookedSeats || currentBookedSeats || []);
                    window.selectedSeats = currentMyReservedSeats.slice();
                    result.data.myReservedSeats = currentMyReservedSeats.slice();
                    result.data.reservedSeats = currentReservedSeats.slice();

                    renderSeatStateFromPayload(result.data, true);
                    startServerReservationCountdown(result.data);
                    showConfirmedSeatUi(true);
                    showSeatRealtimeNotice('', 'success');
                    return true;
                }

                var responseMessage = result.data && result.data.message
                    ? result.data.message
                    : 'KhĂ´ng thể giữ ghế. Vui lĂ²ng thử lại.';

                console.warn('Reservation API returned non-success:', result.data);
                window.selectedSeats = selectedBeforeReserve.slice();
                showEditableSeatUi(responseMessage, 'warning');
                refreshSeatStatusNow(showtimeId, true);
                return false;
            })
            .catch(function(error) {
                console.error('Reserve seats error:', error);
                window.selectedSeats = selectedBeforeReserve.slice();
                showEditableSeatUi('KhĂ´ng thể giữ ghế trĂªn server. Vui lĂ²ng kiểm tra kết nối vĂ  thử lại.', 'warning');
                refreshSeatStatusNow(showtimeId, true);
            })
            .finally(function() {
                reservationRequestInFlight = false;
                updatePayButtonState();

                var btn = document.getElementById('confirmSeatsBtn');
                if (btn && !window.seatsConfirmed) {
                    resetSeatActionButton();
                }
            });

        return false;
    };

    window.reselectSeats = function() {
        var showtimeId = getCurrentSeatMapShowtimeId();
        var seatsToRelease = uniqueSeatList((currentMyReservedSeats && currentMyReservedSeats.length ? currentMyReservedSeats : window.selectedSeats) || []);

        window.selectedSeats = [];
        window.seatsConfirmed = false;
        currentMyReservedSeats = [];

        showEditableSeatUi('', 'info', true);

        if (showtimeId && seatsToRelease.length > 0) {
            releaseCurrentSelectionNow(showtimeId, seatsToRelease)
                .catch(function(error) {
                    console.error('Release seats error:', error);
                })
                .finally(function() {
                    loadSeatMapNow(showtimeId, {
                        preservePurchaseCountdown: true
                    });
                });
        } else if (showtimeId || window.selectedShowtimeId) {
            loadSeatMapNow(showtimeId || window.selectedShowtimeId, {
                preservePurchaseCountdown: true
            });
        }
    };

    window.confirmSeats = function() {
        if (isSeatActionButtonReselect()) {
            window.reselectSeats();
            return false;
        }

        var validation = validateSeatSelectionNow(true);
        if (!validation.valid) {
            return false;
        }

        var showtimeId = getCurrentSeatMapShowtimeId();
        if (!showtimeId) {
            showErrorMsg('Vui long chon suat chieu truoc.');
            return false;
        }

        var selectedBeforeReserve = uniqueSeatList(window.selectedSeats || []);
        var confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.style.display = 'inline-block';
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = holdingSeatHtml;
            confirmBtn.setAttribute('data-seat-action', 'holding');
        }

        reservationRequestInFlight = true;
        setAvailableSeatsInteractive(false);

        reserveCurrentSelectionNow(showtimeId)
            .then(function(result) {
                if (result.status >= 200 && result.status < 300 && result.data && result.data.success) {
                    currentMyReservedSeats = firstNonEmptySeatList(result.data.myReservedSeats, result.data.lockedSeats, selectedBeforeReserve);
                    currentReservedSeats = mergeSeatLists(result.data.reservedSeats || [], currentMyReservedSeats);
                    currentBookedSeats = uniqueSeatList(result.data.bookedSeats || currentBookedSeats || []);
                    window.selectedSeats = currentMyReservedSeats.slice();
                    result.data.myReservedSeats = currentMyReservedSeats.slice();
                    result.data.reservedSeats = currentReservedSeats.slice();

                    renderSeatStateFromPayload(result.data, true);
                    startServerReservationCountdown(result.data);
                    showConfirmedSeatUi(true);
                    showSeatRealtimeNotice('', 'success');
                    return true;
                }

                var responseMessage = result.data && result.data.message
                    ? result.data.message
                    : 'Khong the giu ghe. Vui long thu lai.';

                console.warn('Reservation API returned non-success:', result.data);
                window.selectedSeats = selectedBeforeReserve.slice();
                showEditableSeatUi(responseMessage, 'warning');
                refreshSeatStatusNow(showtimeId, true);
                return false;
            })
            .catch(function(error) {
                console.error('Reserve seats error:', error);
                window.selectedSeats = selectedBeforeReserve.slice();
                showEditableSeatUi('Khong the giu ghe tren server. Vui long kiem tra ket noi va thu lai.', 'warning');
                refreshSeatStatusNow(showtimeId, true);
            })
            .finally(function() {
                reservationRequestInFlight = false;
                updatePayButtonState();

                var btn = document.getElementById('confirmSeatsBtn');
                if (btn && !window.seatsConfirmed) {
                    resetSeatActionButton();
                }
            });

        return false;
    };

    function renderInitialDateTabs(selectedDate) {
        var dateSection = document.getElementById('dateSelectionSection');
        var datesContainer = document.getElementById('datesContainer');
        if (dateSection) {
            dateSection.style.display = 'block';
        }
        if (!datesContainer || datesContainer.children.length > 0) {
            return;
        }

        var dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        var html = '';
        for (var i = 0; i < 7; i++) {
            var date = new Date();
            date.setDate(date.getDate() + i);
            var dateValue = formatLocalDate(date);
            var selectedClass = selectedDate === dateValue ? ' selected' : '';
            html += '<div class="date-tab' + selectedClass + '" onclick="selectDate(\'' + dateValue + '\')" data-date="' + dateValue + '">';
            html += '<div class="day-name">' + dayNames[date.getDay()] + (i === 0 ? ' (Hom nay)' : '') + '</div>';
            html += '<div class="date-text">' + String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0') + '</div>';
            html += '</div>';
        }

        datesContainer.innerHTML = html;
    }

    function restoreBookingPageState() {
        var initialShowtimeId = bookingPageConfig.selectedShowtimeId ||
            (document.getElementById('showtimeIdInput') ? document.getElementById('showtimeIdInput').value : null);
        var initialTheaterId = bookingPageConfig.selectedTheaterId || window.selectedTheaterId;
        var initialDate = bookingPageConfig.selectedDate || window.selectedDate;

        if (!initialShowtimeId || !initialTheaterId || !initialDate) {
            return;
        }

        window.selectedTheaterId = String(initialTheaterId);
        window.selectedDate = initialDate;
        window.selectedShowtimeId = String(initialShowtimeId);
        currentSeatMapShowtimeId = String(initialShowtimeId);

        var theaterInput = document.getElementById('theaterIdInput');
        if (theaterInput) {
            theaterInput.value = window.selectedTheaterId;
        }

        var showtimeInput = document.getElementById('showtimeIdInput');
        if (showtimeInput) {
            showtimeInput.value = window.selectedShowtimeId;
        }

        document.querySelectorAll('.theater-card').forEach(function(card) {
            card.classList.toggle('selected', card.getAttribute('data-theater-id') === window.selectedTheaterId);
        });

        renderInitialDateTabs(initialDate);

        document.querySelectorAll('.date-tab').forEach(function(tab) {
            tab.classList.toggle('selected', tab.getAttribute('data-date') === initialDate);
        });

        var showtimesSection = document.getElementById('showtimeSelectionSection');
        var showtimesContainer = document.getElementById('showtimesContainer');
        if (showtimesSection) {
            showtimesSection.style.display = 'block';
        }

        if (showtimesContainer && bookingPageRoutes.bookingShowtimes && currentMovieId) {
            showtimesContainer.innerHTML = '<p class="text-center text-muted">Dang tai...</p>';
            var url = bookingPageRoutes.bookingShowtimes + '?movie_id=' + encodeURIComponent(currentMovieId) +
                '&theater_id=' + encodeURIComponent(window.selectedTheaterId) +
                '&date=' + encodeURIComponent(initialDate);

            fetch(url)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    var showtimes = data.showtimes || [];
                    if (!showtimes.length) {
                        showtimesContainer.innerHTML = '<p class="text-center text-warning">Khong co suat chieu nao cho ngay nay</p>';
                        return;
                    }

                    showtimesContainer.innerHTML = showtimes.map(function(showtime) {
                        var selectedClass = String(showtime.id) === String(initialShowtimeId) ? ' selected' : '';
                        return '<div class="showtime-btn' + selectedClass + '" onclick="selectShowtime(' + showtime.id + ')" data-showtime-id="' + showtime.id + '">' +
                            '<div>' + showtime.show_time + '</div>' +
                            '<div class="screen-info">' + (showtime.screen_name || 'N/A') + ' - ' + (showtime.screen_type || '2D') + '</div>' +
                            '</div>';
                    }).join('');
                })
                .catch(function() {
                    showtimesContainer.innerHTML = '<p class="text-center text-danger">Loi khi tai lich chieu</p>';
                });
        }

        updateBookingUrlState(window.selectedShowtimeId);
        loadSeatMapNow(window.selectedShowtimeId, {
            preservePurchaseCountdown: true
        });
    }

    restoreBookingPageState();

    if (!window.__bookingSeatVisibilityHandlerBound) {
        window.__bookingSeatVisibilityHandlerBound = true;

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                refreshSeatStatusNow(currentSeatMapShowtimeId || window.selectedShowtimeId);
            }
        });
    }
})();
