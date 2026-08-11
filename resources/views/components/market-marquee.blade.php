@php
    $prices = \App\Models\MarketPrice::orderBy('product_name')->get();
@endphp

@if($prices->isNotEmpty())
    <div class="market-marquee group relative overflow-hidden  bg-red-600">
        {{-- Dégradés de masquage aux extrémités --}}
        {{-- <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-12 bg-gradient-to-r from-emerald-600 to-transparent"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-12 bg-gradient-to-l from-emerald-600 to-transparent"></div> --}}

        <div class="flex w-max animate-marquee items-center gap-8 py-2 group-hover:[animation-play-state:paused]">
            {{-- Deux passages pour une boucle continue --}}
            @foreach([0, 1] as $pass)
                @foreach($prices as $price)
                    <div class="flex shrink-0 items-center gap-2 text-white">
                        <span class="flex size-1.5 rounded-full bg-white/80"></span>
                        <span class="text-sm font-medium">{{ $price->product_name }}</span>
                        <span class="text-sm font-bold">
                            {{ number_format($price->price_per_unit, 0, ',', ' ') }}
                            <span class="text-xs font-normal text-white/70">{{ $price->currency ?? 'FCFA' }}/{{ $price->unit ?? 'kg' }}</span>
                        </span>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endif
