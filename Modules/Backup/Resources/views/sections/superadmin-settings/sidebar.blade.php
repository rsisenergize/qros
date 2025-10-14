<li class="me-2">
    <a href="{{ route('superadmin.superadmin-settings.index').'?tab=backup&subtab=settings' }}" wire:navigate
    @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'backup'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'backup')])>@lang('backup::app.databaseBackupSettings')</a>
</li>
