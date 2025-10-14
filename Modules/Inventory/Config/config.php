<?php

$addOnOf = 'tabletrack';

return [
    'name' => 'Inventory',
    'verification_required' => true,
    'envato_item_id' => 57274242,
    'parent_envato_id' => 55116396, // TableTrack Envato ID
    'parent_min_version' => '1.2.19',
    'script_name' => $addOnOf . '-inventory-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Inventory\Entities\InventoryGlobalSetting::class,
];
