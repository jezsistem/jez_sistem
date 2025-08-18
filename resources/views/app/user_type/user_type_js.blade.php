<script>
    // Modal functions using vanilla JavaScript
    function showModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            console.log('Modal shown:', modalId);
        } else {
            console.error('Modal not found:', modalId);
        }
    }

    function hideModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            console.log('Modal hidden:', modalId);
        } else {
            console.error('Modal not found:', modalId);
        }
    }

    // Simple and robust DataTables initialization
    $(document).ready(function() {
        console.log('User Type JS loaded and ready');
        
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
                    var userTypeTable = $('#userTypeTable').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: true,
                        responsive: false, // Disable responsive to prevent conflicts
                        dom: 'rt<"pagination-class"ip>',
                        ajax: {
                            url: "{{ url('user-types/datatables') }}",
                            data: function(d) {
                                d.search = $('#user_type_search').val();
                            }
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                            { data: 'ut_code', name: 'ut_code', width: '15%' },
                            { data: 'ut_name', name: 'ut_name', width: '25%' },
                            { data: 'ut_description', name: 'ut_description', width: '30%' },
                            { data: 'ut_status', name: 'ut_status', width: '10%' },
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
                            "sUrl":          ""
                        }
                    });

                    // Search functionality
                    $('#user_type_search').on('keyup', function() {
                        userTypeTable.draw();
                    });

                    // Store reference for other functions
                    window.userTypeTable = userTypeTable;
                    
                    console.log('User Type DataTable initialized successfully');
                    
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

        // Handle modal close buttons
        $('[data-dismiss="modal"]').on('click', function() {
            console.log('Modal close button clicked');
            var modal = $(this).closest('.modal');
            if (modal.length > 0) {
                hideModal(modal.attr('id'));
            }
        });

        // Handle modal backdrop click
        $('.modal').on('click', function(e) {
            if (e.target === this) {
                console.log('Modal backdrop clicked');
                hideModal($(this).attr('id'));
            }
        });

        // Handle escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log('Escape key pressed');
                $('.modal.show').each(function() {
                    hideModal($(this).attr('id'));
                });
            }
        });

        // Test form submission binding
        console.log('Binding form submission event');
        $('#userTypeForm').off('submit').on('submit', function(e) {
            console.log('Form submission event triggered');
            e.preventDefault();
            
            var userTypeId = $('#userTypeId').val();
            var isEdit = !!userTypeId;
            
            console.log('Form submission - userTypeId:', userTypeId, 'isEdit:', isEdit);
            
            // Set the method spoofing
            if (isEdit) {
                $('#httpMethod').val('PUT');
            } else {
                $('#httpMethod').val('POST');
            }
            
            var formData = {
                ut_code: $('#ut_code').val(),
                ut_name: $('#ut_name').val(),
                ut_description: $('#ut_description').val(),
                ut_status: $('#ut_status').val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: $('#httpMethod').val()
            };

            // Use the correct URL for edit vs create
            var url = isEdit ? "{{ url('user-types') }}/" + userTypeId : "{{ url('user-types') }}";
            var method = 'POST'; // Always use POST with method spoofing

            // Debug logging
            console.log('Form Submission Debug:', {
                'userTypeId': userTypeId,
                'url': url,
                'method': method,
                'formData': formData,
                'isEdit': isEdit,
                'httpMethod': $('#httpMethod').val()
            });

            // Validate form data
            if (!formData.ut_code || !formData.ut_name || !formData.ut_status) {
                alert('Please fill in all required fields');
                return;
            }

            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Success Response:', response);
                    if (response.success) {
                        // Reload DataTable
                        if (window.userTypeTable && typeof window.userTypeTable.ajax !== 'undefined') {
                            window.userTypeTable.ajax.reload();
                        } else {
                            location.reload();
                        }
                        
                        // Reset form and hide modal
                        $('#userTypeForm')[0].reset();
                        $('#userTypeId').val(''); // Clear the ID
                        hideModal('userTypeModal');
                        
                        // Show success message
                        alert('User type ' + (isEdit ? 'updated' : 'created') + ' successfully');
                    } else {
                        alert('Failed to ' + (isEdit ? 'update' : 'create') + ' user type: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        'xhr': xhr,
                        'status': status,
                        'error': error,
                        'responseText': xhr.responseText
                    });
                    
                    var errorMessage = 'Error occurred while ' + (isEdit ? 'updating' : 'creating') + ' user type';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });
        
        console.log('User Type JS initialization completed');
    });

    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for User Type');
        
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
            // For edit links, close menu after a short delay
            setTimeout(function() {
                $('.menu').removeClass('show');
            }, 100);
        });
        
        console.log('Simple dropdown system initialized for User Type');
    }

    // Re-initialize dropdown on each draw
    if (window.userTypeTable) {
        window.userTypeTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Edit user type function
    function editUserType(id) {
        console.log('Edit UserType called with ID:', id);
        
        $.ajax({
            url: "{{ url('user-types') }}/" + id,
            type: 'GET',
            success: function(response) {
                console.log('Edit UserType Success Response:', response);
                if (response.success) {
                    var userType = response.data;
                    console.log('UserType Data to populate form:', userType);
                    
                    $('#userTypeId').val(userType.id);
                    $('#ut_code').val(userType.ut_code);
                    $('#ut_name').val(userType.ut_name);
                    $('#ut_description').val(userType.ut_description);
                    $('#ut_status').val(userType.ut_status);
                    
                    // Update modal title
                    $('#userTypeModalTitle').text('Edit User Type');
                    
                    showModal('userTypeModal');
                    
                    console.log('Form populated with values:', {
                        'id': $('#userTypeId').val(),
                        'ut_code': $('#ut_code').val(),
                        'ut_name': $('#ut_name').val(),
                        'ut_description': $('#ut_description').val(),
                        'ut_status': $('#ut_status').val()
                    });
                } else {
                    console.error('Failed to load user type data:', response);
                    alert('Failed to load user type data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit UserType Error:', {
                    'xhr': xhr,
                    'status': status,
                    'error': error,
                    'responseText': xhr.responseText
                });
                alert('Error occurred while loading user type data');
            }
        });
    }

    // Delete user type function
    function deleteUserType(id) {
        if (confirm('Are you sure you want to delete this user type?')) {
            $.ajax({
                url: "{{ url('user-types') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Reload DataTable
                        if (window.userTypeTable && typeof window.userTypeTable.ajax !== 'undefined') {
                            window.userTypeTable.ajax.reload();
                        } else {
                            location.reload();
                        }
                        alert('User type deleted successfully');
                    } else {
                        alert('Failed to delete user type');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting user type');
                }
            });
        }
    }

    // Add new user type function
    function addUserType() {
        $('#userTypeId').val('');
        $('#userTypeForm')[0].reset();
        
        // Update modal title
        $('#userTypeModalTitle').text('Add User Type');
        
        showModal('userTypeModal');
    }
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex !important;
}

.modal-dialog {
    position: relative;
    width: auto;
    margin: 0.5rem;
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 0.3rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.5);
    outline: 0;
}

.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
}

.modal-title {
    margin-bottom: 0;
    line-height: 1.5;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}

.modal-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    padding: 0.75rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
}

.close {
    float: right;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
    background: transparent;
    border: 0;
    cursor: pointer;
}

.close:hover {
    opacity: 0.75;
}
</style>
