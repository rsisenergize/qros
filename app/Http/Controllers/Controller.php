<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;


abstract class Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __construct()
    {
        $this->checkMigrateStatus();

        if (session('locale')) {
            App::setLocale(session('locale'));
        } else {
            $user = auth()->user();

            if (isset($user)) {

                App::setLocale($user?->locale ?? 'en');
            } else {
                try {

                    App::setLocale(session('locale') ?? global_setting()?->locale);
                } catch (\Exception $e) {
                    App::setLocale('en');
                }
            }
        }
    }

    public function checkMigrateStatus()
    {
        return check_migrate_status();
    }
}
