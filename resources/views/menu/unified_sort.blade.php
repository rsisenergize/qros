@extends('layouts.app')

@section('content')

@livewire('menu.sorting-component')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
@endpush

@endsection
