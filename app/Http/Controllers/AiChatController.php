<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\AiConversation;
use App\Models\Movie;
use App\Models\MovieViewEvent;
use App\Models\Subscription;
use App\Services\GeminiMovieAdvisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function chat(Request $request, GeminiMovieAdvisor $advisor): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:600'],
            'history' => ['sometimes', 'array', 'max:8'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.text' => ['required_with:history', 'string', 'max:600'],
        ]);

        $user = $request->user();
        $conversation = $user
            ? AiConversation::firstOrCreate(['user_id' => $user->id], ['title' => 'CineBot'])
            : null;
        $conversation?->messages()->create(['role' => 'user', 'content' => $data['message']]);
        $preferredCategories = $this->preferredCategories($user?->id);
        $candidates = $this->candidateMovies($preferredCategories->pluck('id'));
        $plans = Subscription::query()->orderBy('price')->get(['name', 'price', 'description']);

        if ($localAnswer = $this->localAnswer($data['message'], $candidates, $preferredCategories)) {
            return $this->respond($localAnswer, $conversation);
        }

        try {
            $answer = $advisor->ask($this->buildPrompt(
                $data['message'],
                collect($data['history'] ?? []),
                $candidates,
                $preferredCategories,
                $plans,
                $user?->subscription?->name
            ));
        } catch (\Throwable $exception) {
            Log::warning('AI movie advisor unavailable', ['message' => $exception->getMessage()]);

            $fallbackMovies = $this->fallbackMovies($data['message'], $candidates)->take(4);

            return $this->respond([
                'message' => $fallbackMovies->isEmpty()
                    ? 'Mình chưa tìm thấy phim phù hợp trong kho CineHub. Bạn thử mô tả thêm thể loại hoặc tâm trạng nhé!'
                    : 'Gemini đang phản hồi chậm, nhưng CineBot đã chọn nhanh một vài phim phù hợp từ kho CineHub cho bạn:',
                'movies' => $fallbackMovies->map(fn ($movie) => $this->moviePayload($movie))->values(),
            ], $conversation);
        }

        $movieIds = collect($answer['movie_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(4);
        $allowedIds = $candidates->pluck('id')->map(fn ($id) => (int) $id);
        $movieIds = $movieIds->intersect($allowedIds)->values();
        $moviesById = Movie::whereIn('id', $movieIds)->get()->keyBy('id');

        return $this->respond([
            'message' => strip_tags((string) $answer['reply']),
            'movies' => $movieIds->map(function ($id) use ($moviesById) {
                $movie = $moviesById->get($id);
                if (!$movie) return null;
                return $this->moviePayload($movie);
            })->filter()->values(),
        ], $conversation);
    }

    public function history(Request $request): JsonResponse
    {
        $conversation = AiConversation::where('user_id', $request->user()->id)->first();
        if (!$conversation) return response()->json(['messages' => []]);

        $messages = $conversation->messages()->latest('id')->limit(30)->get()
            ->reverse()->values()->map(fn ($message) => [
                'role' => $message->role,
                'text' => $message->content,
                'movies' => $message->metadata['movies'] ?? [],
            ]);

        return response()->json(['messages' => $messages]);
    }

    private function respond(array $payload, ?AiConversation $conversation): JsonResponse
    {
        $conversation?->messages()->create([
            'role' => 'assistant',
            'content' => (string) ($payload['message'] ?? ''),
            'metadata' => ['movies' => $payload['movies'] ?? []],
        ]);
        $conversation?->touch();

        return response()->json($payload);
    }

    private function fallbackMovies(string $message, Collection $candidates): Collection
    {
        $normalized = mb_strtolower($message);
        $filtered = $candidates;

        if (str_contains($normalized, 'rạp')) {
            $filtered = $filtered->filter(fn ($movie) => $movie->status === 'Chiếu rạp');
        }

        if (str_contains($normalized, 'mới')) {
            return $filtered->sortByDesc('publish_date')->values();
        }

        if (str_contains($normalized, 'quan tâm') || str_contains($normalized, 'hot')) {
            return $filtered->sortByDesc('interests_count')->values();
        }

        return $filtered->sortByDesc('view_events_count')->values();
    }

    private function localAnswer(string $message, Collection $candidates, Collection $preferredCategories): ?array
    {
        $normalized = mb_strtolower(trim($message));
        $movies = null;
        $reply = null;

        if (str_contains($normalized, 'phim mới') || str_contains($normalized, 'mới nổi bật') || str_contains($normalized, 'mới ra')) {
            $movies = $candidates->sortByDesc('publish_date');
            $reply = 'Đây là những phim mới nổi bật trong kho CineHub, bạn xem thử nhé!';
        } elseif (str_contains($normalized, 'nhiều lượt xem') || str_contains($normalized, 'xem nhiều nhất')) {
            $movies = $candidates;
            if (str_contains($normalized, 'rạp')) {
                $movies = $movies->filter(fn ($movie) => $movie->status === 'Chiếu rạp');
                $reply = 'Đây là những phim chiếu rạp có nhiều lượt xem nhất trên CineHub:';
            } else {
                $reply = 'Đây là những phim đang có nhiều lượt xem nhất trên CineHub:';
            }
            $movies = $movies->sortByDesc('view_events_count');
        } elseif (str_contains($normalized, 'quan tâm') || str_contains($normalized, 'đang hot') || str_contains($normalized, 'phim hot')) {
            $movies = $candidates->sortByDesc('interests_count');
            $reply = 'Những phim này đang nhận được nhiều sự quan tâm nhất:';
        } elseif (str_contains($normalized, 'theo gu') || str_contains($normalized, 'gu của tôi') || str_contains($normalized, 'hợp gu')) {
            $movies = $candidates;
            $genres = $preferredCategories->pluck('name')->filter()->join(', ');
            $reply = $genres !== ''
                ? "Dựa trên lịch sử xem, có vẻ bạn thích {$genres}. Mình gợi ý các phim này:"
                : 'Mình chưa có đủ lịch sử để hiểu gu của bạn, nên chọn một vài phim được đánh giá tốt nhé!';
        } elseif (str_contains($normalized, 'hôm nay xem') || str_contains($normalized, 'tối nay xem') || $normalized === 'xem gì') {
            $movies = $candidates->sortByDesc(fn ($movie) => ((int) $movie->view_events_count * 2) + (int) $movie->interests_count);
            $reply = 'Hôm nay CineBot chọn nhanh những phim này cho bạn:';
        }

        if ($movies === null) return null;

        $movies = $movies->take(4)->values();

        return [
            'message' => $movies->isEmpty()
                ? 'Mình chưa tìm thấy phim phù hợp trong kho CineHub ở thời điểm này.'
                : $reply,
            'movies' => $movies->map(fn ($movie) => $this->moviePayload($movie))->values(),
        ];
    }

    private function moviePayload(Movie $movie): array
    {
        return [
            'id' => $movie->id,
            'title' => $movie->title,
            'thumbnail' => $movie->thumbnail,
            'rating' => $movie->rating,
            'level' => $movie->level,
            'url' => route('movies.introduce', $movie->id),
        ];
    }

    private function preferredCategories(?int $userId): Collection
    {
        if (!$userId) return collect();

        $categoryIds = MovieViewEvent::query()
            ->join('movies', 'movies.id', '=', 'movie_view_events.movie_id')
            ->where('movie_view_events.user_id', $userId)
            ->whereNotNull('movies.category_id')
            ->selectRaw('movies.category_id, COUNT(*) as views_count')
            ->groupBy('movies.category_id')
            ->orderByDesc('views_count')
            ->limit(4)
            ->pluck('views_count', 'movies.category_id');

        return Category::whereIn('id', $categoryIds->keys())
            ->get(['id', 'name'])
            ->sortByDesc(fn ($category) => $categoryIds[$category->id] ?? 0)
            ->values();
    }

    private function candidateMovies(Collection $preferredCategoryIds): Collection
    {
        $base = fn () => Movie::with(['category:id,name', 'categories:id,name'])
            ->withCount(['viewEvents', 'interests'])
            ->where('status_admin', 'published');

        $personalized = $preferredCategoryIds->isEmpty()
            ? collect()
            : $base()->where(function ($query) use ($preferredCategoryIds) {
                $query->whereIn('category_id', $preferredCategoryIds)
                    ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->whereIn('categories.id', $preferredCategoryIds));
            })->orderByDesc('rating')->limit(10)->get();

        $popular = $base()->orderByDesc('view_events_count')->orderByDesc('interests_count')->limit(12)->get();
        $newest = $base()->orderByDesc('publish_date')->limit(10)->get();

        return $personalized->concat($popular)->concat($newest)
            ->unique('id')
            ->take(24)
            ->values();
    }

    private function buildPrompt(
        string $message,
        Collection $history,
        Collection $movies,
        Collection $categories,
        Collection $plans,
        ?string $currentPlan
    ): string {
        $catalog = $movies->map(fn ($movie) => [
            'id' => $movie->id,
            'title' => $movie->title,
            'description' => mb_substr(strip_tags((string) $movie->description), 0, 240),
            'genres' => $movie->categories->pluck('name')->prepend($movie->category?->name)->filter()->unique()->values(),
            'rating' => $movie->rating,
            'level' => $movie->level,
            'release_date' => $movie->publish_date?->format('Y-m-d'),
            'views' => $movie->view_events_count,
            'interests' => $movie->interests_count,
        ])->values();

        return implode("\n", [
            'Bạn là CineBot, trợ lý chọn phim thân thiện của CineHub.',
            'Chỉ đề xuất phim có trong CATALOG và chỉ dùng đúng ID được cung cấp. Không bịa phim, giá hoặc tính năng.',
            'Ưu tiên đúng yêu cầu hiện tại; sau đó mới xét thể loại người dùng hay xem, phim mới và mức độ quan tâm.',
            'Chỉ nhắc nâng cấp gói khi phim phù hợp cần level cao hơn gói hiện tại hoặc người dùng hỏi về gói. Không quảng cáo dồn dập.',
            'Trả lời tiếng Việt ngắn gọn, tự nhiên. Không dùng Markdown.',
            'Bắt buộc trả JSON đúng dạng: {"reply":"...","movie_ids":[1,2]}. Chọn tối đa 4 phim.',
            'CURRENT_PLAN: ' . ($currentPlan ?: 'Khách/không xác định'),
            'PREFERRED_GENRES: ' . $categories->pluck('name')->join(', '),
            'PLANS: ' . $plans->toJson(JSON_UNESCAPED_UNICODE),
            'CATALOG: ' . $catalog->toJson(JSON_UNESCAPED_UNICODE),
            'RECENT_CONVERSATION: ' . $history->toJson(JSON_UNESCAPED_UNICODE),
            'USER_MESSAGE: ' . $message,
        ]);
    }
}
