<script>
    // Local toast function to ensure it works
    function showToast(title, message, type) {
        console.log('showToast called with:', title, message, type);
        // Try to use jQuery toast first
        if (typeof jQuery !== 'undefined' && typeof jQuery.toast === 'function') {
            console.log('Using jQuery.toast');
            jQuery.toast({
                heading: title,
                text: message,
                icon: type,
                loader: true,
                loaderBg: '#072544',
                position: 'top-right',
                stack: false,
                hideAfter: 3000
            });
        } 
        // Skip global toast function since it has jQuery.toast dependency issues
        // Fallback to simple notification div
        else {
            console.log('Using simple custom toast');
            showSimpleToast(title, message, type);
        }
    }

    // Simple custom toast as final fallback
    function showSimpleToast(title, message, type) {
        console.log('showSimpleToast executed with:', title, message, type);
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
        var userPositionTable = $('#userPositionTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
                        responsive: false, // Disable responsive to prevent conflicts
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                            url: "{{ url('user-positions/datatables') }}",
                            data: function(d) {
                    d.search = $('#user_position_search').val();
                }
            },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'id',
                                searchable: false
                            },
                            {
                                data: 'up_code',
                                name: 'up_code'
                            },
                            {
                                data: 'up_name',
                                name: 'up_name'
                            },
                            {
                                data: 'up_description',
                                name: 'up_description'
                            },
                            {
                                data: 'up_level',
                                name: 'up_level'
                            },
                            {
                                data: 'up_is_active',
                                name: 'up_is_active'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        columnDefs: [{
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                        }, {
                    "targets": 4,
                    "className": "text-center"
                        }, {
                    "targets": 5,
                    "className": "text-center"
                        }, {
                    "targets": 6,
                    "className": "text-center"
                        }],
                        order: [
                            [0, 'desc']
            ],
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
                    $('#user_position_search').on('keyup', function() {
                        userPositionTable.draw();
                    });

                    // Store reference for delete function
                    window.userPositionTable = userPositionTable;
                    
                    console.log('User Position DataTable initialized successfully');
                    
                    // Debug: Check what Metronic components are available
                    console.log('=== Metronic Components Check ===');
                    console.log('typeof KTMenu:', typeof KTMenu);
                    console.log('typeof KT:', typeof KT);
                    console.log('typeof KT.Menu:', typeof KT !== 'undefined' ? typeof KT.Menu : 'KT undefined');
                    console.log('window.KTMenu:', window.KTMenu);
                    console.log('window.KT:', window.KT);
                    
                    // Simple working dropdown solution
                    function initializeSimpleDropdown() {
                        console.log('Initializing simple dropdown system');
                        
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
                        
                        console.log('Simple dropdown system initialized');
                    }
                    
                    // Initialize simple dropdown after a delay
                    setTimeout(initializeSimpleDropdown, 500);
                    
                    // Re-initialize on each draw
                    userPositionTable.on('draw.dt', function() {
                        setTimeout(initializeSimpleDropdown, 100);
                    });
                    
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

    // Delete function
    function deleteUserPosition(id) {
        if (confirm('Are you sure you want to delete this user position?')) {
            $.ajax({
                url: "{{ url('user-positions') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Delete response:', response); // Debug log
                    console.log('Calling showToast function...'); // Debug
                    
                    if (response.success) {
                        if (window.userPositionTable && typeof window.userPositionTable.ajax !== 'undefined') {
                            window.userPositionTable.ajax.reload();
                        } else {
                            location.reload();
                        }
                        
                        // Use our custom toast function
                        console.log('About to show success toast'); // Debug
                        showToast('Success', 'User position deleted successfully', 'success');
                        console.log('Success toast called'); // Debug
                    } else {
                        console.log('About to show error toast'); // Debug
                        showToast('Error', 'Failed to delete user position', 'error');
                        console.log('Error toast called'); // Debug
                    }
                },
                error: function() {
                    showToast('Error', 'Error occurred while deleting user position', 'error');
                }
            });
        }
    }
</script> 