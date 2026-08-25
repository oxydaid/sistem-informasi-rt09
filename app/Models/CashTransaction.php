<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_account_id',
        'cash_category_id',
        'type', // 'income' or 'expense'
        'amount',
        'transaction_date',
        'description',
        'reference_number',
        'attachment_path',
        'status', // 'confirmed' or 'void'
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
