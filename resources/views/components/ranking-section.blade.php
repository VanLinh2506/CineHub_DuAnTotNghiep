<section class="movies-section ranking-section">
    <div class="section-header">
        <h2 class="section-title">Top {{ $categoryName }}</h2>
        <a href="{{ route('movies.index', ['category' => $rankedMovies->first()->category_id]) }}" class="view-all-link">Xem tất cả</a>
    </div>

    <div class="ranking-row">
        @foreach ($rankedMovies as $rank => $movie)
            <a href="{{ route('movies.introduce', $movie->id) }}" class="ranking-card">
                <span class="ranking-number" data-rank="{{ $rank + 1 }}">{{ $rank + 1 }}</span>
                <div class="ranking-poster">
                    @if ($movie->thumbnail)
                        <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}" loading="lazy">
                    @else
                        <div class="ranking-poster-empty"><i class="fas fa-film"></i></div>
                    @endif
                    @if($movie->rating !== null)
                    <span class="ranking-rating"><i class="fas fa-star"></i> {{ number_format($movie->rating, 1) }}</span>
                    @endif
                </div>
                <div class="ranking-info">
                    <strong>{{ $movie->title }}</strong>
                    <small>{{ $movie->watch_history_count }} lượt xem tuần này</small>
                </div>
            </a>
        @endforeach
    </div>
</section>
