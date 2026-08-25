<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Organization;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function __construct(
        protected FinanceService $financeService,
        protected FileUploadService $fileUploadService,
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $orgId = $request->query('org_id') ?: ($user->organization_id ?: Organization::first()?->id);

        if (! $user->canAccessOrganization($orgId)) {
            abort(403, 'Akses ditolak.');
        }

        $organizations = $user->isSuperAdmin() ? Organization::all() : Organization::where('id', $orgId)->get();

        $accounts = CashAccount::where('organization_id', $orgId)->where('is_active', true)->get();
        $categories = CashCategory::where('organization_id', $orgId)->where('is_active', true)->get();

        $accountId = $request->query('account_id') ?: $accounts->first()?->id;
        $type = $request->query('type');
        $categoryId = $request->query('category_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transactions = CashTransaction::where('cash_account_id', $accountId)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($categoryId, fn ($q) => $q->where('cash_category_id', $categoryId))
            ->when($startDate, fn ($q) => $q->where('transaction_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('transaction_date', '<=', $endDate))
            ->with(['category', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalIncome = CashTransaction::where('cash_account_id', $accountId)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->when($startDate, fn ($q) => $q->where('transaction_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('transaction_date', '<=', $endDate))
            ->sum('amount');

        $totalExpense = CashTransaction::where('cash_account_id', $accountId)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->when($startDate, fn ($q) => $q->where('transaction_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('transaction_date', '<=', $endDate))
            ->sum('amount');

        $activeAccount = CashAccount::find($accountId);

        return Inertia::render('admin/finance/Index', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'accounts' => $accounts,
            'activeAccount' => $activeAccount,
            'categories' => $categories,
            'transactions' => $transactions,
            'summary' => [
                'current_balance' => (float) ($activeAccount?->current_balance ?? 0),
                'filtered_income' => (float) $totalIncome,
                'filtered_expense' => (float) $totalExpense,
            ],
            'filters' => [
                'account_id' => $accountId ? (int) $accountId : null,
                'type' => $type,
                'category_id' => $categoryId ? (int) $categoryId : null,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cash_account_id' => ['required', 'exists:cash_accounts,id'],
            'cash_category_id' => ['required', 'exists:cash_categories,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:500'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ]);

        $account = CashAccount::findOrFail($validated['cash_account_id']);
        if (! $request->user()->canAccessOrganization($account->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->hasFile('receipt')) {
            $validated['attachment_path'] = $this->fileUploadService->uploadReceipt($request->file('receipt'));
        }

        $transaction = $this->financeService->recordTransaction($validated, $request->user()->id);

        $this->auditLogService->log('created', 'CashTransaction', $transaction->id, null, $transaction->toArray());

        return back()->with('success', 'Transaksi keuangan berhasil dicatat.');
    }

    public function voidTransaction(Request $request, CashTransaction $transaction): RedirectResponse
    {
        $account = $transaction->account;
        if (! $request->user()->canAccessOrganization($account->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($transaction->status === 'void') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        $oldValues = $transaction->toArray();
        $this->financeService->voidTransaction($transaction->id, $request->user()->id);

        $this->auditLogService->log('voided', 'CashTransaction', $transaction->id, $oldValues, $transaction->fresh()->toArray());

        return back()->with('success', 'Transaksi berhasil dibatalkan (void). Saldo telah diperbarui.');
    }

    public function categories(Request $request): Response
    {
        $user = $request->user();
        $orgId = $request->query('org_id') ?: ($user->organization_id ?: Organization::first()?->id);

        if (! $user->canAccessOrganization($orgId)) {
            abort(403, 'Akses ditolak.');
        }

        $organizations = $user->isSuperAdmin() ? Organization::all() : Organization::where('id', $orgId)->get();
        $categories = CashCategory::where('organization_id', $orgId)
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/finance/Categories', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'categories' => $categories,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
        ]);

        if (! $request->user()->canAccessOrganization($validated['organization_id'])) {
            abort(403, 'Akses ditolak.');
        }

        $category = CashCategory::create($validated);

        $this->auditLogService->log('created', 'CashCategory', $category->id, null, $category->toArray());

        return back()->with('success', 'Kategori transaksi berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, CashCategory $category): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($category->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $category->toArray();
        $category->update($validated);

        $this->auditLogService->log('updated', 'CashCategory', $category->id, $oldValues, $category->fresh()->toArray());

        return back()->with('success', 'Kategori transaksi berhasil diperbarui.');
    }

    public function destroyCategory(Request $request, CashCategory $category): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($category->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena sudah memiliki riwayat transaksi. Anda dapat menonaktifkannya.');
        }

        $oldValues = $category->toArray();
        $category->delete();

        $this->auditLogService->log('deleted', 'CashCategory', $category->id, $oldValues, null);

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
