<script>
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

    // Modal functions using vanilla JavaScript
    function showModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }

    function hideModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    // DataTables initialization
    $(document).ready(function() {
        console.log('Staff JS loaded and ready');
        
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
                    
                    var staffTable = $('#staffTable').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: true,
                        responsive: false, // Disable responsive to prevent conflicts
                        dom: 'rt<"pagination-class"ip>',
                        ajax: {
                            url : "{{ url('staff/datatables') }}",
                            data : function (d) {
                                d.search = $('#staff_search').val();
                                d.position_filter = $('#position_filter').val();
                                d.division_filter = $('#division_filter').val();
                            }
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                            { data: 'u_nip', name: 'u_nip', width: '12%' },
                            { data: 'u_name', name: 'u_name', width: '18%' },
                            { data: 'up_name', name: 'up_name', width: '13%' },
                            { data: 'ud_name', name: 'ud_name', width: '13%' },
                            { data: 'ut_name', name: 'ut_name', width: '12%' },
                            { data: 'lb_remaining_balance', name: 'lb_remaining_balance', width: '13%' },
                            { data: 'action', name: 'action', orderable: false, searchable: false, width: '14%' },
                        ],
                        columnDefs: [
                            {
                                "targets": 0,
                                "className": "text-center",
                                "width": "5%"
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
                            "sUrl":          "",
                        }
                    });
                    
                    // Store reference for other functions
                    window.staffTable = staffTable;
                    
                    console.log('Staff DataTable initialized successfully');
                    
                    // Initialize dropdown menu system
                    initializeSimpleDropdown();
                    
                    // Search functionality with debounce
                    var searchTimeout;
                    $('#staff_search').on('keyup input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            staffTable.draw(false);
                        }, 300);
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
        
        // staffTable.buttons().container().appendTo($('#staff_excel_btn'));
        
        // Filter functionality
        $('#position_filter, #division_filter').on('change', function() {
            if (window.staffTable && typeof window.staffTable.draw !== 'undefined') {
                window.staffTable.draw(false);
            }
        });
    });

    // Apply filters function
    function applyFilters() {
        if (window.staffTable && typeof window.staffTable.ajax !== 'undefined') {
            window.staffTable.ajax.reload();
        }
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
                        $('#userDivisionTable').DataTable().ajax.reload();
                        showToast('Success', 'User division deleted successfully', 'success');
                    } else {
                        showToast('Error', 'Failed to delete user division', 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'Error occurred while deleting user division', 'error');
                }
            });
        }
    }

    // Get current position and division data for edit modals
    function editPosition(userId) {
        // Get current user data from DataTable
        if (window.staffTable && typeof window.staffTable.row !== 'undefined') {
            var row = window.staffTable.row(function(idx, data, node) {
                return data.id == userId;
            }).data();
            
            if (row) {
                $('#positionUserId').val(userId);
                $('#up_id').val(row.up_id || ''); // Set current position
                showModal('positionModal');
            }
        }
    }

    function editDivision(userId) {
        // Get current user data from DataTable
        if (window.staffTable && typeof window.staffTable.row !== 'undefined') {
            var row = window.staffTable.row(function(idx, data, node) {
                return data.id == userId;
            }).data();
            
            if (row) {
                $('#divisionUserId').val(userId);
                $('#ud_id').val(row.ud_id || ''); // Set current division
                showModal('divisionModal');
            }
        }
    }

    function editUserType(userId) {
        // Get current user data from DataTable
        if (window.staffTable && typeof window.staffTable.row !== 'undefined') {
            var row = window.staffTable.row(function(idx, data, node) {
                return data.id == userId;
            }).data();
            
            if (row) {
                $('#userTypeUserId').val(userId);
                $('#ut_id').val(row.ut_id); // Set current user type
                showModal('userTypeModal');
            }
        }
    }

    function editLeaveBalance(userId) {
        // Get current user data from DataTable
        if (window.staffTable && typeof window.staffTable.row !== 'undefined') {
            var row = window.staffTable.row(function(idx, data, node) {
                return data.id == userId;
            }).data();
            
            if (row) {
                $('#leaveBalanceUserId').val(userId);
                $('#lb_remaining_balance').val(row.lb_remaining_balance || 0); // Set current balance
                showModal('leaveBalanceModal');
            }
        }
    }

    // Handle form submissions
    $(document).ready(function() {
        $('#positionForm').on('submit', function(e) {
            e.preventDefault();
            
            var userId = $('#positionUserId').val();
            var positionId = $('#up_id').val();
            
            $.ajax({
                url: "{{ url('staff') }}/" + userId + "/position",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: userId,
                    up_id: positionId
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        hideModal('positionModal');
                        if (window.staffTable && typeof window.staffTable.ajax !== 'undefined') {
                            window.staffTable.ajax.reload();
                        }
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'An error occurred while updating position', 'error');
                }
            });
        });

        $('#divisionForm').on('submit', function(e) {
            e.preventDefault();
            
            var userId = $('#divisionUserId').val();
            var divisionId = $('#ud_id').val();
            
            $.ajax({
                url: "{{ url('staff') }}/" + userId + "/division",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: userId,
                    ud_id: divisionId
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        hideModal('divisionModal');
                        if (window.staffTable && typeof window.staffTable.ajax !== 'undefined') {
                            window.staffTable.ajax.reload();
                        }
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'An error occurred while updating division', 'error');
                }
            });
        });

        $('#userTypeForm').on('submit', function(e) {
            e.preventDefault();
            
            var userId = $('#userTypeUserId').val();
            var userTypeId = $('#ut_id').val();
            
            $.ajax({
                url: "{{ url('staff') }}/" + userId + "/user-type",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: userId,
                    ut_id: userTypeId
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        hideModal('userTypeModal');
                        if (window.staffTable && typeof window.staffTable.ajax !== 'undefined') {
                            window.staffTable.ajax.reload();
                        }
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'An error occurred while updating user type', 'error');
                }
            });
        });

        $('#leaveBalanceForm').on('submit', function(e) {
            e.preventDefault();
            
            var userId = $('#leaveBalanceUserId').val();
            var balance = $('#lb_remaining_balance').val();
            
            $.ajax({
                url: "{{ url('staff') }}/" + userId + "/leave-balance",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: userId,
                    lb_remaining_balance: balance
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        hideModal('leaveBalanceModal');
                        if (window.staffTable && typeof window.staffTable.ajax !== 'undefined') {
                            window.staffTable.ajax.reload();
                        }
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'An error occurred while updating leave balance', 'error');
                }
            });
        });

        // Handle modal close buttons
        $('[data-dismiss="modal"]').on('click', function() {
            var modal = $(this).closest('.modal');
            if (modal.length > 0) {
                hideModal(modal.attr('id'));
            }
        });

        // Handle modal backdrop click
        $('.modal').on('click', function(e) {
            if (e.target === this) {
                hideModal($(this).attr('id'));
            }
        });
    });

    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Staff');
        
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
                // For buttons with onclick, let the onclick handle it
                return;
            }
            // For other links, close menu after a short delay
            setTimeout(function() {
                $('.menu').removeClass('show');
            }, 100);
        });
        
        console.log('Simple dropdown system initialized for Staff');
    }

    // Re-initialize dropdown on each draw
    if (window.staffTable) {
        window.staffTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }
</script> 