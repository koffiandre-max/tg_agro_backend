@extends('layouts.app')

@section('title', 'Créer un Rapport')
@section('page-title', 'Créer un Rapport')

@section('content')
    @include('livewire.report-form', ['formAction' => $formAction, 'farms' => $farms, 'clients' => $clients])
@endsection