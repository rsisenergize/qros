@extends('layouts.app')

@section('content')

@livewire('kitchens', ['kotPlace' => $kot])
@endsection
