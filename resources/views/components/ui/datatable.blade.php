@props([
    'items',
    'emptyMessage' => 'Aucun élément trouvé',
    'createRoute' => null,
    'createLabel' => 'Ajouter',
    'pagination' => null,
])

@if(isset($filterAction))
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-6">
    {{ $filterAction }}
</div>
@endif

<div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
    @if($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm font-medium">{{ $emptyMessage }}</p>
            @if($createRoute)
                <a href="{{ route($createRoute) }}" class="mt-3 text-sm text-indigo-600 hover:underline">{{ $createLabel }}</a>
            @endif
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-500 border-b border-gray-200">
                <tr>
                    {{ $header ?? '' }}
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{ $rows ?? '' }}
            </tbody>
        </table>
        @if($pagination && method_exists($pagination, 'hasPages') && $pagination->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $pagination->links() }}</div>
        @endif
    @endif
</div>