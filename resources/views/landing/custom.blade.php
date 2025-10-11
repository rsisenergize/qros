@extends('layouts.landing')
@section('content')
@livewire('new-custom-pages' , ['slug' => $slug])
@endsection
