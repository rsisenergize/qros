<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModifierGroupTranslation extends Model
{
    protected $fillable = ['modifier_group_id', 'locale', 'name', 'description'];
    public $timestamps = false;

    protected $table = 'modifier_group_translations';

    public function modifierGroup() : BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class, 'modifier_group_id');
    }


}
