@extends('layouts.app_livewire')

@section('title') Edit Lead @endsection

@section('content')
    @livewire('lead-edit', ['lead_id' => $id, 'redirect' => ($redirect ?? 'leads.table')])
@endsection
