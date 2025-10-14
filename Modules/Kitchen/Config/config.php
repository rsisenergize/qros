<?php

$addOnOf = 'tabletrack';

return [
    'name' => 'Kitchen',
    'verification_required' => true,
    'envato_item_id' => 58623753,
    'parent_envato_id' => 55116396, // TableTrack Envato ID
    'parent_min_version' => '1.2.36',
    'script_name' => $addOnOf . '-kitchen-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Kitchen\Entities\KitchenGlobalSetting::class,
];
