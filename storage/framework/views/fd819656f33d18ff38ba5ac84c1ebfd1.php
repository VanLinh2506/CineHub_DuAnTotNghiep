<?php $__env->startPush('scripts'); ?>
<script>
// ============== DECLARE ALL FUNCTIONS FIRST ==============
// Global variables
var userLat = null;
var userLng = null;
var currentMovieId = <?php echo e(isset($movie) && $movie->id ? $movie->id : 'null'); ?>;
var selectedTheaterId = null;
var selectedDate = null;
var selectedShowtimeId = null;

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
    var url = '<?php echo e(route("api.booking.showtimes")); ?>?movie_id=' + currentMovieId + 
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
    
    var showtimeInput = document.getElementById('showtimeIdInput');
    console.log('Found showtimeIdInput element:', !!showtimeInput);
    
    if (showtimeInput) {
        showtimeInput.value = showtimeId;
        console.log('Set showtimeIdInput.value =', showtimeInput.value);
    } else {
        console.error('showtimeIdInput element not found!');
    }
    
    // Load seat map
    loadSeatMapNow(showtimeId);
};

// Declare loadSeatMap function
function loadSeatMapNow(showtimeId) {
    var seatMapContainer = document.getElementById('seatMap');
    var seatSelectionSection = document.getElementById('seatSelectionSection');
    
    // Show seat selection section
    seatSelectionSection.style.display = 'block';
    seatMapContainer.innerHTML = '<p class="text-center text-muted">Đang tải sơ đồ ghế...</p>';
    
    // Fetch seat map data from API
    var url = '<?php echo e(route("api.booking.seatMap")); ?>?showtime_id=' + showtimeId;
    
    fetch(url)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            console.log('Seat map data received:', data);
            
            // Display screen name
            if (data.screen && data.screen.name) {
                var screenDisplay = document.getElementById('screenNameDisplay');
                if (screenDisplay) {
                    screenDisplay.textContent = '(' + data.screen.name + ' - ' + (data.screen.type || '2D') + ')';
                }
            }
            
            // Render seat map
            if (data.layout) {
                console.log('Using custom layout');
                renderSeatMapFull(data.layout, data.bookedSeats || [], data.prices || {});
            } else {
                console.log('Using default layout');
                generateDefaultSeatLayoutNow(data.bookedSeats || []);
            }
        })
        .catch(function(error) {
            console.error('Error loading seat map:', error);
            generateDefaultSeatLayoutNow([]);
        });
}

// Generate default seat layout
function generateDefaultSeatLayoutNow(bookedSeats) {
    bookedSeats = bookedSeats || [];
    
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
function renderSeatMapFull(layout, bookedSeats, prices) {
    bookedSeats = bookedSeats || [];
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
            
            if (seat.type === 'vip') seatClass += ' seat-vip';
            if (seat.type === 'couple') seatClass += ' seat-couple';
            if (seat.type === 'disabled' || !seat.available) seatClass += ' seat-disabled';
            if (bookedSeats.indexOf(seat.number) !== -1) seatClass += ' seat-booked';
            
            var onclick = (seat.type !== 'disabled' && seat.available && bookedSeats.indexOf(seat.number) === -1)
                ? 'onclick="toggleSeat(\'' + seat.number + '\')"'
                : '';
            
            html += '<div class="' + seatClass + '" data-seat="' + seat.number + '" ' + onclick + '>';
            html += (seat.label || '');
            html += '</div>';
        }
        
        html += '</div>';
    }
    
    seatMapContainer.innerHTML = html;
}

// Declare toggleSeat function
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
        window.selectedSeats = window.selectedSeats.filter(function(s) { return s !== seatNumber; });
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

