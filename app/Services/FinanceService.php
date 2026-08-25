<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FinanceService
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Record a new cash transaction within an atomic DB transaction.
     */
    public function recordTransaction(array $data, ?int $userId = null): CashTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $account = CashAccount::lockForUpdate()->findOrFail($data['cash_account_id']);
            $category = CashCategory::findOrFail($data['cash_category_id']);

            // Security check: category organization must match account organization
            if ((int) $account->organization_id !== (int) $category->organization_id) {
                throw new InvalidArgumentException('Kategori transaksi tidak cocok dengan organisasi kas.');
            }

            $amount = (float) $data['amount'];
            if ($amount <= 0) {
                throw new InvalidArgumentException('Nominal transaksi harus lebih besar dari 0.');
            }

            $type = $data['type'] ?? $category->type;

            $transaction = CashTransaction::create([
                'cash_account_id' => $account->id,
                'cash_category_id' => $category->id,
                'type' => $type,
                'amount' => $amount,
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'],
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'status' => 'confirmed',
                'created_by_user_id' => $userId,
            ]);

            $this->recalculateBalance($account->id);

            return $transaction;
        });
    }

    /**
     * Void a transaction safely without hard-deleting the audit trail.
     */
    public function voidTransaction(int $transactionId, ?int $userId = null): CashTransaction
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = CashTransaction::lockForUpdate()->findOrFail($transactionId);
            $transaction->update(['status' => 'void']);

            $this->recalculateBalance($transaction->cash_account_id);

            return $transaction;
        });
    }

    /**
     * Recalculate cash account balance accurately from confirmed transactions.
     */
    public function recalculateBalance(int $accountId): float
    {
        $account = CashAccount::lockForUpdate()->findOrFail($accountId);

        $income = CashTransaction::where('cash_account_id', $accountId)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->sum('amount');

        $expense = CashTransaction::where('cash_account_id', $accountId)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->sum('amount');

        $currentBalance = (float) ($income - $expense);

        $account->update(['current_balance' => $currentBalance]);

        return $currentBalance;
    }
}
