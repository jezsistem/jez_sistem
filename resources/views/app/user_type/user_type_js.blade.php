<script>
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

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var userTypeTable = $('#userTypeTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('user-types/datatables') }}",
                data : function (d) {
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
            },
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#user_type_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                userTypeTable.draw(false);
            }, 300);
        });

        // Form submission
        $('#userTypeForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                ut_code: $('#ut_code').val(),
                ut_name: $('#ut_name').val(),
                ut_description: $('#ut_description').val(),
                ut_status: $('#ut_status').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            var userTypeId = $('#userTypeId').val();
            var url = userTypeId ? "{{ url('user-types') }}/" + userTypeId : "{{ url('user-types') }}";
            var method = userTypeId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                beforeSend: function() {
                    $('#saveUserTypeBtn').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        hideModal('userTypeModal');
                        $('#userTypeForm')[0].reset();
                        $('#userTypeId').val('');
                        userTypeTable.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    var message = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        message = Object.values(errors).flat().join('\n');
                    }
                    alert('Error: ' + message);
                },
                complete: function() {
                    $('#saveUserTypeBtn').prop('disabled', false).text('Save User Type');
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

    // Add user type function
    function addUserType() {
        $('#userTypeModalTitle').text('Add User Type');
        $('#userTypeForm')[0].reset();
        $('#userTypeId').val('');
        $('#ut_status').val('active');
        showModal('userTypeModal');
    }

    // Edit user type function
    function editUserType(id) {
        $('#userTypeModalTitle').text('Edit User Type');
        
        // Get data from table row
        var table = $('#userTypeTable').DataTable();
        var row = table.row(function(idx, data, node) {
            return data.id == id;
        }).data();
        
        if (row) {
            $('#userTypeId').val(row.id);
            $('#ut_code').val(row.ut_code);
            $('#ut_name').val(row.ut_name);
            $('#ut_description').val(row.ut_description || '');
            $('#ut_status').val(row.ut_status);
            showModal('userTypeModal');
        }
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
                        alert(response.message);
                        $('#userTypeTable').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    var message = 'An error occurred while deleting user type';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert('Error: ' + message);
                }
            });
        }
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
