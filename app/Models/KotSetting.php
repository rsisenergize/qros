<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KotSetting extends Model
{
    use HasBranch;

    protected $guarded = ['id'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
