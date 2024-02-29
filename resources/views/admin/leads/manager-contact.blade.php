@extends('layouts.app_livewire')

@section('title') Arrange a meeting @endsection

@section('content')
    @livewire('lead-manager-contact', ['lead_id' => $id])
@endsection
