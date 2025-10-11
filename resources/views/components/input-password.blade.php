@props(['disabled' => false, 'autocomplete' => true])

<div class="relative">
    <input
        {{ $disabled ? 'disabled' : '' }}
        type="password"
        autocomplete="{{ $autocomplete ? 'new-password' : 'off' }}"
        {!! $attributes->merge(['class' => 'password border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-gray-500 dark:focus:border-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 rounded-md shadow-sm']) !!}
    />
    <button
        type="button"
        class="toggle-password absolute inset-y-0 rtl:left-0 rtl:pl-3 ltr:right-0 ltr:pr-3 flex items-center text-gray-600"
    >
        <!-- Eye Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <!-- Eye Slash Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="eye-slash-icon h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.944-9.543-7a10.033 10.033 0 012.957-4.558m2.556-2.557A10.05 10.05 0 0112 5c4.478 0 8.27 2.944 9.543 7-.275.877-.681 1.693-1.2 2.422m-2.058 2.065A10.05 10.05 0 0112 19a10.05 10.05 0 01-6.473-2.464M3 3l18 18"/></svg>
    </button>
</div>


