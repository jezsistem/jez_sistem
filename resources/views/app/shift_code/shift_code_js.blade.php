<script>
    // Simple and robust DataTables initialization
    $(document).ready(function() {
        console.log('Shift Code JS loaded and ready');
        
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
                    
                    var shiftCodeTable = $('#shiftCodeTable').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: true,
                        responsive: false, // Disable responsive to prevent conflicts
                        dom: 'rt<"pagination-class"ip>',
                        ajax: {
                            url : "{{ url('shift-codes/datatables') }}",
                            data : function (d) {
                                d.search = $('#shift_code_search').val();
                            }
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                            { data: 'sc_code', name: 'sc_code', width: '10%' },
                            { data: 'sc_description', name: 'sc_description', width: '20%' },
                            { data: 'sc_shift_name', name: 'sc_shift_name', width: '15%' },
                            { data: 'sc_start_time', name: 'sc_start_time', width: '10%' },
                            { data: 'sc_end_time', name: 'sc_end_time', width: '10%' },
                            { data: 'sc_type', name: 'sc_type', width: '10%' },
                            { data: 'sc_status', name: 'sc_status', width: '10%' },
                            { data: 'action', name: 'action', orderable: false, searchable: false, width: '10%' },
                        ],
                        columnDefs: [
                            {
                                "targets": 0,
                                "className": "text-center",
                                "width": "5%"
                            },
                            {
                                "targets": 6,
                                "className": "text-center"
                            },
                            {
                                "targets": 7,
                                "className": "text-center"
                            },
                            {
                                "targets": 8,
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
                    
                    // Store reference for other functions
                    window.shiftCodeTable = shiftCodeTable;
                    
                    // Search functionality with debounce
                    var searchTimeout;
                    $('#shift_code_search').on('keyup input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            shiftCodeTable.draw(false);
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
        $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $this = $(this);
            var $menu = $this.siblings('.menu');
            
            console.log('Dropdown clicked, menu found:', $menu.length);
            
            // Close all other menus first
            $('.menu').not($menu).removeClass('show');
            
            // Toggle current menu
            $menu.toggleClass('show');
            
            console.log('Menu toggled, has show class:', $menu.hasClass('show'));
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
        
        console.log('Simple dropdown system initialized for Shift Code');
    }

    // Re-initialize dropdown on each draw
    if (window.shiftCodeTable) {
        window.shiftCodeTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Delete shift code function
    function deleteShiftCode(id) {
        if (confirm('Apakah Anda yakin ingin menghapus shift code ini?')) {
            // Show loading state
            const deleteBtn = document.querySelector(`[onclick="deleteShiftCode(${id})"]`);
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
                success: function(response) {
                    // Show success message
                    if (response.success) {
                        // Refresh DataTable
                        if (window.shiftCodeTable) {
                            window.shiftCodeTable.ajax.reload();
                        }
                        
                        // Show success notification
                        alert('Shift code berhasil dihapus!');
                    } else {
                        alert('Gagal menghapus shift code: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', xhr.responseText);
                    
                    // Show error message
                    let errorMessage = 'Gagal menghapus shift code';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                },
                complete: function() {
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