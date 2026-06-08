@extends('layouts.app')

@php
    $title = 'In vé';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $title }}</h2>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> In
        </button>
    </div>

    @if (!empty($tickets))
        <div class="row">
            @foreach ($tickets as $ticket)
                <div class="col-md-6 mb-4">
                    <div class="ticket-print" id="ticket-{{ $ticket['id'] }}">
                        <!-- Ticket Front -->
                        <div style="border: 2px solid #333; padding: 20px; background: white; margin-bottom: 20px;">
                            <div class="text-center mb-3">
                                <h4>{{ $ticket['movie_title'] }}</h4>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                <div>
                                    <small class="text-muted">Ngày chiếu:</small>
                                    <p><strong>{{ \Carbon\Carbon::parse($ticket['show_date'])->format('d/m/Y') }}</strong></p>
                                </div>
                                <div>
                                    <small class="text-muted">Giờ chiếu:</small>
                                    <p><strong>{{ \Carbon\Carbon::parse($ticket['show_time'])->format('H:i') }}</strong></p>
                                </div>
                                <div>
                                    <small class="text-muted">Phòng:</small>
                                    <p><strong>{{ $ticket['screen_name'] }}</strong></p>
                                </div>
                                <div>
                                    <small class="text-muted">Ghế:</small>
                                    <p><strong>{{ $ticket['seat'] }}</strong></p>
                                </div>
                            </div>

                            <div style="border-top: 1px solid #ddd; padding-top: 15px;">
                                <small class="text-muted">Mã vé:</small>
                                <p style="font-family: monospace; font-size: 14px;"><strong>{{ $ticket['ticket_code'] }}</strong></p>
                            </div>

                            <!-- QR Code -->
                            <div class="text-center mt-3">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Crect fill='white' width='120' height='120'/%3E%3C/svg%3E" 
                                     alt="QR Code" style="width: 120px; height: 120px;">
                                <p class="small text-muted mt-2">Quét mã này tại rạp</p>
                            </div>

                            <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px; text-align: center;">
                                <small class="text-muted">Rạp: {{ $ticket['theater_name'] }}</small>
                                <p class="small text-muted">{{ $ticket['theater_location'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4 no-print">
            <button type="button" class="btn btn-primary me-2" onclick="window.print()">
                <i class="fas fa-print"></i> In vé
            </button>
            <a href="{{ url('/?route=home') }}" class="btn btn-secondary">
                <i class="fas fa-home"></i> Quay lại
            </a>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Không có vé để in
        </div>
    @endif
</div>

<style media="print">
    .no-print {
        display: none;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
    
    .container {
        max-width: 100%;
    }
</style>

<style>
    .ticket-print {
        page-break-inside: avoid;
    }

    @media print {
        .ticket-print {
            page-break-after: always;
        }
    }
</style>
@endsection
