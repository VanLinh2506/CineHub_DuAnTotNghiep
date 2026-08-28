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

    .library-frequent-section { margin:0 0 32px; padding:22px; overflow:hidden; border:1px solid rgba(255,255,255,.1); border-radius:20px; background:linear-gradient(135deg,rgba(229,9,20,.14),rgba(18,18,18,.96) 45%); }
    .library-frequent-heading { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:14px; }
    .library-frequent-heading span { color:#ff6971; font-size:12px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .library-frequent-heading h2 { margin:4px 0 0; font-size:24px; }
    .library-frequent-heading small { color:rgba(255,255,255,.56); }
    .library-frequent-carousel { position:relative; height:clamp(260px,27vw,390px); }
    .library-frequent-slide { position:absolute; top:50%; left:50%; width:31%; display:grid; gap:7px; color:#fff; text-decoration:none; opacity:0; visibility:hidden; pointer-events:none; transform:translate(-50%,-50%) scale(.72); transform-origin:center center; transition:transform .8s cubic-bezier(.22,.61,.36,1),opacity .6s ease,filter .6s ease; will-change:transform,opacity; }
    .library-frequent-slide.is-left { z-index:1; opacity:.46; visibility:visible; pointer-events:auto; transform:translate(-130%,-50%) scale(.8); filter:brightness(.46) saturate(.72); }
    .library-frequent-slide.is-center { z-index:4; opacity:1; visibility:visible; pointer-events:auto; transform:translate(-50%,-50%) scale(1.3); }
    .library-frequent-slide.is-right { z-index:2; opacity:.46; visibility:visible; pointer-events:auto; transform:translate(30%,-50%) scale(.8); filter:brightness(.46) saturate(.72); }
    .library-frequent-poster { position:relative; display:grid; place-items:center; aspect-ratio:16/9; overflow:hidden; border-radius:13px; background:#222; }
    .library-frequent-poster::after { content:""; position:absolute; inset:42% 0 0; background:linear-gradient(transparent,rgba(0,0,0,.86)); }
    .library-frequent-poster > img { width:100%; height:100%; object-fit:cover; }
    .library-frequent-poster > b { position:absolute; z-index:2; right:9px; bottom:8px; padding:5px 8px; border-radius:999px; background:rgba(0,0,0,.78); font-size:11px; }
    .library-play { position:absolute; z-index:2; width:44px; height:44px; display:grid; place-items:center; border-radius:50%; background:#e50914; box-shadow:0 8px 24px rgba(229,9,20,.4); }
    .library-frequent-slide > strong { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .library-frequent-slide > small { color:rgba(255,255,255,.62); }
    .library-frequent-slide.is-center .library-frequent-poster { box-shadow:0 18px 42px rgba(0,0,0,.45),0 0 0 1px rgba(229,9,20,.35); }

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

        .library-frequent-section { padding:16px; }
        .library-frequent-heading { align-items:start; flex-direction:column; gap:5px; }
        .library-frequent-carousel { height:clamp(180px,48vw,250px); }
        .library-frequent-slide { width:38%; }
        .library-frequent-slide.is-left { transform:translate(-128%,-50%) scale(.76); }
        .library-frequent-slide.is-center { transform:translate(-50%,-50%) scale(1.22); }
        .library-frequent-slide.is-right { transform:translate(28%,-50%) scale(.76); }
        .library-frequent-slide > strong { font-size:11px; }
        .library-frequent-slide > small { display:none; }
    }

    @media (prefers-reduced-motion:reduce) { .library-frequent-slide { transition:none; } }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-library-carousel]').forEach(carousel => {
        const slides = Array.from(carousel.querySelectorAll('.library-frequent-slide'));
        if (slides.length < 3) {
            carousel.style.height = 'auto';
            carousel.style.display = 'grid';
            carousel.style.gridTemplateColumns = `repeat(${slides.length}, minmax(0, 1fr))`;
            carousel.style.gap = '15px';
            slides.forEach(slide => {
                slide.style.position = 'static';
                slide.style.width = 'auto';
                slide.style.opacity = '1';
                slide.style.visibility = 'visible';
                slide.style.pointerEvents = 'auto';
                slide.style.transform = 'none';
            });
            return;
        }
        let center = 1;
        let timer;
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const render = () => {
            const left = (center - 1 + slides.length) % slides.length;
            const right = (center + 1) % slides.length;
            slides.forEach((slide, index) => {
                slide.classList.toggle('is-left', index === left);
                slide.classList.toggle('is-center', index === center);
                slide.classList.toggle('is-right', index === right);
                const visible = index === left || index === center || index === right;
                slide.setAttribute('aria-hidden', visible ? 'false' : 'true');
                slide.tabIndex = visible ? 0 : -1;
            });
        };
        const moveRight = () => { center = (center - 1 + slides.length) % slides.length; render(); };
        const stop = () => window.clearInterval(timer);
        const start = () => { stop(); if (!reducedMotion) timer = window.setInterval(moveRight, 3200); };
        render();
        start();
        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        carousel.addEventListener('focusin', stop);
        carousel.addEventListener('focusout', event => { if (!carousel.contains(event.relatedTarget)) start(); });
    });
});
</script>
@endpush
@endsection
