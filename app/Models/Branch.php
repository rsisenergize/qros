<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasRestaurant;
use App\Traits\GeneratesQrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;
use App\Models\OrderType;
use Spatie\LaravelPackageTools\Concerns\Package\HasServiceProviders;

class Branch extends BaseModel
{
    use HasFactory;
    use GeneratesQrCode;
    use HasRestaurant;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'restaurant_id',
        'is_active',
        'unique_hash',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function getQrCodeFileName(): string
    {
        return 'qrcode-branch-' . $this->id . '-' . $this->restaurant->id . '.png';
    }

    public function getRestaurantId(): int
    {
        return $this->restaurant_id;
    }

    public function generateQrCode()
    {
        // Generate a new unique_hash to invalidate old QR code links
        $this->generateUniqueHash();
        $this->save();
        
        // $this->createQrCode(route('table_order', [$this->getRestaurantId()]) . '?branch=' . $this->id);
        $this->createQrCode(route('table_order', [$this->restaurant_id]) . '?branch=' . $this->unique_hash . '&hash=' . $this->restaurant->hash . '&from_qr=1');
    }

    public function deliverySetting()
    {
        return $this->hasOne(BranchDeliverySetting::class, 'branch_id');
    }

    public function deliveryFeeTiers()
    {
        return $this->hasMany(DeliveryFeeTier::class);
    }

    public function qRCodeUrl(): Attribute
    {
        return Attribute::get(fn(): string => asset_url_local_s3('qrcodes/' . $this->getQrCodeFileName()));
    }

    public function printerSettings(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    public function kotPlaces(): HasMany
    {
        return $this->hasMany(KotPlace::class);
    }

    public function orderPlaces(): HasMany
    {
        return $this->hasMany(MultipleOrder::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function kotSetting(): HasOne
    {
        return $this->hasOne(KotSetting::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class)->withoutGlobalScopes();
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class)->withoutGlobalScopes();
    }

    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class)->withoutGlobalScopes();
    }

    public function orderTypes()
    {
        return $this->hasMany(OrderType::class)->withoutGlobalScopes();
    }

    public function generateKotSetting()
    {
        $this->kotSetting()->create([
            'branch_id' => $this->id,
            'default_status' => 'pending',
            'enable_item_level_status' => true,
        ]);
    }

    /**
     * Generate a unique hash for this branch
     */
    public function generateUniqueHash()
    {
        $baseString = $this->id . '_' . ($this->name ?? 'branch') . '_' . time();
        $this->unique_hash = substr(hash('sha256', $baseString), 0, 20);
    }
}
