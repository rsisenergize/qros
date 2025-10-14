@extends('layouts.app')

@section('content')

<livewire:inventory::supplier.supplier-details :supplier="$supplier" />

@endsection