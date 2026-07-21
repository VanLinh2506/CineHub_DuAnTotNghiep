<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Review;
use App\Services\CommentModerationService;
use Illuminate\Console\Command;

class ModerateExistingComments extends Command
{
    protected $signature = 'comments:moderate-existing {--days=7}';
    protected $description = 'Detect and hide recent comments/reviews that violate automatic rules';

    public function handle(CommentModerationService $moderation): int
    {
        $hidden = 0;
        $since = now()->subDays(max((int) $this->option('days'), 1));

        Comment::where('is_hidden', false)->where('created_at', '>=', $since)->orderBy('id')
            ->chunkById(200, function ($comments) use ($moderation, &$hidden) {
                foreach ($comments as $comment) {
                    if ($reason = $moderation->detectViolation($comment->content)) {
                        $hidden += $moderation->hideAndWarn($comment, null, $reason) ? 1 : 0;
                    }
                }
            });

        Review::where('is_hidden', false)->where('created_at', '>=', $since)->orderBy('id')
            ->chunkById(200, function ($reviews) use ($moderation, &$hidden) {
                foreach ($reviews as $review) {
                    if ($reason = $moderation->detectViolation($review->comment)) {
                        $hidden += $moderation->hideAndWarn($review, null, $reason) ? 1 : 0;
                    }
                }
            });

        $this->info("Automatically hidden {$hidden} item(s).");
        return self::SUCCESS;
    }
}
