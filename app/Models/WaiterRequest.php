<?php

namespace App\Models;


use App\Models\BaseModel;
use App\Models\Table;
use App\Models\Branch;
use App\Traits\HasBranch;

class WaiterRequest extends BaseModel
{
    use HasBranch;

    protected $fillable = ['table_id', 'branch_id', 'status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
