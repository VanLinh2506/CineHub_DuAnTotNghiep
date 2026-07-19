<div class="cinebot" id="cinebot">
    <button class="cinebot-nudge" type="button" hidden></button>
    <button class="cinebot-toggle" type="button" aria-label="Mở trợ lý chọn phim" aria-expanded="false">
        <img src="{{ storage_url('data/img/6b24954b-2085-4bb7-aecc-7b85eb9c4cc4.png') }}" alt="CineBot">
    </button>
    <section class="cinebot-panel" aria-label="Trợ lý chọn phim" hidden>
        <header>
            <span class="cinebot-avatar"><img src="{{ storage_url('data/img/6b24954b-2085-4bb7-aecc-7b85eb9c4cc4.png') }}" alt=""></span>
            <div><strong>CineBot</strong><small>Chọn phim hợp gu của bạn</small></div>
            <button class="cinebot-close" type="button" aria-label="Đóng"><i class="fas fa-times"></i></button>
        </header>
        <div class="cinebot-movie-preview" aria-hidden="true"></div>
        <div class="cinebot-messages" aria-live="polite">
            <div class="cinebot-message bot">Chào bạn! Hôm nay bạn muốn xem phim theo tâm trạng, thể loại hay thời lượng nào?</div>
        </div>
        <div class="cinebot-suggestions">
            <button type="button">Hôm nay xem gì?</button>
            <button type="button">Phim mới nổi bật</button>
            <button type="button">Gợi ý theo gu của tôi</button>
        </div>
        <form class="cinebot-form">
            <input type="text" maxlength="600" placeholder="Ví dụ: phim hài nhẹ nhàng..." autocomplete="off" required>
            <button type="submit" aria-label="Gửi"><i class="fas fa-paper-plane"></i></button>
        </form>
    </section>
</div>

