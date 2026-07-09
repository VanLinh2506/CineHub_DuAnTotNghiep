@extends('layouts.app')

@php
    $title = 'Kho phim của tôi';
    $meta_description = 'Chọn kho phim phù hợp với bạn tại CineHub.';
@endphp

@section('content')
<section class="library-choice-page">
    <div class="library-choice-inner">
        <div class="library-choice-heading">
            <p>Kho phim của tôi</p>
            <h1>Chọn gu xem phim của bạn</h1>
        </div>

        <div class="library-choice-grid">
            @foreach($libraryGroups as $group)
            <a href="{{ route('movies.library', $group['key']) }}" class="library-choice-card">
                <img src="{{ $group['image'] }}" alt="{{ $group['title'] }}" class="library-choice-image">
                <span class="library-choice-shade"></span>
                <span class="library-choice-content">
                    <span class="library-choice-title">{{ $group['title'] }}</span>
                    <span class="library-choice-desc">{{ $group['description'] }}</span>
                    <span class="library-choice-action">
                        Xem phim <i class="fas fa-arrow-right"></i>
                    </span>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<style>
    .library-choice-page {
        min-height: calc(100vh - 90px);
        padding: 70px 20px;
        background:
            radial-gradient(circle at 18% 18%, rgba(229, 9, 20, 0.22), transparent 30%),
            linear-gradient(180deg, #141414 0%, #090909 100%);
        color: #fff;
    }

    .library-choice-inner {
        max-width: 1040px;
        margin: 0 auto;
    }

    .library-choice-heading {
        margin-bottom: 28px;
    }

    .library-choice-heading p {
        margin: 0 0 8px;
        color: #ffc107;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
    }

    .library-choice-heading h1 {
        margin: 0;
        font-size: 34px;
        font-weight: 800;
    }

    .library-choice-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .library-choice-card {
        position: relative;
        min-height: 310px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        background: #101010;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.28);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .library-choice-card:hover {
        transform: translateY(-4px);
        border-color: rgba(229, 9, 20, 0.9);
        box-shadow: 0 24px 44px rgba(0, 0, 0, 0.36);
        color: #fff;
    }

    .library-choice-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .library-choice-card:hover .library-choice-image {
        transform: scale(1.04);
    }

    .library-choice-shade {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, rgba(0, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0.34) 42%, rgba(0, 0, 0, 0.9) 100%),
            linear-gradient(90deg, rgba(0, 0, 0, 0.55), transparent 72%);
    }

    .library-choice-content {
        position: relative;
        z-index: 1;
        min-height: 310px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 10px;
        padding: 24px;
    }

    .library-choice-title {
        font-size: 22px;
        font-weight: 800;
    }

    .library-choice-desc {
        flex: 1;
        color: #cfcfcf;
        line-height: 1.55;
    }

    .library-choice-action {
        color: #ffc107;
        font-weight: 700;
    }

    @media (max-width: 780px) {
        .library-choice-page {
            padding: 42px 16px 92px;
        }

        .library-choice-heading h1 {
            font-size: 26px;
        }

        .library-choice-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
