@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="space-y-6 px-6 pb-12">

    {{-- En-tête Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <span>Agenda</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-900 font-semibold">Missions</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Calendar</h1>
        </div>

        <div class="flex items-center gap-3">
            {{-- Barre de Recherche --}}
            <div class="relative hidden md:block">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 0114 0z"/></svg>
                <input type="text" id="calendarSearch" placeholder="Search" class="pl-9 pr-8 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 w-52 shadow-xs transition">
                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">⌘K</span>
            </div>
        </div>
    </div>

    {{-- Conteneur Calendrier --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 overflow-x-auto">
        <div id="calendar" class="min-w-[700px]"></div>
    </div>
</div>

{{-- Modal Détails --}}
<div id="detailsModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden items-center justify-center z-50 transition-all">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-900">Détails de la Mission</h2>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-4 mb-5">
                <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="detailTitle" class="text-base font-semibold text-slate-900">Titre mission</h3>
                    <span id="detailStatus" class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium">Statut</span>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                    <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002-2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-slate-500">Date</p>
                        <p id="detailDate" class="text-sm font-medium text-slate-900">-</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                    <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.501 4.501 0 00-4.525-6.01 4.501 4.501 0 00-6.01 4.525 9.337 9.337 0 00.952 4.121 9.38 9.38 0 00.372 2.625"/>
                    </svg>
                    <div>
                        <p class="text-xs text-slate-500">Technicien</p>
                        <p id="detailTechnician" class="text-sm font-medium text-slate-900">-</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                    <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-slate-500">Exploitation</p>
                        <p id="detailFarm" class="text-sm font-medium text-slate-900">-</p>
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        <p class="text-xs text-slate-500">Description</p>
                    </div>
                    <p id="detailDescription" class="text-sm text-slate-700 mt-1">Aucune description</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" onclick="closeDetails()" class="px-3.5 py-1.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    .fc {
        font-family: inherit;
        --fc-border-color: #eaecf0;
        width: 100% !important;
    }

    .fc .fc-scrollgrid-sync-table,
    .fc .fc-col-header {
        width: 100% !important;
        table-layout: fixed !important;
    }

    .fc .fc-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem !important;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fc .fc-toolbar-title {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #101828 !important;
    }

    .fc .fc-button-primary {
        background-color: #ffffff !important;
        border: 1px solid #d0d5dd !important;
        color: #344054 !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        border-radius: 8px !important;
        padding: 0.35rem 0.65rem !important;
        box-shadow: 0px 1px 2px rgba(16, 24, 40, 0.05) !important;
        text-transform: capitalize !important;
        transition: all 0.15s ease;
    }
    .fc .fc-button-primary:hover {
        background-color: #f9fafb !important;
        color: #101828 !important;
        border-color: #d0d5dd !important;
    }
    .fc .fc-button-active {
        background-color: #f2f4f7 !important;
        color: #101828 !important;
        border-color: #d0d5dd !important;
    }

    .fc .fc-col-header-cell-cushion {
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        color: #475467 !important;
        padding: 6px 0 !important;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .fc .fc-daygrid-day-number {
        font-size: 0.75rem !important;
        color: #344054 !important;
        font-weight: 500 !important;
        padding: 4px 6px !important;
    }
    .fc .fc-day-today {
        background-color: #f9fafb !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background-color: #101828;
        color: #ffffff !important;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
        padding: 0 !important;
    }

    .fc-daygrid-event {
        margin: 1px 2px !important;
        border-radius: 6px !important;
        border: none !important;
        padding: 2px 4px !important;
    }

    .uui-event-card {
        display: flex;
        align-items: center;
        width: 100%;
        font-size: 0.68rem;
        font-weight: 600;
        line-height: 1.2;
        overflow: hidden;
    }

    .event-status-pending {
        background-color: #b54708 !important;
        color: #fff !important;
        border-left: 3px solid #f79009 !important;
    }
    .event-status-in_progress {
        background-color: #175cd3 !important;
        color: #fff !important;
        border-left: 3px solid #2970ff !important;
    }
    .event-status-completed {
        background-color: #027a48 !important;
        color: #fff !important;
        border-left: 3px solid #12b76a !important;
    }
    .event-status-cancelled {
        background-color: #b42318 !important;
        color: #fff !important;
        border-left: 3px solid #f04438 !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
    let calendar;
    let selectedEventId = null;
    let localMissions = @json($missions);

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            firstDay: 1,
            showNonCurrentDates: false,
            fixedWeekCount: false,
            height: 'auto',
            aspectRatio: 1.7,
            dayMaxEvents: 2,
            buttonText: {
                today: 'Today',
                month: 'Month view',
                list: 'List view'
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                const formattedEvents = localMissions.map(m => ({
                    id: m.id,
                    title: m.title,
                    start: m.start,
                    className: `event-status-${m.status || 'pending'}`,
                    extendedProps: {
                        farm_id: m.farm_id,
                        technician_id: m.technician_id,
                        technician: m.technician || '',
                        farm: m.farm || '',
                        description: m.description,
                        status: m.status,
                        rawTitle: m.title
                    }
                }));
                successCallback(formattedEvents);
            },
            eventContent: function(arg) {
                let title = arg.event.title;
                let tech = arg.event.extendedProps.technician;
                let displayTitle = tech ? `${title} · <span class="opacity-75 font-normal">${tech}</span>` : title;

                return {
                    html: `<div class="uui-event-card">
                             <div class="truncate">${displayTitle}</div>
                           </div>`
                };
            },
            eventClick: (info) => showDetails(info.event),
            dateClick: (info) => openModal(null, info.dateStr)
        });
        calendar.render();

        document.getElementById('calendarSearch')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = localMissions.filter(m =>
                m.title.toLowerCase().includes(query) ||
                (m.technician && m.technician.toLowerCase().includes(query))
            );

            calendar.removeAllEvents();
            calendar.addEventSource(filtered.map(m => ({
                id: m.id,
                title: m.title,
                start: m.start,
                className: `event-status-${m.status || 'pending'}`,
                extendedProps: { ...m, rawTitle: m.title }
            })));
        });
    });

    function getStatusClasses(status) {
        switch(status) {
            case 'completed': return 'bg-emerald-100 text-emerald-700';
            case 'in_progress': return 'bg-blue-100 text-blue-700';
            case 'cancelled': return 'bg-rose-100 text-rose-700';
            default: return 'bg-amber-100 text-amber-700';
        }
    }

    function showDetails(event) {
        selectedEventId = event.id;
        const modal = document.getElementById('detailsModal');

        const statusLabels = {
            pending: 'En attente',
            in_progress: 'En cours',
            completed: 'Terminée',
            cancelled: 'Annulée'
        };

        document.getElementById('detailTitle').textContent = event.extendedProps.rawTitle || event.title;
        document.getElementById('detailDate').textContent = event.start || '-';
        document.getElementById('detailTechnician').textContent = event.extendedProps.technician || '-';
        document.getElementById('detailFarm').textContent = event.extendedProps.farm || '-';
        document.getElementById('detailDescription').textContent = event.extendedProps.description || 'Aucune description';

        const statusEl = document.getElementById('detailStatus');
        statusEl.textContent = statusLabels[event.extendedProps.status] || event.extendedProps.status;
        statusEl.className = `inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClasses(event.extendedProps.status)}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetails() {
        const modal = document.getElementById('detailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.addEventListener('click', (e) => {
        const modal = document.getElementById('detailsModal');
        if (e.target === modal) closeDetails();
    });
</script>
@endsection