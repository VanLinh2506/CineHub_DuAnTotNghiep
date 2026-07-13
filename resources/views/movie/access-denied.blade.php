<div class="upgrade-modal-overlay" id="upgradeAccessModal" role="dialog" aria-modal="true" aria-labelledby="upgradeModalTitle">
    <div class="upgrade-modal-panel">
        <button class="upgrade-modal-close" type="button" aria-label="Đóng" onclick="closeUpgradeAccessModal()">&times;</button>

        <div class="upgrade-modal-head">
            <div class="upgrade-lock"><i class="fas fa-lock"></i></div>
            <div>
                <span class="upgrade-kicker">Nội dung giới hạn</span>
                <h2 id="upgradeModalTitle">Bạn chưa đủ level để xem “{{ $movie->title }}”</h2>
                <p>
                    Phim yêu cầu tối thiểu gói <strong>{{ $movieLevel }}</strong>, trong khi gói hiện tại
                    của bạn là <strong>{{ $subscriptionName }}</strong>. Hãy chọn gói phù hợp để xem ngay.
                </p>
                <div class="upgrade-meta">
                    <span><i class="fas fa-user-shield"></i> Hiện tại: {{ $subscriptionName }}</span>
                    <span><i class="fas fa-crown"></i> Yêu cầu: {{ $movieLevel }}</span>
                    <span><i class="fas fa-coins"></i> {{ number_format($user->points ?? 0, 0, ',', '.') }} điểm</span>
                </div>
            </div>
        </div>

        <div class="upgrade-title-row">
            <div><span>Nâng cấp tài khoản</span><h3>Các gói mở khóa phim này</h3></div>
            <button type="button" onclick="closeUpgradeAccessModal()"><i class="fas fa-arrow-left"></i> Tiếp tục xem thông tin phim</button>
        </div>

        <div class="upgrade-plans">
            @forelse($eligibleSubscriptions as $package)
                @php
                    $currentPrice = (float) optional($user->subscription)->price;
                    $upgradeCost = max((float) $package->price - $currentPrice, 0);
                    $canAfford = ($user->points ?? 0) >= $upgradeCost;
                @endphp
                <article class="upgrade-plan {{ $package->name === $movieLevel ? 'recommended' : '' }}">
                    @if($package->name === $movieLevel)<span class="upgrade-best">Phù hợp nhất</span>@endif
                    <div class="upgrade-gem"><i class="fas fa-gem"></i></div>
                    <h4>{{ $package->name }}</h4>
                    <div class="upgrade-price">{{ number_format($upgradeCost, 0, ',', '.') }} <small>điểm nâng cấp</small></div>
                    <p>{{ $package->description }}</p>
                    @if($package->benefits)<div class="upgrade-benefits"><i class="fas fa-check-circle"></i> {{ $package->benefits }}</div>@endif

                    @if($canAfford)
                        <form method="POST" action="{{ route('profile.upgradeSubscription') }}" onsubmit="return confirm('Xác nhận nâng cấp lên gói {{ $package->name }} với {{ number_format($upgradeCost, 0, ',', '.') }} điểm?')">
                            @csrf
                            <input type="hidden" name="subscription_id" value="{{ $package->id }}">
                            <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                            <button class="upgrade-action" type="submit"><i class="fas fa-bolt"></i> Nâng cấp và xem ngay</button>
                        </form>
                    @else
                        <a class="upgrade-action deposit" href="{{ route('profile.index') }}#wallet">
                            <i class="fas fa-wallet"></i> Nạp thêm {{ number_format($upgradeCost - ($user->points ?? 0), 0, ',', '.') }} điểm
                        </a>
                    @endif
                </article>
            @empty
                <div class="upgrade-empty">Hiện chưa có gói phù hợp. Vui lòng liên hệ quản trị viên.</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    body.upgrade-modal-open { overflow:hidden; }
    .upgrade-modal-overlay { position:fixed; inset:0; z-index:10050; display:flex; align-items:center; justify-content:center; padding:24px; background:rgba(0,0,0,.78); backdrop-filter:blur(10px); animation:upgradeFade .2s ease; }
    .upgrade-modal-panel { position:relative; width:min(1100px,100%); max-height:92vh; overflow-y:auto; padding:30px; border:1px solid rgba(255,73,87,.38); border-radius:26px; color:#c8c8cb; background:linear-gradient(145deg,rgba(27,22,26,.98),rgba(10,11,15,.98)); box-shadow:0 35px 100px rgba(0,0,0,.7); animation:upgradeRise .28s ease; scrollbar-width:thin; scrollbar-color:#e50914 #242428; }
    .upgrade-modal-close { position:absolute; top:16px; right:18px; z-index:2; width:42px; height:42px; border:1px solid rgba(255,255,255,.12); border-radius:50%; color:#fff; background:rgba(255,255,255,.07); font-size:28px; line-height:1; cursor:pointer; }
    .upgrade-modal-close:hover { color:#fff; background:#e50914; transform:rotate(8deg); }
    .upgrade-modal-head { display:grid; grid-template-columns:auto 1fr; gap:22px; padding:4px 45px 26px 0; border-bottom:1px solid rgba(255,255,255,.09); }
    .upgrade-lock { width:68px; height:68px; display:grid; place-items:center; border-radius:19px; color:#fff; font-size:27px; background:linear-gradient(135deg,#e50914,#ff5965); box-shadow:0 12px 28px rgba(229,9,20,.3); }
    .upgrade-kicker,.upgrade-title-row span { color:#ff5965; font-size:.74rem; font-weight:850; letter-spacing:.15em; text-transform:uppercase; }
    .upgrade-modal-head h2 { margin:6px 0 8px; color:#fff; font-size:clamp(1.55rem,3vw,2.35rem); font-weight:850; }
    .upgrade-modal-head p { max-width:850px; margin:0; line-height:1.65; }
    .upgrade-meta { display:flex; flex-wrap:wrap; gap:9px; margin-top:15px; }
    .upgrade-meta span { padding:7px 11px; border:1px solid rgba(255,255,255,.1); border-radius:999px; color:#eee; background:rgba(255,255,255,.06); font-size:.84rem; }
    .upgrade-meta i { color:#ff5965; margin-right:4px; }
    .upgrade-title-row { display:flex; justify-content:space-between; align-items:end; gap:18px; margin:25px 0 17px; }
    .upgrade-title-row h3 { margin:4px 0 0; color:#fff; font-size:1.55rem; }
    .upgrade-title-row button { border:0; color:#bbb; background:none; cursor:pointer; }
    .upgrade-title-row button:hover { color:#fff; }
    .upgrade-plans { display:grid; grid-template-columns:repeat(auto-fit,minmax(245px,1fr)); gap:17px; }
    .upgrade-plan { position:relative; display:flex; flex-direction:column; padding:22px; border:1px solid rgba(255,255,255,.1); border-radius:19px; background:linear-gradient(145deg,#202127,#15161b); }
    .upgrade-plan.recommended { border-color:#ef3948; box-shadow:0 14px 36px rgba(229,9,20,.14); }
    .upgrade-best { position:absolute; top:15px; right:15px; padding:5px 9px; border-radius:999px; color:#fff; background:#e50914; font-size:.68rem; font-weight:800; }
    .upgrade-gem { width:41px; height:41px; display:grid; place-items:center; border-radius:12px; color:#ff5965; background:rgba(229,9,20,.14); }
    .upgrade-plan h4 { margin:14px 0 3px; color:#fff; font-size:1.4rem; }
    .upgrade-price { color:#fff; font-size:1.3rem; font-weight:850; }
    .upgrade-price small { color:#929298; font-size:.72rem; font-weight:500; }
    .upgrade-plan p { min-height:44px; margin:12px 0 9px; }
    .upgrade-benefits { flex:1; padding-top:10px; border-top:1px solid rgba(255,255,255,.08); color:#d0d0d4; line-height:1.5; }
    .upgrade-benefits i { color:#43d88b; }
    .upgrade-plan form { margin-top:18px; }
    .upgrade-action { width:100%; display:block; margin-top:18px; padding:12px 14px; border:0; border-radius:11px; color:#fff; text-align:center; text-decoration:none; font-weight:800; background:linear-gradient(135deg,#e50914,#ff4352); cursor:pointer; }
    .upgrade-plan form .upgrade-action { margin-top:0; }
    .upgrade-action.deposit { background:linear-gradient(135deg,#635bff,#847dff); }
    .upgrade-action:hover { color:#fff; transform:translateY(-2px); filter:brightness(1.08); }
    .upgrade-empty { grid-column:1/-1; padding:25px; text-align:center; border-radius:16px; background:#18191e; }
    @keyframes upgradeFade { from{opacity:0} to{opacity:1} }
    @keyframes upgradeRise { from{opacity:0;transform:translateY(20px) scale(.98)} to{opacity:1;transform:none} }
    @media(max-width:700px) { .upgrade-modal-overlay{padding:10px}.upgrade-modal-panel{max-height:96vh;padding:21px}.upgrade-modal-head{grid-template-columns:1fr;padding-right:32px}.upgrade-lock{width:55px;height:55px}.upgrade-title-row{align-items:start;flex-direction:column}.upgrade-meta{align-items:start;flex-direction:column} }
</style>

<script>
    function closeUpgradeAccessModal() {
        const modal = document.getElementById('upgradeAccessModal');
        if (modal) modal.remove();
        document.body.classList.remove('upgrade-modal-open');
    }

    document.body.classList.add('upgrade-modal-open');
    document.getElementById('upgradeAccessModal')?.addEventListener('click', function (event) {
        if (event.target === this) closeUpgradeAccessModal();
    });
    document.addEventListener('keydown', function closeUpgradeOnEscape(event) {
        if (event.key === 'Escape') {
            closeUpgradeAccessModal();
            document.removeEventListener('keydown', closeUpgradeOnEscape);
        }
    });
</script>
