<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----------------------------------------------------------------
        // Bảng lưu kết quả kiểm duyệt của AI cho từng comment/review
        // ----------------------------------------------------------------
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->increments('id');

            // Đối tượng bị kiểm duyệt
            $table->string('target_type', 20);          // 'comment' | 'review'
            $table->unsignedInteger('target_id');

            $table->unsignedInteger('user_id');          // Chủ nội dung
            $table->text('content_snapshot');            // Nội dung tại thời điểm kiểm duyệt

            // Kết quả từ AI
            $table->boolean('is_violation')->default(false);
            $table->string('violated_clause', 255)->nullable(); // Điều khoản vi phạm
            $table->string('action', 30)->default('ALLOW');     // ALLOW | DELETE_COMMENT | TEMP_BAN | PERMANENT_BAN
            $table->text('reason_to_user')->nullable();         // Lý do tiếng Việt gửi cho user
            $table->json('raw_ai_response')->nullable();        // Full JSON từ Gemini để debug

            // Nguồn kiểm duyệt
            $table->string('source', 20)->default('ai');        // 'regex' | 'ai' | 'manual'
            $table->boolean('executed')->default(false);        // Đã áp dụng action chưa

            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
            $table->index('user_id');
            $table->index('action');
        });

        // ----------------------------------------------------------------
        // Bảng kháng nghị khi user cho rằng bị phạt oan
        // ----------------------------------------------------------------
        Schema::create('moderation_appeals', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('moderation_log_id');   // Liên kết với log bị phạt
            $table->unsignedInteger('user_id');              // User gửi kháng nghị
            $table->text('appeal_reason');                   // Lý do user kháng nghị

            // Trạng thái xử lý
            $table->string('status', 20)->default('pending'); // pending | reviewing | approved | rejected
            $table->unsignedInteger('reviewed_by')->nullable(); // Admin xử lý
            $table->text('admin_note')->nullable();            // Ghi chú của admin
            $table->timestamp('reviewed_at')->nullable();

            // Giới hạn số lần kháng nghị
            $table->unsignedTinyInteger('attempt_number')->default(1); // Lần thứ mấy

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('moderation_log_id');
            $table->index('user_id');
            $table->index('status');

            $table->foreign('moderation_log_id')->references('id')->on('moderation_logs')->onDelete('cascade');
        });

        // Thêm cột moderation_log_id vào comments để trace nhanh
        if (Schema::hasTable('comments') && !Schema::hasColumn('comments', 'moderation_log_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedInteger('moderation_log_id')->nullable()->after('is_hidden');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_appeals');
        Schema::dropIfExists('moderation_logs');

        if (Schema::hasColumn('comments', 'moderation_log_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('moderation_log_id');
            });
        }
    }
};
