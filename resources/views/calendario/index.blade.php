<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendario de Pagos') }}
        </h2>
    </x-slot>

    <!-- Container maximizado -->
    <div class="h-100 p-0">
        <div class="card shadow-none h-100 border-0 rounded-0">
            <div class="card-body p-2 h-100">
                <div id="calendar" class="h-100"></div>
            </div>
        </div>
    </div>

    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es', // Español
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Lista'
                },
                events: @json($events),
                datesSet: function (info) {
                    let currentDate = calendar.getDate();
                    let currentMonth = currentDate.getMonth();
                    let currentYear = currentDate.getFullYear();
                    let total = 0;

                    let allEvents = calendar.getEvents();
                    allEvents.forEach(function (event) {
                        // Sum only events belonging to the displayed month/year AND NOT VOIDED (Status 0)
                        if (event.start.getMonth() === currentMonth && event.start.getFullYear() === currentYear) {
                            if (event.extendedProps.estado != 0) {
                                // Ensure monto is treated as float
                                total += parseFloat(event.extendedProps.monto || 0);
                            }
                        }
                    });

                    // Update title
                    let titleEl = document.querySelector('.fc-toolbar-title');
                    if (titleEl) {
                        let formattedTotal = new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(total);
                        titleEl.innerHTML = `${info.view.title} - <span style="font-size: 0.8em; color: #6b7280;">Total: ${formattedTotal}</span>`;
                    }
                },
                eventClick: function (info) {
                    let props = info.event.extendedProps;

                    // Direct Navigation if already confirmed (Status 2), Voided (Status 0), or payment types that are always confirmed (Efectivo, Depósito)
                    if (props.estado == 2 || props.estado == 0 || props.type === 'Efectivo' || props.type === 'Depósito') {
                        window.location.href = `/admin/facturas/${props.factura_id}`;
                        return;
                    }

                    let confirmUrl = '';
                    let anularUrl = '';

                    if (props.type === 'Cheque') {
                        confirmUrl = `/admin/cheques/${props.id}/confirmar`;
                        anularUrl = `/admin/cheques/${props.id}/anular`;
                    } else {
                        confirmUrl = `/admin/tarjetas/${props.id}/confirmar`;
                        anularUrl = `/admin/tarjetas/${props.id}/anular`;
                    }

                    Swal.fire({
                        title: info.event.title,
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>Tipo:</strong> ${props.type}</p>
                                <p class="mb-2"><strong>No. Doc:</strong> ${props.no_doc}</p>
                                <p class="mb-2"><strong>Fecha:</strong> ${info.event.start.toLocaleDateString()}</p>
                            </div>
                            <div class="mt-4 flex gap-2 justify-center">
                                <button id="btn-void-payment" class="btn btn-danger flex-1 text-sm">Anular</button>
                                <a href="/admin/facturas/${props.factura_id}" class="btn btn-primary flex-1 text-sm flex items-center justify-center p-0" style="text-decoration:none;">Ver Detalles</a>
                                <button id="btn-confirm-payment" class="btn btn-success flex-1 text-sm">Confirmar</button>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true,
                        buttonsStyling: false,
                        customClass: {
                            popup: 'card shadow'
                        },
                        didOpen: () => {
                            const btnConfirm = document.getElementById('btn-confirm-payment');
                            if (btnConfirm) {
                                btnConfirm.addEventListener('click', () => {
                                    Swal.fire({
                                        title: '¿Confirmar Pago?',
                                        text: "¡Esto confirmará la recepción de fondos!",
                                        icon: 'success',
                                        showCancelButton: true,
                                        confirmButtonText: 'Sí, confirmar',
                                        cancelButtonText: 'Cancelar',
                                        customClass: {
                                            confirmButton: 'btn btn-success me-2',
                                            cancelButton: 'btn btn-secondary'
                                        },
                                        buttonsStyling: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            fetch(confirmUrl, {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        info.event.setProp('color', '#9ca3af'); // Gray
                                                        info.event.setExtendedProp('estado', 2);
                                                        Swal.close();
                                                        Swal.fire('¡Confirmado!', 'El pago ha sido confirmado.', 'success');
                                                    }
                                                });
                                        }
                                    });
                                });
                            }

                            const btnVoid = document.getElementById('btn-void-payment');
                            if (btnVoid) {
                                btnVoid.addEventListener('click', () => {
                                    Swal.fire({
                                        title: '¿Anular Pago?',
                                        text: "¡Esto anulará el pago y dejará la factura pendiente!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Sí, anular',
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonText: 'Cancelar',
                                        customClass: {
                                            confirmButton: 'btn btn-danger me-2',
                                            cancelButton: 'btn btn-secondary'
                                        },
                                        buttonsStyling: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            fetch(anularUrl, {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        info.event.setProp('color', '#ef4444'); // Red
                                                        info.event.setExtendedProp('estado', 0);
                                                        Swal.close();
                                                        Swal.fire('¡Anulado!', 'El pago ha sido anulado.', 'error');
                                                    }
                                                });
                                        }
                                    });
                                });
                            }
                        }
                    });
                },
                height: '100%', // Ocupar todo el alto del contenedor
                themeSystem: 'standard'
            });
            calendar.render();
        });
    </script>

    <!-- Custom CSS -->
    <style>
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: #374151 !important;
        }

        .fc-button-primary {
            background-color: #be185d !important;
            border-color: #be185d !important;
        }

        .fc-button-primary:hover {
            background-color: #9d174d !important;
            border-color: #9d174d !important;
        }

        .fc-button-active {
            background-color: #be185d !important;
            border-color: #be185d !important;
        }

        .fc-event {
            cursor: pointer !important;
            /* Force pointer cursor */
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .fc-daygrid-event {
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 0.85rem;
        }
    </style>
</x-app-layout>