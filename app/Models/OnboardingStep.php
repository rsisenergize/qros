<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class OnboardingStep extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];
}
