@extends('layouts.guest')

@section('content')

@livewire('shop.addresses', ["shopBranch" => $shopBranch])

@endsection
