<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;
// use Modules\Inventory\Database\Factories\UnitFactory;

class Unit extends Model
{
    use HasFactory;
    use HasBranch;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    /**
     * The units that are available.
     */
    const UNITS = [
        ['name' => 'Kilogram', 'symbol' => 'kg'],
        ['name' => 'Gram', 'symbol' => 'g'],
        ['name' => 'Liter', 'symbol' => 'L'],
        ['name' => 'Milliliter', 'symbol' => 'ml'],
        ['name' => 'Piece', 'symbol' => 'pc'],
        ['name' => 'Box', 'symbol' => 'box'],
        ['name' => 'Dozen', 'symbol' => 'dz'],
        ['name' => 'Bottle', 'symbol' => 'btl'],
        ['name' => 'Package', 'symbol' => 'pkg'],
        ['name' => 'Can', 'symbol' => 'can'],
    ];
}
