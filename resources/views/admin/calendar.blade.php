@extends('layouts.app')

@section('title', 'Calendrier des Missions')

@section('content')
<div class="space-y-6 px-6 ">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <span>Agenda</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-900 font-semibold">Missions</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Calendrier des Missions</h1>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 0114 0z"/></svg>
                <input type="text" id="calendarSearch" placeholder="Rechercher..." class="pl-9 pr-8 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 w-52 shadow-xs transition">
            </div>

            <button onclick="openModal()" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-xs hover:shadow transition-all duration-200">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle Mission
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        <div id="calendar" class="min-w-[700px]"></div>
    </div>
</div>

<div id="missionModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden items-center justify-center z-50 transition-all">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 id="modalTitle" class="text-sm font-bold text-slate-900">Nouvelle Mission</h2>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition">&times;</button>
        </div>

        <form id="missionForm" onsubmit="saveMission(event)">
            <div class="p-6 space-y-4 text-xs">
                <input type="hidden" id="missionId">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Technicien *</label>
                        <select name="technician_id" id="technician_id" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition">
                            <option value="">Sélectionner un technicien</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->user?->name ?? 'Technicien #'.$technician->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Exploitation *</label>
                        <select name="farm_id" id="farm_id" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition">
                            <option value="">Sélectionner une exploitation</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Titre de la mission *</label>
                    <input type="text" id="title" name="title" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition" placeholder="Intitulé de l'intervention">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                        <input type="date" id="scheduled_date" name="scheduled_date" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Statut *</label>
                        <select name="status" id="status" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition">
                            <option value="pending">En attente</option>
                            <option value="in_progress">En cours</option>
                            <option value="completed">Terminée</option>
                            <option value="cancelled">Annulée</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 outline-none transition" placeholder="Précisions sur la mission..."></textarea>
                </div>
            </div>

            <div class="px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
                <button type="button" id="deleteBtn" onclick="deleteMission()" class="hidden text-xs font-semibold text-rose-600 hover:text-rose-700 transition">
                    Supprimer
                </button>
                <div class="flex space-x-2 ml-auto">
                    <button type="button" onclick="closeModal()" class="px-3.5 py-1.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Annuler</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition shadow-xs">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
            <button type="button" id="deleteFromDetailsBtn" onclick="deleteFromDetails()" class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition">Supprimer</button>
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" onclick="closeDetails()" class="px-3.5 py-1.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Fermer</button>
                <button type="button" onclick="editFromDetails()" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition shadow-xs">Modifier</button>
            </div>
        </div>
    </div>
