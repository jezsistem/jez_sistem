<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('publicHolidayCalendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id', // Bahasa Indonesia
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: "{{ url('public-holidays/calendar') }}",
            eventColor: '#dc3545', // merah (hari libur)
            eventTextColor: '#fff',
            dayMaxEvents: true
        });

        calendar.render();

        // reload calendar setelah sync
        window.reloadHolidayCalendar = function () {
            calendar.refetchEvents();
        };
    });

    function deletePublicHoliday(id) {
        Swal.fire({
            title: 'Hapus Hari Libur?',
            text: 'Data yang dihapus tidak bisa dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('public-holidays') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire(
                                'Terhapus!',
                                res.message,
                                'success'
                            );

                            // reload datatable
                            if (window.phTableTable) {
                                phTableTable.ajax.reload(null, false);
                            }



                            // reload calendar
                            if (typeof reloadHolidayCalendar === 'function') {
                                reloadHolidayCalendar();
                            }
                        } else {
                            Swal.fire(
                                'Gagal',
                                res.message || 'Delete gagal',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            'Error',
                            'Terjadi kesalahan saat menghapus data',
                            'error'
                        );
                    }
                });
            }
        });
    }

    $(document).on('click', '#btnSyncPublicHoliday', function () {

        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');

        $.ajax({
            url: "{{ route('public-holiday.sync') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sync Berhasil',
                        html: `
                            Data Baru : <b>${res.inserted}</b><br>
                            Data Update : <b>${res.updated}</b>
                        `
                    });

                    $.post("{{ url('public-holidays/sync-leave-balance') }}", {
                        _token: "{{ csrf_token() }}"
                    }).done(function () {
                        console.log('Leave balance updated');
                    });

                    phTableTable.ajax.reload(null, false);



                    if (typeof reloadHolidayCalendar === 'function') {
                        reloadHolidayCalendar();
                    }
                }
            },
            error: function (xhr) {

                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat melakukan sync hari libur',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });

                console.error(xhr.responseText);
            },
            complete: function () {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-sync"></i> Sync Hari Libur');
            }
        });
    });


    $(document).ready(function () {
        console.log('Shift Code JS loaded and ready');

        function formatTanggalIndo(dateStr) {
            if (!dateStr) return '-';

            const bulanIndo = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const date = new Date(dateStr);
            const tanggal = date.getDate();
            const bulan = bulanIndo[date.getMonth()];
            const tahun = date.getFullYear();

            return `${tanggal} ${bulan} ${tahun}`;
        }

        // Wait for DataTables to be available
        function initDataTable() {
            if (typeof $.fn.DataTable !== 'undefined') {
                try {
                    // Set up CSRF token
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var phTableTable = $('#phTable').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: true,
                        responsive: false,
                        dom: 'rt<"pagination-class"ip>',
                        ajax: {
                            url: "{{ route('public-holiday.datatables') }}",
                            data: function (d) {
                                d.search = $('#public_holiday_search').val();
                            }
                        },
                        columns: [
                            {
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                className: 'text-center',
                                width: '5%'
                            },
                            {
                                data: 'holiday_date',
                                name: 'holiday_date',
                                render: function (data) {
                                    return formatTanggalIndo(data);
                                }
                            },
                            {
                                data: 'description',
                                name: 'description',
                                width: '55%'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                className: 'text-center',
                                width: '20%'
                            }
                        ],
                        order: [[1, 'asc']],
                        pageLength: 10,
                        language: {
                            sProcessing: "Loading...",
                            sZeroRecords: "Tidak ditemukan data",
                            sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                            sInfoFiltered: "(difilter dari _MAX_ data)",
                        }
                    });

                    // Store reference for other functions
                    window.phTableTable = phTableTable;

                    // Search functionality with debounce
                    var searchTimeout;
                    $('#public_holiday_search').on('keyup input', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function () {
                            phTableTable.draw(false);
                        }, 300);
                    });

                    console.log('Shift Code DataTable initialized successfully');

                    // Initialize dropdown menu system
                    initializeSimpleDropdown();

                } catch (error) {
                    console.error('Error initializing DataTable:', error);
                }
            } else {
                // Wait a bit and try again
                setTimeout(initDataTable, 100);
            }
        }

        // Start initialization
        initDataTable();
    });

    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Shift Code');

        // Remove any existing event handlers
        $(document).off('click', '[data-kt-menu-trigger="click"]');

        // Add click handler for dropdown toggle
        $(document).on('click', '[data-kt-menu-trigger="click"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this);
            var $menu = $this.siblings('.menu');
            var $cardBody = $this.closest('.card.card-custom').find('> .card-body');
            var $row = $this.closest('tr');

            // Tutup semua menu lain
            $('.menu').not($menu).removeClass('show');
            $cardBody.removeClass('pb-extra'); // reset padding

            // Toggle menu ini
            $menu.toggleClass("show");

            // Jika menu terbuka & baris ini adalah row terakhir
            if ($menu.hasClass('show') && $row.is(':last-child')) {
                $cardBody.addClass('pb-extra');
            }
        });

        // Close menu when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.menu').removeClass('show');
            }
        });

        // Close menu when clicking on menu items
        $(document).on('click', '.menu-link', function (e) {
            if ($(this).attr('onclick')) {
                // For delete button, let the onclick handle it
                return;
            }
            // For view/edit links, close menu after a short delay
            setTimeout(function () {
                $('.menu').removeClass('show');
            }, 100);
        });

        console.log('Simple dropdown system initialized for Shift Code');
    }

    // Re-initialize dropdown on each draw
    if (window.phTableTable) {
        window.phTableTable.on('draw.dt', function () {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Delete shift code function
    function deletephTable(id) {
        if (confirm('Apakah Anda yakin ingin menghapus shift code ini?')) {
            // Show loading state
            const deleteBtn = document.querySelector(`[onclick="deletephTable(${id})"]`);
            if (deleteBtn) {
                deleteBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
                deleteBtn.style.pointerEvents = 'none';
            }

            // Send delete request
            $.ajax({
                url: `/shift-codes/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // Show success message
                    if (response.success) {
                        // Refresh DataTable
                        if (window.phTableTable) {
                            window.phTableTable.ajax.reload();
                        }

                        // Show success notification
                        alert('Shift code berhasil dihapus!');
                    } else {
                        alert('Gagal menghapus shift code: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Delete error:', xhr.responseText);

                    // Show error message
                    let errorMessage = 'Gagal menghapus shift code';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                },
                complete: function () {
                    // Reset button state
                    if (deleteBtn) {
                        deleteBtn.innerHTML = 'Delete';
                        deleteBtn.style.pointerEvents = 'auto';
                    }
                }
            });
        }
    }
</script> 