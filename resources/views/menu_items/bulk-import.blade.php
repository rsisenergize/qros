@extends('layouts.app')

@section('content')

@livewire('menu.bulk-import-page')

@push('styles')
    <link href="{{ asset('css/bulk-upload.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        // Additional scripts for bulk import functionality can be added here
    </script>
@endpush

@endsection
