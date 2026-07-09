export function calculateDistanceKm(lat1, lng1, lat2, lng2) {
    const rad = Math.PI / 180;
    const dLat = (lat2 - lat1) * rad;
    const dLng = (lng2 - lng1) * rad;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * rad) * Math.cos(lat2 * rad) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);

    return 6371 * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
}

export function formatLocalDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function getSeatGroupsFromRow(rowEl) {
    const groups = [[]];
    const children = rowEl.children;

    for (let i = 0; i < children.length; i++) {
        const child = children[i];
        if (child.classList.contains('seat-space')) {
            if (groups[groups.length - 1].length > 0) {
                groups.push([]);
            }
            continue;
        }

        if (child.classList.contains('seat') && child.dataset.seat && !child.classList.contains('seat-disabled')) {
            groups[groups.length - 1].push(child);
        }
    }

    const result = groups.filter((group) => group.length > 0);
    if (result.length !== 1) {
        return result;
    }

    const seats = result[0].slice().sort((a, b) => {
        return parseInt(a.dataset.seat.substring(1), 10) - parseInt(b.dataset.seat.substring(1), 10);
    });

    const rowName = seats[0].dataset.seat.charAt(0);
    const splitGroups = [[]];

    for (let s = 0; s < seats.length; s++) {
        const col = parseInt(seats[s].dataset.seat.substring(1), 10);
        if (splitGroups[splitGroups.length - 1].length > 0) {
            const prevCol = parseInt(splitGroups[splitGroups.length - 1][splitGroups[splitGroups.length - 1].length - 1].dataset.seat.substring(1), 10);
            if (col - prevCol > 1 || (prevCol <= 6 && col >= 7) || (rowName === 'J' && prevCol <= 3 && col >= 4)) {
                splitGroups.push([]);
            }
        }
        splitGroups[splitGroups.length - 1].push(seats[s]);
    }

    return splitGroups.filter((group) => group.length > 0);
}

export function isSeatOccupied(seatNo, selected, booked) {
    return booked.indexOf(seatNo) !== -1 || selected.indexOf(seatNo) !== -1;
}

export function uniqueSeatList(seats) {
    const seen = {};
    const result = [];

    (seats || []).forEach((seat) => {
        if (!seat || seen[seat]) {
            return;
        }
        seen[seat] = true;
        result.push(seat);
    });

    return result;
}
