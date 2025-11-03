<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use App\Enums\OrderStatus;
use App\Models\OrderCharge;
use App\Scopes\BranchScope;
use App\Models\DeliveryExecutive;
use App\Models\OrderNumberSetting;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];
    protected $appends = ['show_formatted_order_number'];

    protected $casts = [
        'date_time' => 'datetime',
        'order_status' => OrderStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid ??= (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class)->withoutGlobalScope(BranchScope::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(OrderTax::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(OrderCharge::class);
    }

    public function extraCharges(): BelongsToMany
    {
        return $this->belongsToMany(RestaurantCharge::class, 'order_charges', 'order_id', 'charge_id');
    }

    public function kot(): HasMany
    {
        return $this->hasMany(Kot::class)->where('status', '!=', 'cancelled');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function splitOrders(): HasMany
    {
        return $this->hasMany(SplitOrder::class, 'order_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withoutGlobalScopes();
    }

    public function deliveryExecutive(): BelongsTo
    {
        return $this->belongsTo(DeliveryExecutive::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function cancelReason(): BelongsTo
    {
        return $this->belongsTo(KotCancelReason::class, 'cancel_reason_id');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function deliveryApp(): BelongsTo
    {
        return $this->belongsTo(DeliveryPlatform::class, 'delivery_app_id');
    }

    public function deliveryPlatform(): BelongsTo
    {
        return $this->deliveryApp();
    }

    public static function generateOrderNumber($branch)
    {
        // Check if order number settings exist and feature is enabled
        $settings = OrderNumberSetting::where('branch_id', $branch->id)->first();

        if ($settings && $settings->enable_feature) {
            return self::generateFormattedOrderNumber($branch->id, $settings);
        }

        $lastOrder = Order::where('branch_id', $branch->id)->latest()->first();
        $orderNumber = $lastOrder ? ((int)$lastOrder->order_number + 1) : 1;

        return [
            'order_number' => $orderNumber,
            'formatted_order_number' => ($settings && $settings->enable_feature) ? (string) $orderNumber : null
        ];
    }

    private static function generateFormattedOrderNumber($branchId, $settings)
    {
        $currentTime = now(restaurant()->timezone ?? 'UTC');

        // Determine the next order number
        $orderQuery = Order::where('branch_id', $branchId);

        if ($settings->reset_daily) {
            // Reset counter daily - only consider orders from today
            $orderQuery->whereDate('created_at', $currentTime->toDateString());
        }

        $lastOrder = $orderQuery->latest()->first();
        $nextNumber = $lastOrder ? ((int)$lastOrder->order_number + 1) : 1;

        // Check if the order number already exists (to avoid duplicates)
        do {
            $exists = Order::where('branch_id', $branchId)
                ->where('order_number', $nextNumber)
                ->whereDate('created_at', $settings->reset_daily ? $currentTime->toDateString() : '>=' . $currentTime->startOfDay())
                ->exists();

            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        // Generate formatted order number
        $formattedNumber = self::buildFormattedOrderNumber($nextNumber, $settings, $currentTime);

        return [
            'order_number' => $nextNumber,
            'formatted_order_number' => $formattedNumber
        ];
    }


    private static function buildFormattedOrderNumber($orderNumber, $settings, $currentTime)
    {
        $parts = [];

        // Add prefix
        if (!empty($settings->prefix)) {
            $parts[] = $settings->prefix;
        }

        // Add date components if enabled
        if ($settings->include_date) {
            $dateParts = [];

            if ($settings->show_year) {
                $dateParts[] = $currentTime->format('Y');
            }

            if ($settings->show_month) {
                $dateParts[] = $currentTime->format('m');
            }

            if ($settings->show_day) {
                $dateParts[] = $currentTime->format('d');
            }

            if (!empty($dateParts)) {
                $parts[] = implode('', $dateParts);
            }

            if ($settings->show_time) {
                $parts[] = $currentTime->format('Hi'); // HHMM format
            }
        }

        $paddedNumber = str_pad($orderNumber, $settings->digits, '0', STR_PAD_LEFT);
        $parts[] = $paddedNumber;

        // Join all parts with separator
        return implode($settings->separator, $parts);
    }



    public function getShowFormattedOrderNumberAttribute()
    {

        if (!is_null($this->formatted_order_number)) {
            return $this->formatted_order_number;
        }

        return __('modules.order.orderNumber') . ' #' . $this->order_number;
    }

    public function kiosk(): ?BelongsTo
    {
        if (
            module_enabled('Kiosk') &&
            class_exists(\Modules\Kiosk\Entities\Kiosk::class)
        ) {
            return $this->belongsTo(\Modules\Kiosk\Entities\Kiosk::class);
        }

        return null;
    }
}
