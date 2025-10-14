<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Entities\InventoryItem;
use App\Models\Branch;
use App\Models\User;
// use Modules\Inventory\Database\Factories\InventoryMovementFactory;

class InventoryMovement extends Model
{
    use HasFactory;
    use HasBranch;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'inventory_item_id',
        'quantity',
        'transaction_type',
        'waste_reason',
        'added_by',
        'unit_purchase_price',
        'expiration_date',
        'supplier_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'expiration_date' => 'date'
    ];



    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by')->withoutGlobalScopes();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transferBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'transfer_branch_id');
    }

    // Add constants for transaction types
    const TRANSACTION_TYPE_ORDER_USED = 'out';
    const TRANSACTION_TYPE_STOCK_ADDED = 'in';
    const TRANSACTION_TYPE_WASTE = 'waste';
    const TRANSACTION_TYPE_TRANSFER = 'transfer';
}
