<?php

$addOnOf = 'tabletrack';

return [
    'name' => 'Backup',
    'verification_required' => true,
    'envato_item_id' => 59411920,
    'parent_envato_id' => 55116396, // TableTrack Envato ID
    'parent_min_version' => '1.2.41',
    'script_name' => $addOnOf . '-backup-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Backup\Models\DatabaseBackupSetting::class,
];
