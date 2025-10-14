<?php

$addOnOf = 'tabletrack';

return [
    'name' => 'Kiosk',
    'verification_required' => false,
    'envato_item_id' => 59978598,
    'parent_envato_id' => 55116396, // TableTrack Envato ID
    'parent_min_version' => '1.2.44',
    'script_name' => $addOnOf . '-kiosk-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Kiosk\Entities\KioskGlobalSetting::class,
];