// Validate seat selection
function validateSeatSelectionNow(showAlert) {
    if (!window.selectedSeats || window.selectedSeats.length === 0) {
        if (showAlert) showErrorMsg('Vui lòng chọn ít nhất 1 ghế!');
        return { valid: false, message: 'Vui lòng chọn ít nhất 1 ghế!' };
    }
    
    if (window.selectedSeats.length > 8) {
        if (showAlert) showErrorMsg('Chỉ được đặt tối đa 8 ghế!');
        return { valid: false, message: 'Chỉ được đặt tối đa 8 ghế!' };
    }
    
    // Check for gaps (seats must be adjacent in same row)
    var seatsByRow = {};
    for (var i = 0; i < window.selectedSeats.length; i++) {
        var seat = window.selectedSeats[i];
        var row = seat.charAt(0);
        var col = parseInt(seat.substring(1));
        if (!seatsByRow[row]) seatsByRow[row] = [];
        seatsByRow[row].push(col);
    }
    
    for (var row in seatsByRow) {
        var cols = seatsByRow[row].sort(function(a, b) { return a - b; });
        
        // Check if there are gaps between selected seats
        for (var i = 0; i < cols.length - 1; i++) {
            var gap = cols[i + 1] - cols[i];
            if (gap > 1) {
                var message = 'Ghế trong hàng ' + row + ' phải liền kề nhau!\nKhông được bỏ trống ghế ở giữa (ghế ' + row + cols[i] + ' và ' + row + cols[i+1] + ' cách nhau ' + (gap-1) + ' ghế)';
                if (showAlert) showErrorMsg(message);
                return { valid: false, message: message };
            }
        }
    }
    
    return { valid: true, message: '' };
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
    var basePrice = <?php echo e(isset($basePrice) ? $basePrice : 90000); ?>;
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
    var seatBreakdown = { normal: 0, vip: 0, couple: 0 };
    
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

// Confirm seats
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
    var foodSection = document.getElementById('foodSection');
    if (foodSection) foodSection.style.display = 'block';
    
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
    
    return true;
};

// Reselect seats
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
    
    var paymentSection = document.getElementById('paymentSection');
    if (paymentSection) paymentSection.style.display = 'none';
    
    // Reset food quantities
    var foodInputs = document.querySelectorAll('input[name^="food_items["]');
    for (var i = 0; i < foodInputs.length; i++) {
        foodInputs[i].value = 0;
    }
    
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

// Update food quantity
window.updateFoodQuantity = function(foodId, change) {
    console.log('updateFoodQuantity called:', {foodId, change});
    var input = document.getElementById('food_' + foodId);
    if (!input) {
        console.error('Food input not found for ID:', foodId);
        return;
    }
    
    var currentValue = parseInt(input.value) || 0;
    var newValue = currentValue + change;
    
    if (newValue < 0) newValue = 0;
    if (newValue > 10) newValue = 10;
    
    console.log('Updating food quantity:', {foodId, currentValue, newValue});
    input.value = newValue;
    
    // Update total price immediately
    updateBookingSummaryFull();
};

// Also make it available without window prefix
var updateFoodQuantity = window.updateFoodQuantity;

// Update booking summary (full)
function updateBookingSummaryFull() {
    console.log('updateBookingSummaryFull called, seatsConfirmed:', window.seatsConfirmed);
    
    if (!window.seatsConfirmed) {
        calculateSeatPriceNow();
        return;
    }
    
    calculateSeatPriceNow();
    
    var basePrice = <?php echo e(isset($basePrice) ? $basePrice : 90000); ?>;
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
                
                console.log('Food item:', {foodId, foodName, qty, price, subtotal});
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
    
    var bookBtn = document.getElementById('bookBtn');
    if (bookBtn) bookBtn.disabled = false;
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
        var seatsText = document.getElementById('selectedSeatsText');
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
    console.log('Rendering seat map...', {layout, bookedSeats, prices});
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
            
            var dateStr = date.toISOString().split('T')[0];
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
    
    console.log('✅ Form validation passed! Submitting...');
    console.log('Final form data:');
    console.log('- showtime_id:', showtimeId);
    console.log('- seats:', Array.from(seats).map(s => s.value));
    console.log('- email:', email);
    
    return true;
}
</script>
<?php $__env->stopPush(); ?>

<?php
    $title = 'Đặt Vé Xem Phim';
    $meta_description = isset($movie) ? 'Đặt vé xem phim ' . $movie->title . ' tại CineHub. Chọn rạp, ngày, giờ và ghế ngồi phù hợp cho bạn.' : 'Đặt vé xem phim tại CineHub.';
    $meta_keywords = 'đặt vé xem phim, vé xem phim online, mua vé xem phim, CineHub';
    $meta_og_title = $title . ' - CineHub';
    $meta_og_description = $meta_description;
?>

