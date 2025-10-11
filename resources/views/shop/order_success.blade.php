@extends('layouts.guest')

@section('content')

@livewire('shop.orderDetail', [
    'id' => $id,
    'restaurant' => $restaurant,
    'shopBranch' => $shopBranch,
])
    
@endsection