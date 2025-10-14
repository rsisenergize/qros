<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Branch;
use App\Models\User;

class PurchaseOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generatePoNumber(): void
    {
        $latestPo = PurchaseOrder::orderBy('id', 'desc')->first();
        $number = $latestPo ? intval(substr($latestPo->po_number, 3)) + 1 : 1;
        $this->po_number = 'PO-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
