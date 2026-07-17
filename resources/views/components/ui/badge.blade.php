{{--
  <x-ui.badge> — Badge statut sémantique

  Props:
    status  : string — auto-mappe vers une couleur sémantique (paid, draft, overdue, sent…)
    variant : success | warning | danger | info | primary | default — couleur explicite
    dot     : bool — affiche un point coloré avant le texte
    size    : xs | sm | md  (défaut: sm)

  Exemples:
    <x-ui.badge status="paid">Payée</x-ui.badge>
    <x-ui.badge status="overdue">En retard</x-ui.badge>
    <x-ui.badge variant="info" dot>En cours</x-ui.badge>
    <x-ui.badge variant="primary" size="md">Nouveau</x-ui.badge>
--}}

@props([
    'status'  => null,
    'variant' => null,
    'dot'     => false,
    'size'    => 'sm',
])

@php
// Mapping statuts → variant sémantique
$statusMap = [
    // success
    'paid'        => 'success',
    'active'      => 'success',
    'approved'    => 'success',
    'posted'      => 'success',
    'confirmed'   => 'success',
    'completed'   => 'success',
    'done'        => 'success',
    // warning
    'pending'     => 'warning',
    'draft'       => 'warning',
    'partial'     => 'warning',
    'on_hold'     => 'warning',
    // info
    'sent'        => 'info',
    'viewed'      => 'info',
    'processing'  => 'info',
    'converted'   => 'info',
    'in_progress' => 'info',
    // danger
    'overdue'     => 'danger',
    'rejected'    => 'danger',
    'void'        => 'danger',
    'inactive'    => 'danger',
    'cancelled'   => 'danger',
    'expired'     => 'danger',
    'failed'      => 'danger',
    // primary
    'new'         => 'primary',
    'open'        => 'primary',
];

$variantStyles = [
    'success' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
    'warning' => 'bg-amber-100   text-amber-700   border border-amber-200',
    'danger'  => 'bg-red-100     text-red-700     border border-red-200',
    'info'    => 'bg-blue-100    text-blue-700    border border-blue-200',
    'primary' => 'bg-indigo-100  text-indigo-700  border border-indigo-200',
    'default' => 'bg-gray-100    text-gray-600    border border-gray-200',
];

$dotColors = [
    'success' => 'bg-emerald-500',
    'warning' => 'bg-amber-500',
    'danger'  => 'bg-red-500',
    'info'    => 'bg-blue-500',
    'primary' => 'bg-indigo-500',
    'default' => 'bg-gray-400',
];

$sizes = [
    'xs' => 'px-1.5 py-0.5 text-[10px] leading-tight rounded gap-1',
    'sm' => 'px-2    py-0.5 text-xs     leading-tight rounded-md gap-1.5',
    'md' => 'px-2.5  py-1   text-sm     leading-tight rounded-md gap-1.5',
];

$resolvedVariant = $variant ?? ($status ? ($statusMap[$status] ?? 'default') : 'default');
$style           = $variantStyles[$resolvedVariant] ?? $variantStyles['default'];
$dotColor        = $dotColors[$resolvedVariant] ?? $dotColors['default'];
$sizeClass       = $sizes[$size] ?? $sizes['sm'];

$classes = 'inline-flex items-center font-medium ' . $style . ' ' . $sizeClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    @endif
    {{ $slot }}
</span>
