<?php

namespace App\Models;

use App\Traits\HasBranch;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Menu extends BaseModel
{
    use HasFactory;
    use HasBranch;
    use HasTranslations;

    protected $guarded = ['id'];
    public $translatable = ['menu_name'];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