</div>

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
    }
    .fc .fc-button-primary:hover {
        background-color: #f9fafb !important;
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

    .fc .fc-list-event {
        padding: 0 !important;
        margin: 0 !important;
    }

    .fc .fc-list-event .fc-event-card {
        background: white !important;
        border: 2px solid !important;
        border-radius: 12px !important;
        padding: 16px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    }

    .fc .fc-list-event .fc-event-card .fc-event-title {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #101828 !important;
        margin-bottom: 8px !important;
    }

    .fc .fc-list-event .fc-event-card .fc-event-time {
        font-size: 12px !important;
        color: #64748b !important;
        margin-bottom: 8px !important;
    }

    .list-view-cards {
        display: grid !important;
        grid-template-columns: repeat(6, 1fr) !important;
        gap: 12px !important;
    }

    .mission-card {
        background: white !important;
        border-left: 4px solid !important;
        border-radius: 8px !important;
        padding: 12px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .mission-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }

    .mission-card.border-amber { border-left-color: #f59e0b !important; }
    .mission-card.border-blue { border-left-color: #3b82f6 !important; }
    .mission-card.border-emerald { border-left-color: #10b981 !important; }
    .mission-card.border-rose { border-left-color: #ec4899 !important; }

    .mission-title {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #101828 !important;
        margin-bottom: 6px !important;
        line-height: 1.3 !important;
    }

    .mission-meta {
        font-size: 11px !important;
        color: #64748b !important;
        margin-bottom: 4px !important;
    }

    .mission-date {
        font-size: 11px !important;
        color: #94a3b8 !important;
        font-weight: 500 !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
    let calendar;
    let editingId = null;
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
                today: 'Aujourd\'hui',
                month: 'Vue mois',
                list: 'Vue liste'
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
            eventDblClick: (info) => openModal(info.event),
            dateClick: (info) => openModal(null, info.dateStr),
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

    function getStatusColor(status) {
        const colors = {
            pending: 'amber',
            in_progress: 'blue',
            completed: 'emerald',
            cancelled: 'rose'
        };
        return colors[status] || 'amber';
    }

    function getStatusBorderColor(status) {
        const colors = {
            pending: '#f59e0b',
            in_progress: '#3b82f6',
            completed: '#10b981',
            cancelled: '#ec4899'
        };
        return colors[status] || '#f59e0b';
    }

    function getStatusLabel(status) {
        const labels = {
            pending: 'En attente',
            in_progress: 'En cours',
            completed: 'Terminée',
            cancelled: 'Annulée'
        };
        return labels[status] || status;
    }

    function getStatusClass(status) {
        const classes = {
            pending: 'bg-amber-100 text-amber-700',
            in_progress: 'bg-blue-100 text-blue-700',
            completed: 'bg-emerald-100 text-emerald-700',
            cancelled: 'bg-rose-100 text-rose-700'
        };
        return classes[status] || classes.pending;
    }

    function openModal(event = null, dateStr = '') {
        const modal = document.getElementById('missionModal');
        const deleteBtn = document.getElementById('deleteBtn');
        const modalTitle = document.getElementById('modalTitle');

        document.getElementById('missionId').value = '';
        document.getElementById('technician_id').value = '';
        document.getElementById('farm_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('scheduled_date').value = dateStr ? dateStr.split('T')[0] : '';
        document.getElementById('description').value = '';
        document.getElementById('status').value = 'pending';

        deleteBtn.classList.add('hidden');

        if (event) {
            modalTitle.textContent = 'Modifier la Mission';
            editingId = event.id;

            document.getElementById('missionId').value = event.id;
            document.getElementById('technician_id').value = event.extendedProps.technician_id || '';
            document.getElementById('farm_id').value = event.extendedProps.farm_id || '';
            document.getElementById('title').value = event.extendedProps.rawTitle || event.title;
            document.getElementById('description').value = event.extendedProps.description || '';

            const d = new Date(event.start);
            document.getElementById('scheduled_date').value = d.toISOString().split('T')[0];
            document.getElementById('status').value = event.extendedProps.status || 'pending';

            deleteBtn.classList.remove('hidden');
        } else {
            modalTitle.textContent = 'Nouvelle Mission';
            editingId = null;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('missionModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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
        document.getElementById('detailDate').textContent = event.start ? new Date(event.start).toLocaleDateString('fr-FR') : '-';
        document.getElementById('detailTechnician').textContent = event.extendedProps.technician || '-';
        document.getElementById('detailFarm').textContent = event.extendedProps.farm || '-';
        document.getElementById('detailDescription').textContent = event.extendedProps.description || 'Aucune description';

        const statusEl = document.getElementById('detailStatus');
        statusEl.textContent = statusLabels[event.extendedProps.status] || event.extendedProps.status;
        statusEl.className = `inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(event.extendedProps.status)}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetails() {
        const modal = document.getElementById('detailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editFromDetails() {
        closeDetails();
        const event = calendar.getEventById(selectedEventId);
        if (event) {
            setTimeout(() => openModal(event), 100);
        }
    }

    function deleteFromDetails() {
        if (!selectedEventId || !confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')) return;

        fetch('{{ route("admin.calendar.missions.destroy", ":id") }}'.replace(':id', selectedEventId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = calendar.getEventById(selectedEventId);
                if (event) event.remove();
                closeDetails();
            } else {
                alert('Erreur lors de la suppression.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression.');
        });
    }

    async function saveMission(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        formData.append('technician_id', document.getElementById('technician_id').value);
        formData.append('farm_id', document.getElementById('farm_id').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('scheduled_date', document.getElementById('scheduled_date').value);
        formData.append('status', document.getElementById('status').value);

        const url = editingId
            ? '{{ route("admin.calendar.missions.update", ":id") }}'.replace(':id', editingId)
            : '{{ route("admin.calendar.missions.store") }}';

        if (editingId) formData.append('_method', 'PUT');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                if (editingId) {
                    localMissions = localMissions.map(m => m.id == editingId ? data.mission : m);
                } else {
                    localMissions.push(data.mission);
                }

                calendar.refetchEvents();
                closeModal();
            } else {
                alert(data.message || 'Erreur lors de l\'enregistrement.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de l\'enregistrement.');
        }
    }

    async function deleteMission() {
        if (!editingId || !confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')) return;

        try {
            const response = await fetch('{{ route("admin.calendar.missions.destroy", ":id") }}'.replace(':id', editingId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                localMissions = localMissions.filter(m => m.id != editingId);
                calendar.refetchEvents();
                closeModal();
            } else {
                alert('Erreur lors de la suppression.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de la suppression.');
        }
    }

    window.addEventListener('click', (e) => {
        const modal = document.getElementById('missionModal');
        if (e.target === modal) closeModal();
    });
</script>
@endsection