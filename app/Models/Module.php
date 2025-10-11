<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Permission\Models\Permission;
use App\Models\BaseModel;
use Illuminate\Support\Facades\File;

class Module extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_modules');
    }

    public static function validateVersion($module)
    {
        if (app()->runningInConsole()) {
            return true;
        }

        $parentMinVersion = config(strtolower($module) . '.parent_min_version');

        if ($parentMinVersion >= File::get('version.txt')) {

            $module = \Nwidart\Modules\Facades\Module::findOrFail(strtolower($module));
            /* @phpstan-ignore-line */
            $module->disable();

            $message = 'To activate <strong>' . $module . '</strong> module, minimum version of <b>tabletrack application</b> must be greater than equal to <b>' . $parentMinVersion . '</b> But your application version is <b>' . File::get('version.txt') . '</b>. Please upgrade the application to latest version';
            throw new \Exception($message);
        }
    }
}