<?php $__env->startSection('content'); ?>
<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                <?php if(isset($movie)): ?>
                    <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie" style="position: sticky; top: 20px;">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            <?php if($movie->thumbnail): ?>
                                <img id="img-moviee" 
                                     src="<?php echo e($movie->thumbnail); ?>" 
                                     alt="<?php echo e($movie->title); ?>" 
                                     class="img-fluid rounded"
                                     itemprop="image"
                                     style="max-height: 500px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="poster-placeholder">
                                    <i class="fas fa-film fa-5x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="background_film_blur">
                            <img src="<?php echo e($movie->thumbnail ?? ''); ?>" alt="">
                        </div>
                        
                        <!-- Movie Title -->
                        <h1 class="booking-movie-title" itemprop="name"><?php echo e($movie->title); ?></h1>
                        
                        <!-- Movie Details -->
                        <div class="booking-movie-details">
                            <?php if($movie->rating): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Đánh giá:</span>
                                    <span class="detail-value">
                                        <i class="fas fa-star"></i>
                                        <?php echo e(number_format($movie->rating, 1)); ?>/10
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($movie->duration): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Thời lượng:</span>
                                    <span class="detail-value"><?php echo e(floor($movie->duration / 60)); ?>h <?php echo e($movie->duration % 60); ?>m</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($movie->category): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Thể loại:</span>
                                    <span class="detail-value"><?php echo e($movie->category->name); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($movie->country): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Quốc gia:</span>
                                    <span class="detail-value"><?php echo e($movie->country); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Movie Description -->
                        <?php if($movie->description): ?>
                            <div class="booking-movie-description">
                                <h3>Mô tả</h3>
                                <p itemprop="description"><?php echo e($movie->description); ?></p>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-film"></i>
                        Vui lòng chọn một bộ phim để đặt vé
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container" style="position: sticky; top: 20px; z-index: 10; max-height: calc(100vh - 40px); overflow-y: auto;">
                    <h2 class="booking-form-title">
                        <?php if(!isset($movie)): ?>
                            Đặt vé xem phim
                        <?php else: ?>
                            Chọn Lịch Chiếu & Ghế
                        <?php endif; ?>
                    </h2>
                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(!isset($movie) && isset($allMovies)): ?>
                        <!-- Movies List - Display when no movie selected -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-film me-2"></i>Danh sách phim đang chiếu
                            </label>
                            <?php if(count($allMovies) == 0): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            <?php else: ?>
                                <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                                    <?php $__currentLoopData = $allMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('booking.index', ['movie' => $m->id])); ?>"
                                            class="movie-card-booking"
                                            style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 24px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#e50914'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                                            onmouseout="this.style.borderColor='#ddd'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <?php if($m->thumbnail): ?>
                                                <img src="<?php echo e($m->thumbnail); ?>"
                                                    alt="<?php echo e($m->title); ?>"
                                                    style="width: 100%; height: 200px; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-film" style="font-size: 48px; color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div style="padding: 10px;">
                                                <h4 style="margin: 0; font-size: 14px; color: #333; font-weight: bold; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo e($m->title); ?>

                                                </h4>
                                                <?php if($m->rating): ?>
                                                    <div style="text-align: center; margin-top: 5px;">
                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                        <span style="font-size: 12px; color: #666;"><?php echo e(number_format($m->rating, 1)); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                    
                    <form id="bookingForm" method="POST" action="<?php echo e(route('booking.processBooking')); ?>" class="booking-form" onsubmit="return validateFormBeforeSubmit()">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="showtime_id" id="showtimeIdInput" value="">
                        <div id="seatsInputContainer" style="display: none;"></div>
                        
                        <!-- Theater Selection as Cards -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    <i class="fas fa-building me-2"></i>Chọn rạp cho phim này
                                </label>
                                <div id="userLocationBadge" style="display: none; font-size: 12px; padding: 6px 12px; background: rgba(40, 167, 69, 0.1); border-radius: 20px; color: #28a745;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="userLocationText">Đang lấy vị trí...</span>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="requestUserLocation()" title="Lấy lại vị trí" style="font-size: 12px;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="theater_id" id="theaterIdInput" required>
                            
                            <!-- Test button for debugging -->
                            <button type="button" onclick="alert('Button works! Theater cards: ' + document.querySelectorAll('.theater-card').length)" style="margin-bottom: 10px; padding: 8px 16px; background: #e50914; color: white; border: none; border-radius: 4px;">
                                🔍 Test Click (Debug)
                            </button>
                            
                            <?php if(isset($theaters) && count($theaters) > 0): ?>
                                <div id="theatersContainer" class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                    <?php $__currentLoopData = $theaters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theater): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="theater-card" 
                                             data-theater-id="<?php echo e($theater->id); ?>"
                                             data-lat="<?php echo e($theater->latitude ?? ''); ?>" 
                                             data-lng="<?php echo e($theater->longitude ?? ''); ?>"
                                             data-location="<?php echo e($theater->location ?? ''); ?>"
                                             onclick="window.selectTheaterDirect(<?php echo e($theater->id); ?>)"
                                             style="border: 2px solid #ddd; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s; background: white; position: relative; z-index: 1;">
                                            
                                            <div class="d-flex align-items-start" style="pointer-events: none;">
                                                <div class="theater-icon" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                                    <i class="fas fa-film" style="color: white; font-size: 18px;"></i>
                                                </div>
                                                
                                                <div style="flex: 1;">
                                                    <h5 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #333;">
                                                        <?php echo e($theater->name); ?>

                                                    </h5>
                                                    
                                                    <?php if($theater->location): ?>
                                                        <p style="margin: 0; font-size: 13px; color: #666;">
                                                            <i class="fas fa-map-marker-alt" style="color: #e50914;"></i>
                                                            <?php echo e($theater->location); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($theater->address): ?>
                                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">
                                                            <?php echo e($theater->address); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <div class="theater-distance" data-theater-id="<?php echo e($theater->id); ?>" style="margin-top: 8px; font-size: 12px; color: #28a745; display: none;">
                                                        <i class="fas fa-route"></i>
                                                        <span class="distance-text"></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="theater-check" style="display: none; position: absolute; top: 10px; right: 10px; width: 24px; height: 24px; background: #28a745; border-radius: 50%; color: white;">
                                                    <i class="fas fa-check" style="font-size: 12px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có rạp nào chiếu phim này.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <style>
                            .theater-card:hover {
                                border-color: #e50914 !important;
                                box-shadow: 0 4px 12px rgba(229, 9, 20, 0.2);
                                transform: translateY(-2px);
                            }
                            
                            .theater-card.selected {
                                border-color: #28a745 !important;
                                background: rgba(40, 167, 69, 0.05) !important;
                            }
                            
                            .theater-card.selected .theater-check {
                                display: flex !important;
                            }
                            
                            .dates-tabs .date-tab {
                                min-width: 90px;
                                padding: 12px 16px;
                                border: 2px solid #ddd;
                                border-radius: 8px;
                                background: #2a2a2a;
                                color: #fff;
                                text-align: center;
                                cursor: pointer;
                                transition: all 0.3s;
                                flex-shrink: 0;
                            }
                            
                            .dates-tabs .date-tab:hover {
                                border-color: #e50914;
                                transform: translateY(-2px);
                            }
                            
                            .dates-tabs .date-tab.selected {
                                border-color: #28a745;
                                background: rgba(40, 167, 69, 0.2);
                            }
                            
                            .dates-tabs .date-tab .day-name {
                                font-size: 11px;
                                color: #999;
                                text-transform: uppercase;
                            }
                            
                            .dates-tabs .date-tab .date-text {
                                font-size: 16px;
                                font-weight: bold;
                                margin-top: 4px;
                            }
                            
                            .showtimes-grid .showtime-btn {
                                padding: 12px 16px;
                                border: 2px solid #ddd;
                                border-radius: 8px;
                                background: #2a2a2a;
                                color: #fff;
                                text-align: center;
                                cursor: pointer;
                                transition: all 0.3s;
                                font-size: 14px;
                                font-weight: 600;
                            }
                            
                            .showtimes-grid .showtime-btn:hover {
                                border-color: #e50914;
                                transform: scale(1.05);
                            }
                            
                            .showtimes-grid .showtime-btn.selected {
                                border-color: #28a745;
                                background: rgba(40, 167, 69, 0.2);
                            }
                            
                            .showtimes-grid .showtime-btn .screen-info {
                                font-size: 11px;
                                color: #999;
                                margin-top: 4px;
                            }
                        </style>
                        
                        <!-- Date Selection (hiển thị sau khi chọn rạp) -->
                        <div id="dateSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chọn ngày xem
                            </label>
                            <div id="datesContainer" class="dates-tabs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                                <!-- Dates will be loaded via JavaScript when theater is selected -->
                            </div>
                        </div>
                        
                        <!-- Showtime Selection (appears after date selection) -->
                        <div id="showtimeSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-clock me-2"></i>Chọn khung giờ chiếu
                            </label>
                            <div id="showtimesContainer" class="showtimes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;">
                                <!-- Showtimes will be loaded via JavaScript -->
                            </div>
                        </div>
                        
                        <!-- Seat Selection -->
                        <div id="seatSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-couch me-2"></i>Chọn Ghế
                                <span id="screenNameDisplay" style="margin-left: 10px; color: #ffc107; font-weight: bold;"></span>
                            </label>
                            
                            <!-- Screen indicator -->
                            <div class="screen-indicator" style="margin: 20px 0; text-align: center;">
                                <div style="width: 80%; height: 4px; background: linear-gradient(to bottom, #fff, #666); margin: 0 auto; border-radius: 50%; box-shadow: 0 3px 10px rgba(255,255,255,0.4);"></div>
                                <p style="color: #999; margin-top: 10px; font-size: 12px;">Màn hình</p>
                            </div>
                            
                            <!-- Seat map container -->
                            <div id="seatMap" class="seat-map-container" style="padding: 20px; background: #2a2a2a; border-radius: 8px; max-width: 600px; margin: 0 auto;">
                                <p class="text-center text-muted">Vui lòng chọn khung giờ chiếu</p>
                            </div>
                            
                            <!-- Seat legend -->
                            <div class="seat-legend" style="display: flex; justify-content: center; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #4a4a4a; border: 1px solid #666; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Trống</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #28a745; border: 1px solid #28a745; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đang chọn</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #dc3545; border: 1px solid #dc3545; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đã đặt</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 1px solid #764ba2; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">VIP</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 40px; height: 24px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: 1px solid #f5576c; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đôi</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email for ticket -->
                        <div id="emailSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email nhận vé
                            </label>
                            <input type="email" 
                                   name="customer_email" 
                                   id="customerEmail" 
                                   class="form-control" 
                                   placeholder="email@example.com"
                                   value="<?php echo e(Auth::check() ? Auth::user()->email : ''); ?>"
                                   required>
                            <small class="text-muted" style="font-size: 11px; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Vé điện tử sẽ được gửi đến email này
                            </small>
                        </div>
                        
                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Ghế đã chọn:</strong>
                            <span id="seatsText"></span>
                        </div>
                        
                        <!-- Confirm Seats Button -->
                        <div class="confirm-seats-section" style="margin: 15px 0;">
                            <button type="button" id="confirmSeatsBtn" onclick="confirmSeats()" disabled class="btn-confirm-seats" style="width: 100%; padding: 12px; background: #ffc107; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-check-circle"></i> Xác nhận chọn ghế
                            </button>
                            <button type="button" id="reselectSeatsBtn" onclick="reselectSeats()" style="display: none; width: 100%; padding: 12px; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-redo"></i> Chọn lại ghế
                            </button>
                        </div>
                        
                        <style>
                            .btn-confirm-seats:hover:not(:disabled) {
                                background: #ffca2c;
                                transform: translateY(-2px);
                                box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
                            }
                            .btn-confirm-seats:disabled {
                                opacity: 0.5;
                                cursor: not-allowed;
                            }
                            #reselectSeatsBtn:hover {
                                background: #5a6268;
                            }
                        </style>
                        
                        <!-- Price Info Box -->
                        <div id="priceInfoBox" class="price-info-box" style="display: none; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <h6 style="color: #ffc107; margin-bottom: 10px;">
                                <i class="fas fa-info-circle"></i> Thông tin giá vé
                            </h6>
                            <div style="font-size: 13px; color: #ccc;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-couch" style="color: #999;"></i> Ghế thường:</span>
                                    <span id="normalPriceDisplay">150.000đ</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-crown" style="color: #764ba2;"></i> Ghế VIP (+30%):</span>
                                    <span id="vipPriceDisplay">186.000đ</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span><i class="fas fa-heart" style="color: #f5576c;"></i> Ghế đôi (+50%/ghế):</span>
                                    <span id="couplePriceDisplay">210.000đ</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Food Items Section - Grid cards -->
                        <div id="foodSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-utensils me-2"></i>Combo Đồ Ăn & Nước (Tùy chọn)
                            </label>
                            <div id="foodItemsContainer" class="food-items-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
                                <?php
                                    $hasFoodItems = isset($foodItems) && count($foodItems) > 0;
                                ?>
                                
                                <?php if($hasFoodItems): ?>
                                    <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="food-item-card-compact" data-food-id="<?php echo e($food->id); ?>" data-food-price="<?php echo e($food->price); ?>" style="border: 2px solid #444; border-radius: 10px; padding: 10px; background: #2a2a2a; text-align: center; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='#ffc107'" onmouseout="this.style.borderColor='#444'">
                                            <?php if($food->image): ?>
                                                <img src="<?php echo e(storage_url($food->image)); ?>" alt="<?php echo e($food->name); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #444; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                                    <i class="fas fa-utensils" style="color: #666; font-size: 20px;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <h6 style="margin: 0 0 5px 0; color: #fff; font-size: 12px; font-weight: 600; min-height: 32px; display: flex; align-items: center; justify-content: center;"><?php echo e($food->name); ?></h6>
                                            <p style="margin: 0 0 8px 0; color: #ffc107; font-weight: bold; font-size: 13px;"><?php echo e(number_format($food->price)); ?>đ</p>
                                            <div class="quantity-control" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity(<?php echo e($food->id); ?>, -1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">−</button>
                                                <input type="number" name="food_items[<?php echo e($food->id); ?>]" id="food_<?php echo e($food->id); ?>" value="0" min="0" max="10" readonly style="width: 40px; height: 26px; text-align: center; background: #1a1a1a; border: 1px solid #666; color: #fff; border-radius: 4px; font-size: 14px; font-weight: bold; padding: 0;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity(<?php echo e($food->id); ?>, 1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">+</button>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <p class="text-muted" style="text-align: center; grid-column: 1 / -1;">Không có combo đồ ăn nào</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <style>
                            .btn-quantity-compact:hover {
                                background: #4a4a4a !important;
                                transform: scale(1.05);
                            }
                            .food-item-card-compact:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
                            }
                        </style>
                        
                        <!-- Payment Method Selection -->
                        <div id="paymentSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                            </label>
                            <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <input type="radio" name="payment_method" value="vnpay" checked style="position: absolute; opacity: 0;">
                                    <i class="fas fa-credit-card" style="color: #1e88e5; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">VNPay</div>
                                        <small style="color: #999; font-size: 11px;">Thẻ/QR</small>
                                    </div>
                                </label>
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <input type="radio" name="payment_method" value="wallet" style="position: absolute; opacity: 0;">
                                    <i class="fas fa-wallet" style="color: #28a745; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">Ví CineHub</div>
                                        <small style="color: #999; font-size: 11px;" id="walletBalance"><?php echo e(Auth::check() ? number_format(Auth::user()->points ?? 0) : 0); ?>đ</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <style>
                            .payment-method-card:has(input:checked) {
                                border-color: #ffc107 !important;
                                background: rgba(255, 193, 7, 0.1) !important;
                            }
                            .btn-quantity:hover {
                                background: #4a4a4a !important;
                            }
                        </style>
                        
                        <!-- Price Summary -->
                        <div class="price-summary">
                            <div class="price-row">
                                <span>Giá vé (1 vé):</span>
                                <span id="unitPrice">0 ₫</span>
                            </div>
                            <div class="price-row">
                                <span>Số lượng ghế:</span>
                                <span id="quantity">0</span>
                            </div>
                            <div class="price-row">
                                <span>Tiền vé:</span>
                                <span id="seatsTotal">0 ₫</span>
                            </div>
                            <div id="foodSummaryRows" style="border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 8px; margin-top: 8px; display: none;">
                                <!-- Food items will be added here dynamically -->
                            </div>
                            <div class="price-row total" style="border-top: 2px solid rgba(229, 9, 20, 0.5); margin-top: 8px; padding-top: 8px; font-size: 18px;">
                                <span style="font-weight: bold;">Tổng thanh toán:</span>
                                <span id="totalPrice" style="font-weight: bold; color: #ffc107;">0 ₫</span>
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept_terms" required>
                                <span>Tôi đồng ý với điều khoản và chính sách</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn-book" id="bookBtn" disabled>
                            <i class="fas fa-credit-card"></i>
                            Tiếp tục thanh toán
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .booking-page-section {
        padding: 2rem 0;
        min-height: 100vh;
    }
    
    .booking-movie-info {
        position: relative;
        z-index: 2;
    }
    
    .booking-form-container {
        position: relative;
        z-index: 10;
    }
    
    /* Custom scrollbar for booking form */
    .booking-form-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .booking-form-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
    }
    
    .booking-form-container::-webkit-scrollbar-thumb {
        background: rgba(229, 9, 20, 0.6);
        border-radius: 4px;
    }
    
    .booking-form-container::-webkit-scrollbar-thumb:hover {
        background: rgba(229, 9, 20, 0.8);
    }
    
    .background_film_blur {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        filter: blur(15px);
        opacity: 0.2;
        z-index: -1;
    }
    
    .movie-poster-large {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .movie-poster-large img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .poster-placeholder {
        background: #2a2a2a;
        padding: 3rem;
        text-align: center;
        color: #666;
    }
    
    .booking-movie-title {
        font-size: 2rem;
        color: #fff;
        margin: 1rem 0;
    }
    
    .booking-movie-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .detail-label {
        font-size: 0.8rem;
        color: #999;
    }
    
    .detail-value {
        color: #fff;
        font-weight: 500;
    }
    
    .booking-movie-description {
        margin-top: 1.5rem;
    }
    
    .booking-movie-description h3 {
        color: #fff;
        font-size: 1.1rem;
        margin: 0 0 0.5rem 0;
    }
    
    .booking-movie-description p {
        color: #ccc;
        line-height: 1.6;
        margin: 0;
    }
    
    .booking-form-container {
        background: #1a1a1a;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .booking-form-title {
        color: #fff;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        color: #fff;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #2a2a2a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        color: #fff;
        font-size: 0.95rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #e50914;
        box-shadow: 0 0 10px rgba(229, 9, 20, 0.3);
    }
    
    .seat-map {
        background: #2a2a2a;
        padding: 1rem;
        border-radius: 6px;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .seat-map-container {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .seat-row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 8px 0;
        gap: 8px;
    }
    
    .seat-row-label {
        width: 30px;
        text-align: center;
        color: #999;
        font-weight: bold;
        font-size: 14px;
    }
    
    .seat {
        width: 32px;
        height: 32px;
        background: #4a4a4a;
        border: 2px solid #666;
        border-radius: 6px 6px 2px 2px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        color: #fff;
        position: relative;
    }
    
    .seat:hover:not(.seat-booked):not(.seat-disabled) {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }
    
    .seat.seat-selected {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
        color: white !important;
        box-shadow: none !important;
        transform: scale(1.05);
    }
    
    .seat.seat-selected.seat-vip {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
    }
    
    .seat.seat-selected.seat-couple {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
    }
    
    .seat.seat-booked {
        background: #dc3545;
        border-color: #dc3545;
        cursor: not-allowed;
        color: white;
    }
    
    .seat.seat-vip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #764ba2;
    }
    
    .seat.seat-couple {
        width: 56px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-color: #f5576c;
    }
    
    .seat.seat-disabled {
        background: transparent;
        border: none;
        cursor: default;
    }
    
    .seat-space {
        width: 32px;
        height: 32px;
    }
    
    .selected-seats-display {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        padding: 1rem;
        border-radius: 6px;
        color: #fff;
        margin-bottom: 1rem;
    }
    
    .price-summary {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        color: #ccc;
        margin-bottom: 0.5rem;
    }
    
    .price-row.total {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 0.5rem;
        color: #fff;
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ccc;
        cursor: pointer;
    }
    
    .checkbox-label input[type="checkbox"] {
        cursor: pointer;
    }
    
    .btn-book {
        width: 100%;
        padding: 1rem;
        background: #e50914;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-book:hover:not(:disabled) {
        background: #ff1f1f;
    }
    
    .btn-book:disabled {
        background: #666;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
    }
    
    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #ff7b8f;
    }
    
    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #85ff9f;
    }
    
    .alert-warning {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        color: #ffd649;
    }
    
    @media (max-width: 768px) {
        .booking-form-container {
            padding: 1rem;
        }
        
        .booking-movie-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/booking/index.blade.php ENDPATH**/ ?>