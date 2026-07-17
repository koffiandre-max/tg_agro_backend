@extends('layouts.app')

@section('page-title', 'Nouveau Rapport')

@section('content')
    @include('livewire.report-form', ['formAction' => $formAction, 'farms' => $farms, 'clients' => $clients])
@endsection