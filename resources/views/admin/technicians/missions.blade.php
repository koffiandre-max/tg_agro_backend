@extends('layouts.app')

@section('title', 'Missions du Technicien - ' . ($technician->user->name ?? 'Technicien'))
@section('page-title', 'Missions du Technicien')

@section('content')
@php
    $colors = ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b'];
    $profileColor = $colors[abs(crc32($technician->user->name ?? 'T')) % count($colors)];
@endphp

{{-- Header --}}
<div class="mb-8">
    <a href="{{ route('admin.technicians.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour aux techniciens
    </a>
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-white shadow-sm flex items-center justify-center border-4 border-gray-100">
            <span class="text-xl font-bold" style="color: {{ $profileColor }}">
                {{ strtoupper(substr($technician->user->name ?? 'T', 0, 1)) }}
            </span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $technician->user->name ?? 'Technicien' }}</h1>
            <p class="mt-1 text-gray-500">Liste des missions assignées à ce technicien</p>
        </div>
    </div>
</div>

{{-- Datatable des missions --}}
@livewire('technician-missions-table', ['technicianId' => $technician->id])
@endsection