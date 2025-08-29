<script>
    // Global reference for DataTable
    window.leaveTypeTable = null;

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing leave type DataTable...');
            
            window.leaveTypeTable = $('#leaveTypeTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url : "{{ url('leave-types/datatables') }}",
                    data : function (d) {
                        d.search = $('#leave_type_search').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'lt_code', name: 'lt_code', width: '15%' },
                    { data: 'lt_name', name: 'lt_name', width: '20%' },
                    { data: 'lt_description', name: 'lt_description', width: '25%' },
                    { data: 'lt_duration', name: 'lt_duration', width: '10%' },
                    { data: 'lt_status', name: 'lt_status', width: '10%' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' },
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": 4,
                        "className": "text-center"
                    },
                    {
                        "targets": 5,
                        "className": "text-center"
                    },
                    {
                        "targets": 6,
                        "className": "text-center"
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    "sProcessing":   "Loading...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Cari:",
                    "sUrl":          ""
                }
            });
            
            console.log('Leave type DataTable initialized successfully');
            
            // Initialize dropdown menu system
            initializeSimpleDropdown();
            
            // Search functionality with debounce
            var searchTimeout;
            $('#leave_type_search').on('keyup input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    window.leaveTypeTable.draw(false);
                }, 300);
            });
            
        } catch (error) {
            console.error('Error initializing leave type DataTable:', error);
            setTimeout(initDataTable, 100);
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize DataTable
        initDataTable();
    });

    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Leave Type');
        
        // Remove any existing event handlers
        $(document).off('click', '[data-kt-menu-trigger="click"]');
        
        // Add click handler for dropdown toggle
        $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
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
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.menu').removeClass('show');
            }
        });
        
        // Close menu when clicking on menu items
        $(document).on('click', '.menu-link', function(e) {
            if ($(this).attr('onclick')) {
                // For delete button, let the onclick handle it
                return;
            }
            // For view/edit links, close menu after a short delay
            setTimeout(function() {
                $('.menu').removeClass('show');
            }, 100);
        });
        
        console.log('Simple dropdown system initialized for Leave Type');
    }

    // Re-initialize dropdown on each draw
    if (window.leaveTypeTable) {
        window.leaveTypeTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Custom toast function
    function showToast(title, message, type) {
        showSimpleToast(title, message, type);
    }

    // Simple custom toast function
    function showSimpleToast(title, message, type) {
        const toastHtml = `
            <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; 
                        background: ${type === 'success' ? '#1BC5BD' : '#dc3545'}; 
                        color: white; padding: 15px 20px; border-radius: 2px; 
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2); max-width: 300px;" 
             id="customToast">
                <strong>${title}</strong><br>
                ${message}
            </div>
        `;
        
        $('body').append(toastHtml);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            $('#customToast').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Delete leave type function
    function deleteLeaveType(id) {
        if (confirm('Are you sure you want to delete this leave type?')) {
            $.ajax({
                url: "{{ url('leave-types') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (window.leaveTypeTable) {
                            window.leaveTypeTable.ajax.reload();
                        } else {
                            $('#leaveTypeTable').DataTable().ajax.reload();
                        }
                        showToast('Success', 'Leave type deleted successfully', 'success');
                    } else {
                        showToast('Error', 'Failed to delete leave type', 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'Error occurred while deleting leave type', 'error');
                }
            });
        }
    }
</script> 