@once
<style>
.cinebot{position:fixed;right:18px;bottom:18px;z-index:12000;font-family:inherit}.cinebot-nudge{position:absolute;right:4px;bottom:70px;width:max-content;max-width:230px;border:1px solid rgba(255,255,255,.16);border-radius:13px 13px 3px 13px;padding:9px 12px;color:#fff;background:rgba(24,25,31,.96);box-shadow:0 10px 30px rgba(0,0,0,.35);font-size:12px;line-height:1.35;animation:cinebotNudgeIn .25s ease}.cinebot-nudge:hover{border-color:rgba(229,9,20,.7)}@keyframes cinebotNudgeIn{from{opacity:0;transform:translateY(6px) scale(.96)}to{opacity:1;transform:none}}.cinebot-toggle{display:block;width:58px;height:58px;overflow:hidden;border:2px solid rgba(229,9,20,.8);border-radius:50%;padding:0;background:#fff;box-shadow:0 10px 28px rgba(229,9,20,.38);transition:transform .2s,box-shadow .2s}.cinebot-toggle:hover{transform:translateY(-2px) scale(1.04);box-shadow:0 14px 34px rgba(229,9,20,.5)}.cinebot-toggle img{width:100%;height:100%;object-fit:cover;transform:scale(1.22)}.cinebot-panel{position:absolute;right:0;bottom:68px;width:min(330px,calc(100vw - 24px));height:min(480px,calc(100vh - 100px));overflow:hidden;border:1px solid rgba(255,255,255,.13);border-radius:17px;background:rgba(15,16,21,.97);box-shadow:0 20px 58px rgba(0,0,0,.55);backdrop-filter:blur(18px)}.cinebot-panel:not([hidden]){display:grid;grid-template-rows:auto 1fr auto auto}.cinebot-panel header{display:flex;align-items:center;gap:9px;padding:11px 12px;border-bottom:1px solid rgba(255,255,255,.09);color:#fff}.cinebot-avatar{display:block;width:32px;height:32px;overflow:hidden;border:1px solid rgba(229,9,20,.65);border-radius:10px;background:#fff}.cinebot-avatar img{width:100%;height:100%;object-fit:cover;transform:scale(1.18)}.cinebot-panel header div{display:grid;flex:1}.cinebot-panel header strong{font-size:13px}.cinebot-panel header small{color:#9ca3af;font-size:10px}.cinebot-close{border:0;color:#9ca3af;background:transparent}.cinebot-messages{display:flex;flex-direction:column;gap:8px;overflow-y:auto;padding:11px}.cinebot-message{max-width:90%;padding:8px 10px;border-radius:12px;color:#e5e7eb;background:#292b33;font-size:12px;line-height:1.4;white-space:pre-wrap}.cinebot-message.user{align-self:flex-end;color:#fff;background:#b20710}.cinebot-message.loading{color:#9ca3af}.cinebot-movies{position:relative;display:flex;flex-wrap:wrap;gap:5px;width:100%;max-width:96%}.cinebot-movie-link{position:relative;display:inline-flex;align-items:center;gap:4px;border:1px solid rgba(229,9,20,.42);border-radius:999px;padding:5px 9px;color:#ffb1b6;background:rgba(229,9,20,.09);text-decoration:none;font-size:11px;line-height:1.2}.cinebot-movie-link:hover,.cinebot-movie-link:focus{color:#fff;border-color:#e50914;background:rgba(229,9,20,.2);outline:none}.cinebot-movie-preview{position:absolute;z-index:50;right:10px;display:none;width:190px;grid-template-columns:54px 1fr;gap:8px;padding:8px;border:1px solid rgba(255,255,255,.16);border-radius:11px;color:#fff;background:#1c1e25;box-shadow:0 12px 30px rgba(0,0,0,.5);pointer-events:none}.cinebot-movie-preview.is-visible{display:grid}.cinebot-movie-preview img{width:54px;height:74px;object-fit:cover;border-radius:7px}.cinebot-movie-preview strong{display:block;margin-bottom:5px;font-size:11px;line-height:1.3}.cinebot-movie-preview small{color:#fbbf24;font-size:10px}.cinebot-suggestions{display:flex;gap:5px;overflow-x:auto;padding:0 10px 8px}.cinebot-suggestions button{flex:none;border:1px solid rgba(229,9,20,.45);border-radius:999px;padding:5px 8px;color:#ff9ca2;background:rgba(229,9,20,.1);font-size:10px}.cinebot-form{display:grid;grid-template-columns:1fr 36px;gap:6px;padding:9px;border-top:1px solid rgba(255,255,255,.09)}.cinebot-form input{min-width:0;border:1px solid #343641;border-radius:10px;padding:8px 10px;color:#fff;background:#202229;outline:none;font-size:12px}.cinebot-form input:focus{border-color:#e50914}.cinebot-form button{border:0;border-radius:10px;color:#fff;background:#e50914}.cinebot button{cursor:pointer}@media(max-width:575px){.cinebot{right:12px;bottom:12px}.cinebot-toggle{width:52px;height:52px}.cinebot-nudge{right:0;bottom:63px;max-width:210px}.cinebot-panel{position:fixed;inset:auto 10px 72px auto;width:min(320px,calc(100vw - 20px));height:min(460px,calc(100vh - 90px))}}
.cinebot-toggle,.cinebot-avatar{background:transparent}.cinebot-toggle img{transform:scale(1.08)}.cinebot-avatar img{transform:scale(1.06)}.cinebot-nudge{right:-1px;bottom:76px;border-radius:24px;padding:10px 15px;overflow:visible}.cinebot-nudge::before{content:"";position:absolute;right:18px;bottom:-8px;width:13px;height:13px;border-radius:50%;background:rgba(24,25,31,.96);box-shadow:10px 8px 0 -3px rgba(24,25,31,.96)}.cinebot-nudge::after{content:"";position:absolute;left:18px;top:-5px;width:22px;height:10px;border-radius:50%;background:rgba(255,255,255,.035)}
</style>
@endonce

@push('scripts')
<script>
(() => {
    const root = document.getElementById('cinebot');
    if (!root) return;
    const toggle = root.querySelector('.cinebot-toggle');
    const panel = root.querySelector('.cinebot-panel');
    const close = root.querySelector('.cinebot-close');
    const form = root.querySelector('.cinebot-form');
    const input = form.querySelector('input');
    const messages = root.querySelector('.cinebot-messages');
    const moviePreview = root.querySelector('.cinebot-movie-preview');
    const nudge = root.querySelector('.cinebot-nudge');
    const isWatchPage = @json(Route::is('movies.watch'));
    const history = [];
    let busy = false;
    let historyLoaded = false;

    const setOpen = open => { panel.hidden = !open; toggle.setAttribute('aria-expanded', String(open)); nudge.hidden = true; if (open) input.focus(); };
    toggle.addEventListener('click', () => { const opening = panel.hidden; setOpen(opening); if (opening) loadSavedHistory(); });
    close.addEventListener('click', () => setOpen(false));

    function addMessage(text, role, extraClass = '') {
        const node = document.createElement('div');
        node.className = `cinebot-message ${role} ${extraClass}`;
        node.textContent = text;
        messages.appendChild(node);
        messages.scrollTop = messages.scrollHeight;
        return node;
    }

    function addMovies(movies) {
        if (!movies?.length) return;
        const list = document.createElement('div');
        list.className = 'cinebot-movies';
        movies.forEach(movie => {
            const link = document.createElement('a');
            link.className = 'cinebot-movie-link';
            link.href = movie.url;
            link.textContent = movie.title;
            const icon = document.createElement('i');
            icon.className = 'fas fa-arrow-up-right-from-square';
            link.addEventListener('mouseenter', () => showMoviePreview(movie, link));
            link.addEventListener('mouseleave', hideMoviePreview);
            link.addEventListener('focus', () => showMoviePreview(movie, link));
            link.addEventListener('blur', hideMoviePreview);
            link.append(icon);
            list.appendChild(link);
        });
        messages.appendChild(list); messages.scrollTop = messages.scrollHeight;
    }

    function showMoviePreview(movie, link) {
        moviePreview.replaceChildren();
        const image = document.createElement('img');
        image.src = movie.thumbnail || '';
        image.alt = '';
        const info = document.createElement('span');
        const title = document.createElement('strong');
        title.textContent = movie.title;
        const meta = document.createElement('small');
        meta.textContent = `★ ${Number(movie.rating || 0).toFixed(1)} · ${movie.level || 'Free'}`;
        info.append(title, meta);
        moviePreview.append(image, info);
        const panelRect = panel.getBoundingClientRect();
        const linkRect = link.getBoundingClientRect();
        const top = Math.max(55, Math.min(linkRect.top - panelRect.top - 92, panelRect.height - 105));
        moviePreview.style.top = `${top}px`;
        moviePreview.classList.add('is-visible');
    }

    function hideMoviePreview() {
        moviePreview.classList.remove('is-visible');
    }

    async function loadSavedHistory() {
        if (historyLoaded) return;
        historyLoaded = true;
        @auth
        try {
            const response = await fetch(@json(route('ai.history')), { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            if (!data.messages?.length) return;
            messages.replaceChildren();
            data.messages.forEach(item => {
                addMessage(item.text, item.role === 'user' ? 'user' : 'bot');
                history.push({ role: item.role, text: item.text });
                if (item.role === 'assistant') addMovies(item.movies);
            });
        } catch (_) {}
        @endauth
    }

    async function send(text) {
        if (busy || !text.trim()) return;
        busy = true; input.disabled = true;
        addMessage(text.trim(), 'user');
        const previousHistory = history.slice(-8);
        history.push({ role: 'user', text: text.trim() });
        const loading = addMessage('CineBot đang trả lời...', 'bot', 'loading');
        try {
            const response = await fetch(@json(route('ai.chat')), {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@json(csrf_token())},
                body: JSON.stringify({ message: text.trim(), history: previousHistory })
            });
            const data = await response.json();
            loading.remove();
            const reply = data.message || 'Mình chưa tìm được gợi ý phù hợp.';
            addMessage(reply, 'bot'); history.push({ role: 'assistant', text: reply }); addMovies(data.movies);
        } catch (_) {
            loading.textContent = 'Không kết nối được với CineBot. Bạn thử lại nhé!';
            loading.classList.remove('loading');
        } finally {
            busy = false; input.disabled = false; input.focus();
        }
    }

    form.addEventListener('submit', event => { event.preventDefault(); const text = input.value; input.value = ''; send(text); });
    root.querySelectorAll('.cinebot-suggestions button').forEach(button => button.addEventListener('click', () => send(button.textContent)));

    const nudgeTopics = [
        { canonical: 'Hôm nay xem phim gì?', variants: ['Hôm nay xem gì nhỉ?', 'Tối nay chọn phim gì đây?', 'Để CineBot chọn phim hôm nay nhé?'] },
        { canonical: 'Gợi ý phim theo gu của tôi', variants: ['Có phim nào đúng gu bạn không nhỉ?', 'Muốn khám phá phim hợp gu không?', 'CineBot đoán gu phim của bạn nhé?'] },
        { canonical: 'Phim nào đang được nhiều người quan tâm?', variants: ['Mọi người đang mê phim nào?', 'Xem thử phim đang hot nhé?', 'Phim nào đang được quan tâm nhất nhỉ?'] },
        { canonical: 'Có phim mới nào đáng xem?', variants: ['Có phim mới ra mắt đấy!', 'Khám phá phim mới cùng CineBot nhé?', 'Phim mới nào đáng xem hôm nay?'] },
        { canonical: 'Gợi ý một bộ phim nhẹ nhàng', variants: ['Cần một bộ phim thư giãn không?', 'Xem gì nhẹ nhàng cho đỡ mệt nhỉ?', 'Một bộ phim dễ chịu nhé?'] },
        { canonical: 'Gợi ý phim dựa theo tâm trạng', variants: ['Tâm trạng hôm nay hợp phim gì?', 'Kể CineBot nghe tâm trạng nhé?', 'Chọn phim theo cảm xúc hiện tại không?'] }
    ];
    let nudgeTopic = null;
    let nudgeTimer = null;
    let hideNudgeTimer = null;
    let hasShownNudge = false;

    function scheduleNudge(delay = hasShownNudge ? 30000 : 10000) {
        if (isWatchPage) return;
        clearTimeout(nudgeTimer);
        nudgeTimer = setTimeout(showNudge, delay);
    }

    function showNudge() {
        if (isWatchPage || !panel.hidden || document.hidden || busy) return scheduleNudge();
        nudgeTopic = nudgeTopics[Math.floor(Math.random() * nudgeTopics.length)];
        nudge.textContent = nudgeTopic.variants[Math.floor(Math.random() * nudgeTopic.variants.length)];
        nudge.hidden = false;
        hasShownNudge = true;
        clearTimeout(hideNudgeTimer);
        hideNudgeTimer = setTimeout(() => { nudge.hidden = true; scheduleNudge(30000); }, 5000);
    }

    nudge.addEventListener('click', () => {
        if (!nudgeTopic) return;
        const question = nudgeTopic.canonical;
        setOpen(true);
        send(question);
    });
    ['pointerdown', 'keydown', 'scroll'].forEach(eventName => document.addEventListener(eventName, event => {
        if (root.contains(event.target)) return;
        if (!nudge.hidden) nudge.hidden = true;
        scheduleNudge();
    }, { passive: true }));
    scheduleNudge();
})();
</script>
@endpush
