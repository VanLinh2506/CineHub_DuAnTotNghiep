<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use App\Services\TheaterContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TheaterContractController extends Controller
{
    public function __construct(private TheaterContractService $contracts)
    {
    }

    public function index(Request $request)
    {
        $query = TheaterContract::with(['theater', 'representative', 'superAdmin'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('contract_code', 'like', "%{$search}%")
                    ->orWhereHas('theater', fn ($theater) => $theater->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('representative', fn ($user) => $user->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $contracts = $query->paginate(15)->withQueryString();
        $stats = $this->contracts->getStatistics();

        return view('admin.contracts.index', compact('contracts', 'stats'));
    }

    public function create()
    {
        return view('admin.contracts.create', [
            'contract' => null,
            'theaters' => Theater::where('is_active', 1)->orderBy('name')->get(),
            'permissions' => $this->contracts->defaultPermissions(),
            'terms' => $this->contracts->defaultAutoRevokeTerms(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $this->ensureNoConflictingContract($data['theater_id'], $data['representative_user_id']);

        $data['super_admin_id'] = Auth::id();

        $contract = $this->contracts->createContract($data);

        return redirect()
            ->route('admin.contracts.show', $contract)
            ->with('success', 'Đã tạo hợp đồng và sinh PDF tự động.');
    }

    public function show(TheaterContract $contract)
    {
        $contract->load(['theater', 'representative', 'superAdmin', 'renewals']);

        return view('admin.contracts.show', compact('contract'));
    }

    public function renew(Request $request, TheaterContract $contract)
    {
        $data = $this->validatedData($request);
        $this->ensureNoConflictingContract(
            $data['theater_id'],
            $data['representative_user_id'],
            $contract->id
        );

        $data['super_admin_id'] = Auth::id();
        $data['renewed_from_id'] = $contract->id;

        $newContract = $this->contracts->createContract($data);

        return redirect()
            ->route('admin.contracts.show', $newContract)
            ->with('success', 'Đã gia hạn hợp đồng và sinh PDF mới.');
    }

    public function download(TheaterContract $contract)
    {
        if (!$contract->pdf_path || !Storage::disk('public')->exists($contract->pdf_path)) {
            $this->contracts->generatePdf($contract);
            $contract->refresh();
        }

        return Storage::disk('public')->download(
            $contract->pdf_path,
            $contract->contract_code . '.pdf'
        );
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'theater_id' => ['required', 'exists:theaters,id'],
            'representative_user_id' => ['nullable', 'exists:users,id'],
            'representative_email' => ['required_without:representative_user_id', 'nullable', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'admin_permissions' => ['nullable', 'array'],
            'admin_permissions.*' => ['nullable', 'string', 'max:255'],
            'auto_revoke_terms' => ['nullable', 'string'],
            'super_admin_signature' => ['nullable', 'string', 'max:255'],
            'representative_signature' => ['nullable', 'string', 'max:255'],
            'contract_code' => ['nullable', 'string', 'max:50', Rule::unique('theater_contracts', 'contract_code')],
        ]);

        if (empty($data['representative_user_id'])) {
            $email = trim((string) ($data['representative_email'] ?? ''));
            $representative = User::where('email', $email)->first();

            if (!$representative) {
                throw ValidationException::withMessages([
                    'representative_email' => 'Không tìm thấy người dùng với email này.',
                ]);
            }

            if ($representative->role !== 'user' || !empty($representative->theater_id)) {
                throw ValidationException::withMessages([
                    'representative_email' => 'Người dùng này không hợp lệ để làm đại diện rạp hoặc đã thuộc một rạp khác.',
                ]);
            }

            $data['representative_user_id'] = $representative->id;
        }

        unset($data['representative_email']);

        $data['admin_permissions'] = collect($data['admin_permissions'] ?? $this->contracts->defaultPermissions())
            ->filter()
            ->values()
            ->all();

        return $data;
    }

    private function ensureNoConflictingContract(int $theaterId, int $representativeUserId, ?int $ignoreId = null): void
    {
        $query = TheaterContract::whereIn('status', [
            TheaterContract::STATUS_PENDING,
            TheaterContract::STATUS_ACTIVE,
        ])->where(function ($q) use ($theaterId, $representativeUserId) {
            $q->where('theater_id', $theaterId)
                ->orWhere('representative_user_id', $representativeUserId);
        });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'theater_id' => 'Rạp hoặc đại diện này đang có hợp đồng còn hiệu lực/chờ hiệu lực.',
            ]);
        }
    }
}
