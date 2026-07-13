@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Tạo hợp đồng rạp</h2>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.contracts.store') }}" class="stat-card" enctype="multipart/form-data">
        @csrf
        @include('admin.contracts.form', [
            'submitLabel' => 'Tạo hợp đồng và sinh PDF',
            'actionIcon' => 'fa-file-signature',
        ])
    </form>
</div>
@endsection
