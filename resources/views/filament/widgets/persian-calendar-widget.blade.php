<div wire:ignore>
    <div id="persian-calendar-instance" style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" class="dark:bg-gray-900"></div>
    
    <link href="https://cdn.jsdelivr.net/gh/samanrashidii/persian-fullcalendar@master/css/fullcalendar.min.css" rel="stylesheet" />
    <style>
        #persian-calendar-instance { color: #374151; }
        .dark #persian-calendar-instance { color: #e5e7eb; }
        .fc-unthemed td.fc-today { background: #f3f4f6; }
        .dark .fc-unthemed td.fc-today { background: #374151; }
        .fc-button { background: #fff; border: 1px solid #d1d5db; color: #374151; text-transform: capitalize; }
        .dark .fc-button { background: #374151; border: 1px solid #4b5563; color: #e5e7eb; }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-jalaali@0.9.2/build/moment-jalaali.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/samanrashidii/persian-fullcalendar@master/js/fullcalendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/samanrashidii/persian-fullcalendar@master/js/fa.js"></script>
    
    <script>
        (function() {
            function initPersianCalendar() {
                var calendarEl = $('#persian-calendar-instance');
                if (calendarEl.length === 0) return;
                
                moment.loadPersian();
                
                calendarEl.fullCalendar({
                    isRTL: true,
                    locale: 'fa',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listMonth'
                    },
                    events: @json($this->getEvents()),
                    eventClick: function(event) {
                        var msg = 'Document: ' + event.document_title + '\nField: ' + event.field_key;
                        if (event.organization && event.organization !== 'N/A') {
                            msg = 'Org: ' + event.organization + '\n' + msg;
                        }
                        alert(msg);
                    },
                    height: 'auto'
                });
            }

            if (typeof jQuery !== 'undefined' && typeof $.fn.fullCalendar !== 'undefined') {
                initPersianCalendar();
            } else {
                setTimeout(initPersianCalendar, 500);
            }
        })();
    </script>
</div>
