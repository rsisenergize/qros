@extends('layouts.app')

@section('content')

@livewire('kot.kots', ['kotPlace' => null, 'showAllKitchens' => true])

@endsection
