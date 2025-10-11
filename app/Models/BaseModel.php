<?php

namespace App\Models;

use Froiden\RestAPI\ApiModel;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = parent::newFromBuilder($attributes, $connection);

        foreach ($model->getAttributes() as $key => $value) {
            // Convert integer-like strings to integers
            if (is_string($value) && ctype_digit($value)) {
                $model->setAttribute($key, (int) $value);
            }
        }

        return $model;
    }
}
