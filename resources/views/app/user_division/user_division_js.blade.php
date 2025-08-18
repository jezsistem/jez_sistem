<script>
    // Simple and robust DataTables initialization
    $(document).ready(function() {
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
        
                    // Initialize DataTable with safe settings
        var userDivisionTable = $('#userDivisionTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
                        responsive: false, // Disable responsive to prevent conflicts
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                            url: "{{ url('user-divisions/datatables') }}",
                            data: function(d) {
                    d.search = $('#user_division_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'ud_code', name: 'ud_code', width: '15%' },
                { data: 'ud_name', name: 'ud_name', width: '25%' },
                { data: 'ud_description', name: 'ud_description', width: '30%' },
                { data: 'ud_status', name: 'ud_status', width: '10%' },
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
                "sUrl":          "",
                        }
                    });

                    // Search functionality
                    $('#user_division_search').on('keyup', function() {
                        userDivisionTable.draw();
                    });

                    // Store reference for delete function
                    window.userDivisionTable = userDivisionTable;
                    
                    console.log('User Division DataTable initialized successfully');
                    
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
        console.log('Initializing simple dropdown system for User Division');
        
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
        
        console.log('Simple dropdown system initialized for User Division');
    }

    // Re-initialize dropdown on each draw
    if (window.userDivisionTable) {
        window.userDivisionTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Delete user division function
    function deleteUserDivision(id) {
        if (confirm('Are you sure you want to delete this user division?')) {
            $.ajax({
                url: "{{ url('user-divisions') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (window.userDivisionTable && typeof window.userDivisionTable.ajax !== 'undefined') {
                            window.userDivisionTable.ajax.reload();
                        } else {
                            location.reload();
                        }
                        alert('User division deleted successfully');
                    } else {
                        alert('Failed to delete user division');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting user division');
                }
            });
        }
    }
</script> 