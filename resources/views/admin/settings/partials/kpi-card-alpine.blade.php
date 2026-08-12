{{-- Carte KPI réutilisable côté Alpine (x-for) --}}
<div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400" x-text="feature.label"></p>
            <p class="text-3xl font-black text-slate-900 mt-2" x-text="feature.count"></p>
        </div>
        <div class="h-11 w-11 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center border border-yellow-100/50">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon"/>
            </svg>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
        <span class="inline-flex items-center gap-0.5 font-bold px-1.5 py-0.5 rounded-md"
              :class="feature.trend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50'">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round"
                      :d="feature.trend >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 13l-7 7-7-7m7 7V3'"/>
            </svg>
            <span x-text="(feature.trend > 0 ? '+' : '') + feature.trend + '%'"></span>
        </span>
        <span class="text-slate-400">vs mois dernier</span>
    </div>
</div>
