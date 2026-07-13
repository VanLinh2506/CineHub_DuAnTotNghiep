<?php

namespace App\Services;

use App\Mail\ContractExpiringMail;
use App\Models\TheaterContract;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TheaterContractService
{
    public function createContract(array $data): TheaterContract
    {
        return DB::transaction(function () use ($data) {
            $contract = TheaterContract::create([
                'contract_code' => $data['contract_code'] ?? $this->generateContractCode(),
                'theater_id' => $data['theater_id'],
                'representative_user_id' => $data['representative_user_id'],
                'super_admin_id' => $data['super_admin_id'] ?? null,
                'renewed_from_id' => $data['renewed_from_id'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'bestseller_price_min' => $data['bestseller_price_min'] ?? 90000,
                'bestseller_price_max' => $data['bestseller_price_max'] ?? 100000,
                'new_release_price_min' => $data['new_release_price_min'] ?? 100000,
                'new_release_price_max' => $data['new_release_price_max'] ?? 120000,
                'admin_permissions' => $data['admin_permissions'] ?? $this->defaultPermissions(),
                'auto_revoke_terms' => $data['auto_revoke_terms'] ?? $this->defaultAutoRevokeTerms(),
                'super_admin_signature' => $data['super_admin_signature'] ?? null,
                'representative_signature' => $data['representative_signature'] ?? null,
                'status' => $this->initialStatus($data['start_date'], $data['end_date']),
                'source_pdf_path' => $data['source_pdf_path'] ?? null,
                'extracted_text' => $data['extracted_text'] ?? null,
            ]);

            if ($contract->status === TheaterContract::STATUS_ACTIVE) {
                $this->promoteRepresentative($contract);

                if (!empty($data['renewed_from_id'])) {
                    TheaterContract::where('id', $data['renewed_from_id'])
                        ->where('status', TheaterContract::STATUS_ACTIVE)
                        ->update(['status' => TheaterContract::STATUS_RENEWED]);
                }
            }

            $this->generatePdf($contract->fresh(['theater', 'representative', 'superAdmin']));

            return $contract->fresh(['theater', 'representative', 'superAdmin']);
        });
    }

    public function generatePdf(TheaterContract $contract): string
    {
        $contract->loadMissing(['theater', 'representative', 'superAdmin']);

        $pdf = Pdf::loadView('admin.contracts.pdf', [
            'contract' => $contract,
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4');

        $path = 'contracts/' . $contract->contract_code . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $contract->update(['pdf_path' => $path]);

        return $path;
    }

    public function activateDueContracts(): int
    {
        $contracts = TheaterContract::with(['representative'])
            ->where('status', TheaterContract::STATUS_PENDING)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->get();

        foreach ($contracts as $contract) {
            DB::transaction(function () use ($contract) {
                $contract->update([
                    'status' => TheaterContract::STATUS_ACTIVE,
                    'activated_at' => now(),
                ]);

                $this->promoteRepresentative($contract);

                if ($contract->renewed_from_id) {
                    TheaterContract::where('id', $contract->renewed_from_id)
                        ->whereIn('status', [TheaterContract::STATUS_ACTIVE, TheaterContract::STATUS_EXPIRED])
                        ->update(['status' => TheaterContract::STATUS_RENEWED]);
                }
            });
        }

        return $contracts->count();
    }

    public function expireContracts(): int
    {
        $contracts = TheaterContract::with(['representative'])
            ->where('status', TheaterContract::STATUS_ACTIVE)
            ->whereDate('end_date', '<', today())
            ->get();

        foreach ($contracts as $contract) {
            DB::transaction(function () use ($contract) {
                $contract->update([
                    'status' => TheaterContract::STATUS_EXPIRED,
                    'revoked_at' => now(),
                ]);

                $this->revokeRepresentativeIfNoActiveContract($contract);
            });
        }

        return $contracts->count();
    }

    public function promoteRepresentative(TheaterContract $contract): void
    {
        User::where('id', $contract->representative_user_id)->update([
            'role' => 'moderator',
            'theater_id' => $contract->theater_id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    public function revokeRepresentativeIfNoActiveContract(TheaterContract $contract): void
    {
        $hasActiveContract = TheaterContract::where('representative_user_id', $contract->representative_user_id)
            ->where('id', '!=', $contract->id)
            ->where('status', TheaterContract::STATUS_ACTIVE)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->exists();

        if ($hasActiveContract) {
            return;
        }

        User::where('id', $contract->representative_user_id)
            ->where('role', 'moderator')
            ->where('theater_id', $contract->theater_id)
            ->update([
                'role' => 'user',
                'theater_id' => null,
            ]);
    }

    public function defaultPermissions(): array
    {
        return [
            'Quản lý thông tin rạp được phân quyền',
            'Quản lý phòng chiếu và sơ đồ ghế',
            'Quản lý lịch chiếu trong phạm vi rạp',
            'Quản lý nhân viên quầy của rạp',
            'Quản lý combo, đồ ăn và báo cáo vận hành rạp',
            'Hỗ trợ kiểm tra vé và nghiệp vụ bán vé tại rạp',
        ];
    }

    public function defaultAutoRevokeTerms(): string
    {
        return 'Khi hợp đồng hết hiệu lực, hệ thống CineHub tự động thu hồi quyền Admin rạp của Đại diện rạp và chuyển tài khoản về Người dùng nếu Super Admin chưa gia hạn hợp đồng mới còn hiệu lực.';
    }

    private function generateContractCode(): string
    {
        do {
            $code = 'CH-HD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (TheaterContract::where('contract_code', $code)->exists());

        return $code;
    }

    private function initialStatus(string $startDate, string $endDate): string
    {
        if ($endDate < today()->toDateString()) {
            return TheaterContract::STATUS_EXPIRED;
        }

        if ($startDate <= today()->toDateString()) {
            return TheaterContract::STATUS_ACTIVE;
        }

        return TheaterContract::STATUS_PENDING;
    }

    private function logoPath(): ?string
    {
        $paths = [
            public_path('storage/data/img/avt_web.png'),
            public_path('storage/data/img/avt_web.jpg'),
            public_path('favicon.ico'),
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Gửi email thông báo cho hợp đồng sắp hết hạn trong $withinDays ngày tới.
     */
    public function notifyExpiringContracts(int $withinDays = 7): int
    {
        $contracts = TheaterContract::with(['theater', 'representative', 'superAdmin'])
            ->where('status', TheaterContract::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', today())
            ->whereDate('end_date', '<=', today()->addDays($withinDays))
            ->get();

        $notified = 0;

        foreach ($contracts as $contract) {
            $daysLeft = (int) today()->diffInDays($contract->end_date, false);
            if ($daysLeft < 0) {
                continue;
            }

            $recipients = collect();

            if ($contract->representative && $contract->representative->email) {
                $recipients->push($contract->representative->email);
            }

            if ($contract->superAdmin && $contract->superAdmin->email) {
                $recipients->push($contract->superAdmin->email);
            }

            $recipients = $recipients->unique()->filter();

            if ($recipients->isEmpty()) {
                continue;
            }

            try {
                Mail::to($recipients->all())
                    ->send(new ContractExpiringMail($contract, $daysLeft));
                $notified++;

                Log::info("Đã gửi email thông báo hợp đồng sắp hết hạn: {$contract->contract_code}, còn {$daysLeft} ngày.");
            } catch (\Throwable $e) {
                Log::error("Lỗi gửi email thông báo hợp đồng {$contract->contract_code}: " . $e->getMessage());
            }
        }

        return $notified;
    }

    /**
     * Thống kê hợp đồng cho dashboard.
     */
    public function getStatistics(): array
    {
        $total = TheaterContract::count();
        $active = TheaterContract::where('status', TheaterContract::STATUS_ACTIVE)->count();
        $pending = TheaterContract::where('status', TheaterContract::STATUS_PENDING)->count();
        $expired = TheaterContract::where('status', TheaterContract::STATUS_EXPIRED)->count();
        $renewed = TheaterContract::where('status', TheaterContract::STATUS_RENEWED)->count();

        $expiringSoon = TheaterContract::where('status', TheaterContract::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', today())
            ->whereDate('end_date', '<=', today()->addDays(7))
            ->count();

        $recentContracts = TheaterContract::with(['theater', 'representative'])
            ->latest()
            ->take(5)
            ->get();

        return compact('total', 'active', 'pending', 'expired', 'renewed', 'expiringSoon', 'recentContracts');
    }
}
