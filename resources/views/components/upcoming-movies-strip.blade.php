@if(!empty($upcomingMovies) && $upcomingMovies->isNotEmpty())
<section class="upcoming-strip">
    <div class="upcoming-strip-heading">
        <div>
            <span>Sắp ra mắt</span>
            <h2>Phim lẻ sắp chiếu</h2>
        </div>
        <small>Chỉ phát hành trailer trước ngày chiếu</small>
    </div>
    <div class="upcoming-strip-list">
        @foreach($upcomingMovies as $upcomingMovie)
        @php $stripInterested = in_array($upcomingMovie->id, $interestedMovieIds ?? []); @endphp
        <article class="upcoming-strip-card">
            <a href="{{ route('movies.introduce', $upcomingMovie->id) }}">
                <img src="{{ $upcomingMovie->thumbnail }}" alt="{{ $upcomingMovie->title }}" loading="lazy">
                <span class="upcoming-strip-date"><i class="far fa-calendar-alt"></i> {{ $upcomingMovie->publish_date->format('d/m/Y') }}</span>
                <span class="upcoming-strip-trailer"><i class="fas fa-play"></i> Trailer</span>
            </a>
            <strong>{{ $upcomingMovie->title }}</strong>
            <button type="button" class="strip-interest {{ $stripInterested ? 'active' : '' }}"
                data-url="{{ route('movies.interest', $upcomingMovie->id) }}"
                data-login="{{ route('login') }}"
                onclick="markStripInterested(this)" {{ $stripInterested ? 'disabled' : '' }}>
                <i class="{{ $stripInterested ? 'fas' : 'far' }} fa-bell"></i>
                <span>{{ $stripInterested ? 'Đã quan tâm' : 'Quan tâm' }}</span>
                <b>{{ (int) $upcomingMovie->interests_count }}</b>
            </button>
        </article>
        @endforeach
    </div>
</section>

<style>
.upcoming-strip{margin:30px 0;padding:20px;border-radius:18px;background:linear-gradient(135deg,rgba(229,9,20,.1),#171717 45%);border:1px solid rgba(255,255,255,.08)}
.upcoming-strip-heading{display:flex;justify-content:space-between;align-items:end;gap:12px;margin-bottom:15px}.upcoming-strip-heading span{color:#ff6870;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.12em}.upcoming-strip-heading h2{margin:3px 0 0;color:#fff;font-size:21px}.upcoming-strip-heading small{color:rgba(255,255,255,.55)}
.upcoming-strip-list{display:grid;grid-auto-flow:column;grid-auto-columns:minmax(175px,210px);gap:14px;overflow-x:auto;padding-bottom:5px}.upcoming-strip-card{display:grid;gap:8px;min-width:0;color:#fff}.upcoming-strip-card>a{position:relative;display:block;aspect-ratio:16/10;overflow:hidden;border-radius:12px;background:#222}.upcoming-strip-card img{width:100%;height:100%;object-fit:cover}.upcoming-strip-card strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px}.upcoming-strip-date,.upcoming-strip-trailer{position:absolute;z-index:2;padding:5px 7px;border-radius:999px;background:rgba(0,0,0,.78);color:#fff;font-size:10px;font-weight:800}.upcoming-strip-date{left:7px;bottom:7px}.upcoming-strip-trailer{right:7px;top:7px}
.strip-interest{display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;border:0;border-radius:999px;background:#e50914;color:#fff;font-size:12px;font-weight:800;cursor:pointer}.strip-interest b{padding:1px 5px;border-radius:999px;background:rgba(0,0,0,.2)}.strip-interest.active{background:rgba(255,255,255,.1);color:rgba(255,255,255,.7)}
@media(max-width:576px){.upcoming-strip-heading{align-items:start;flex-direction:column}.upcoming-strip-list{grid-auto-columns:72%}}
</style>
<script>
function markStripInterested(button){
    @guest window.location.href=button.dataset.login;return; @endguest
    if(button.disabled)return;button.disabled=true;
    fetch(button.dataset.url,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':@json(csrf_token())}})
    .then(response=>response.ok?response.json():Promise.reject()).then(data=>{button.classList.add('active');button.querySelector('i').className='fas fa-bell';button.querySelector('span').textContent='Đã quan tâm';button.querySelector('b').textContent=data.count;})
    .catch(()=>{button.disabled=false;});
}
</script>
@endif
