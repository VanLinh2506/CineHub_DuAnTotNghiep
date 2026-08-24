<?php

namespace Tests\Unit;

use App\Services\CommentModerationService;
use PHPUnit\Framework\TestCase;

class AutomaticCommentModerationTest extends TestCase
{
    public function test_repeated_character_spam_is_detected(): void
    {
        $service = new CommentModerationService();
        $this->assertSame('Spam ký tự lặp lại quá nhiều', $service->detectViolation('dmmmmmmmmm'));
    }

    public function test_normal_comment_is_allowed(): void
    {
        $service = new CommentModerationService();
        $this->assertNull($service->detectViolation('Bộ phim này khá hay và phần âm thanh rất tốt.'));
    }

    public function test_http_link_is_detected_immediately(): void
    {
        $service = new CommentModerationService();

        $this->assertSame(
            'Bình luận chứa đường dẫn hoặc liên kết không được phép',
            $service->detectViolation('Xem thêm tại https://example.com/phim-hay')
        );
    }

    public function test_bare_domain_is_detected(): void
    {
        $service = new CommentModerationService();

        $this->assertSame(
            'Bình luận chứa đường dẫn hoặc liên kết không được phép',
            $service->detectViolation('Truy cập example.vn để xem nhé')
        );
    }
}
