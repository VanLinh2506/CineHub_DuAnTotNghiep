<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use App\Services\TheaterContractService;
use App\Services\ContractPdfExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TheaterContractController extends Controller
{
    public function __construct(
        private TheaterContractService $contracts,
        private ContractPdfExtractor $pdfExtractor,
    )
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
            'partyTerms' => $this->contracts->defaultPartyTerms(),
        ]);
    }

    public function store(Request $request)
    {
        $contract = DB::transaction(function () use ($request) {
            $data = $this->validatedData($request);
            $this->ensureNoConflictingContract($data['theater_id'], $data['representative_user_id']);
            $data['super_admin_id'] = Auth::id();

            if ($request->hasFile('contract_pdf')) {
                $data['source_pdf_path'] = $request->file('contract_pdf')->store('contracts/source', 'public');
                $data['extracted_text'] = $this->pdfExtractor
                    ->extract(Storage::disk('public')->path($data['source_pdf_path']))['text'] ?? null;
            }

            return $this->contracts->createContract($data);
        });

        return redirect()
            ->route('admin.contracts.show', $contract)
            ->with('success', 'Đã tạo hợp đồng và sinh PDF tự động.');
    }

    public function extractPdf(Request $request)
    {
        $request->validate(['contract_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240']]);
        $result = $this->pdfExtractor->extract($request->file('contract_pdf')->getRealPath());

        if (!empty($result['theater_name'])) {
            $theater = Theater::where('name', 'like', '%' . trim($result['theater_name']) . '%')->first();
            $result['theater_id'] = $theater?->id;
        }

        return response()->json($result);
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
            'representative_email' => ['required_unless:create_admin,1', 'nullable', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'bestseller_price_min' => ['required', 'integer', 'min:0'],
            'bestseller_price_max' => ['required', 'integer', 'gte:bestseller_price_min'],
            'new_release_price_min' => ['required', 'integer', 'min:0'],
            'new_release_price_max' => ['required', 'integer', 'gte:new_release_price_min'],
            'hot_movie_price_min' => ['required', 'integer', 'min:0'],
            'hot_movie_price_max' => ['required', 'integer', 'gte:hot_movie_price_min'],
            'admin_permissions' => ['nullable', 'array'],
            'admin_permissions.*' => ['nullable', 'string', 'max:255'],
            'auto_revoke_terms' => ['nullable', 'string'],
            'party_terms' => ['nullable', 'string'],
            'super_admin_signature' => ['nullable', 'string', 'max:255'],
            'representative_signature' => ['nullable', 'string', 'max:255'],
            'contract_code' => ['nullable', 'string', 'max:50', Rule::unique('theater_contracts', 'contract_code')],
            'contract_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'create_admin' => ['nullable', 'boolean'],
            'admin_name' => ['required_if:create_admin,1', 'nullable', 'string', 'max:255'],
            'admin_email' => ['required_if:create_admin,1', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($request->boolean('create_admin')) {
            $plainPassword = $data['admin_password'] ?: Str::password(12);
            $representative = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($plainPassword),
                'role' => 'user',
                'is_active' => true,
                'status' => 'active',
            ]);
            $data['representative_user_id'] = $representative->id;
            session()->flash('created_admin_credentials', [
                'email' => $representative->email,
                'password' => $plainPassword,
            ]);
        }

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

        unset($data['representative_email'], $data['contract_pdf'], $data['create_admin'], $data['admin_name'], $data['admin_email'], $data['admin_password']);

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
