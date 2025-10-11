@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300 ltr:text-left rtl:text-right']) }}>
    {{ $value ?? $slot }}
</label>
