<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Kalender Mediasi Interaktif</h2>
        <p class="text-xs text-slate-500 mt-1">Visualisasi jadwal sesi mediasi dan ketersediaan ruangan mediasi</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= site_url('mediator/jadwal') ?>" class="text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <i class="fa-solid fa-list mr-1"></i> Tampilan Tabel
        </a>
        <a href="<?= site_url('mediator/jadwal/kalender') ?>" class="text-xs font-bold text-white bg-blue-600 px-4 py-2.5 rounded-xl shadow-md shadow-blue-600/20">
            <i class="fa-regular fa-calendar-days mr-1"></i> Tampilan Kalender
        </a>
    </div>
</div>

<!-- Custom Calendar Styling -->
<style>
.fc .fc-daygrid-body-unbalanced .fc-daygrid-day-events {
    min-height: 2em;
}
.fc-event {
    border-radius: 8px !important;
    border: none !important;
    box-shadow: 0 2px 5px rgba(37, 99, 235, 0.25) !important;
    padding: 2px 4px !important;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4) !important;
}
.fc-daygrid-event-harness {
    margin-bottom: 4px !important;
}
.fc-event-main {
    padding: 2px 4px !important;
}
</style>

<!-- Calendar Card -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
    <div id="calendar-container" class="min-h-[650px]"></div>
</div>

<!-- Include FullCalendar 6.1.10 -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar-container');
    const eventsData = <?= $events_json ?>;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        eventDisplay: 'block',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari'
        },
        events: eventsData,
        eventContent: function(arg) {
            const waktu  = arg.event.extendedProps.waktu || '';
            const nomor  = arg.event.title || '';
            const tempat = arg.event.extendedProps.tempat || '';

            return {
                html: `
                <div class="p-1 text-white font-sans leading-tight">
                    <div class="font-bold text-[11px] mb-0.5 text-blue-100">${waktu}</div>
                    <div class="font-mono font-extrabold text-[12px] text-white truncate">${nomor}</div>
                    <div class="text-[11px] text-blue-100 font-medium truncate mt-0.5">${tempat}</div>
                </div>
                `
            };
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        }
    });

    calendar.render();
});
</script>
