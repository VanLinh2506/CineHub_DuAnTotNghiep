# QR Code Integration for Laravel

## Packages Installed

- **endroid/qr-code**: ^4.8 - Main QR code generation library
- **bacon/bacon-qr-code**: ^2.0 - Backend library (auto-installed as dependency)
- **dasprid/enum**: ^1.0 - Enum support (auto-installed as dependency)

## Files Created

1. **app/Services/QrCodeService.php** - Main QR code service
2. **app/Helpers/QrCodeHelper.php** - Helper functions for easy access

## Usage Examples

### 1. Using Service Class (Recommended)

```php
use App\Services\QrCodeService;

// In controller
public function generateTicket($ticketId)
{
    $qrService = app(QrCodeService::class);
    
    // Generate base64 encoded QR code
    $qrCodeBase64 = $qrService->generateTicketQrCode(
        data: "TICKET-{$ticketId}",
        label: "Ticket #{$ticketId}",
        size: 300
    );
    
    // Use in view: <img src="data:image/png;base64,{{ $qrCodeBase64 }}" />
    return view('ticket.show', compact('qrCodeBase64'));
}
```

### 2. Using Helper Functions

```php
// Simple QR code generation
$qrCode = generate_qr_code('TICKET-12345', 'Ticket #12345');

// Generate as data URI (ready for HTML img tag)
$dataUri = qr_code_data_uri('TICKET-12345');
// Returns: data:image/png;base64,iVBORw0KGgoAAAANS...

// Generate ticket QR code
$ticketQr = generate_ticket_qr(
    ticketId: 12345,
    seatNumber: 'A15',
    verificationCode: 'ABC123XYZ'
);

// Generate booking QR code
$bookingQr = generate_booking_qr(
    bookingId: 789,
    verificationCode: 'BOOK-789-XYZ'
);
```

### 3. In Blade Templates

```blade
{{-- Display QR code using base64 --}}
<img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" />

{{-- Generate QR code directly in blade --}}
<img src="{{ qr_code_data_uri('TICKET-' . $ticket->id) }}" alt="Ticket QR" />

{{-- Using helper in blade --}}
@php
    $qrCode = generate_ticket_qr($ticket->id, $ticket->seat_number, $ticket->verification_code);
@endphp
<img src="data:image/png;base64,{{ $qrCode }}" alt="Ticket QR Code" />
```

### 4. Save QR Code to File

```php
use App\Services\QrCodeService;

$qrService = app(QrCodeService::class);

// Save to storage/app/public/qrcodes/
$filePath = storage_path('app/public/qrcodes/ticket-12345.png');
$qrService->generateAndSave(
    data: 'TICKET-12345',
    filePath: $filePath,
    label: 'Ticket #12345',
    size: 400
);

// Access via URL: /storage/qrcodes/ticket-12345.png
```

### 5. Example: Booking Controller with QR Code

```php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function show($id)
    {
        $booking = Booking::with('tickets')->findOrFail($id);
        $qrService = app(QrCodeService::class);
        
        // Generate QR code for booking
        $bookingQrCode = $qrService->generateBookingQrCode(
            bookingId: $booking->id,
            verificationCode: $booking->verification_code
        );
        
        // Generate QR codes for each ticket
        $tickets = $booking->tickets->map(function($ticket) use ($qrService) {
            $ticket->qr_code = $qrService->generateTicketVerificationQrCode(
                ticketId: $ticket->id,
                seatNumber: $ticket->seat_number,
                verificationCode: $ticket->verification_code
            );
            return $ticket;
        });
        
        return view('booking.show', compact('booking', 'bookingQrCode', 'tickets'));
    }
    
    public function downloadTicket($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $qrService = app(QrCodeService::class);
        
        // Generate QR code and save to temp file
        $tempPath = storage_path('app/temp/ticket-' . $ticketId . '.png');
        $qrService->generateAndSave(
            data: "TICKET-{$ticket->id}-{$ticket->verification_code}",
            filePath: $tempPath,
            label: "Seat: {$ticket->seat_number}",
            size: 400
        );
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
```

### 6. Example: Email with QR Code

```php
// In your Mail class
use App\Services\QrCodeService;

class TicketPurchased extends Mailable
{
    public function build()
    {
        $qrService = app(QrCodeService::class);
        $qrCode = $qrService->generateDataUri("TICKET-{$this->ticket->id}");
        
        return $this->view('emails.ticket-purchased')
                    ->with([
                        'ticket' => $this->ticket,
                        'qrCodeDataUri' => $qrCode
                    ]);
    }
}

// In email blade template
<img src="{{ $qrCodeDataUri }}" alt="Ticket QR Code" style="width: 200px;" />
```

## QR Code Data Format

### Ticket QR Code
Format: `TICKET-{ticket_id}-{seat_number}-{verification_code}`

Example: `TICKET-12345-A15-ABC123XYZ`

### Booking QR Code
Format: `BOOKING-{booking_id}-{verification_code}`

Example: `BOOKING-789-BOOK789XYZ`

## Verification (Scanning QR Code)

```php
// In counter staff controller
public function verifyTicket(Request $request)
{
    $scannedData = $request->qr_data; // e.g., "TICKET-12345-A15-ABC123XYZ"
    
    // Parse QR data
    [$type, $ticketId, $seatNumber, $verificationCode] = explode('-', $scannedData);
    
    if ($type !== 'TICKET') {
        return response()->json(['error' => 'Invalid QR code type'], 400);
    }
    
    // Verify ticket
    $ticket = Ticket::where('id', $ticketId)
        ->where('seat_number', $seatNumber)
        ->where('verification_code', $verificationCode)
        ->first();
    
    if (!$ticket) {
        return response()->json(['error' => 'Invalid ticket'], 404);
    }
    
    if ($ticket->is_picked_up) {
        return response()->json(['error' => 'Ticket already used'], 400);
    }
    
    // Mark as picked up
    $ticket->update(['is_picked_up' => true]);
    
    return response()->json([
        'success' => true,
        'ticket' => $ticket
    ]);
}
```

## Configuration Options

The QR code service supports:

- **Size**: Any pixel size (default: 300px)
- **Encoding**: UTF-8 (supports Vietnamese text)
- **Error Correction**: High level (can recover even if 30% of code is damaged)
- **Margin**: 10px around the code
- **Label**: Optional text below QR code
- **Format**: PNG (best for printing and display)

## Testing

```php
// Test QR code generation
php artisan tinker

use App\Services\QrCodeService;
$qr = app(QrCodeService::class);
$code = $qr->generateTicketQrCode('TEST-12345', 'Test Ticket');
echo strlen($code); // Should return length of base64 string
```

## Notes

- QR codes are generated on-the-fly, no database storage needed
- Use base64 encoding for embedding in HTML/emails
- Save to file only when needed (e.g., PDF generation)
- Verification codes should be unique and secure
- Consider rate limiting on verification endpoints
