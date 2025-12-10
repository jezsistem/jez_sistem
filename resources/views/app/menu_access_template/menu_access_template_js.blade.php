    <script>
        function getMenuAccessItems() {
            $.ajax({
                url: "{{ route('menu.access.templates.get_menu_accesses') }}",
                type: 'GET',
                success: function(data) {
                    $('#menu_access_items').html(data);
                },
                error: function(xhr) {
                    toastr.error('Error fetching menu access items.');
                }
            });
        }

        $(document).ready(function() {
            let selectedMenus = [];
            let isEditMode = false;

            // Initialize DataTable
            var table = $('#templateTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('menu.access.templates.datatable') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'template_name',
                        name: 'template_name'
                    },
                    {
                        data: 'division.ud_name',
                        name: 'division.ud_name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Add menu item
            $(document).on('click', '.menu-item', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                if (!selectedMenus.includes(id)) {
                    selectedMenus.push(id);
                    updateSelectedMenus();
                    $(this).addClass('active');
                }
            });

            // Remove menu item
            $(document).on('click', '.remove-menu', function() {
                const id = $(this).data('id');
                selectedMenus = selectedMenus.filter(item => item !== id);
                updateSelectedMenus();
                $(`.menu-item[data-id="${id}"]`).removeClass('active');
            });

            // Update selected menus display
            function updateSelectedMenus() {
                const container = $('#selectedMenus');
                container.empty();

                if (selectedMenus.length === 0) {
                    container.html('<span class="text-muted">No menu items selected</span>');
                } else {
                    selectedMenus.forEach(id => {
                        const name = $(`.menu-item[data-id="${id}"]`).data('name');
                        const badge = `
                            <span class="badge badge-primary mr-2 mb-2 p-2">
                                ${name}
                                <i class="fas fa-times-circle ml-2 remove-menu" data-id="${id}" style="cursor: pointer;"></i>
                            </span>
                        `;
                        container.append(badge);
                    });
                }

                $('#selectedCount').text(selectedMenus.length);
            }

            // Reset modal
            function resetModal() {
                selectedMenus = [];
                isEditMode = false;
                updateSelectedMenus();
                $('.menu-item').removeClass('active');
                $('#templateForm')[0].reset();
                $('#templateId').val('');
                $('#formMethod').val('POST');
            }

            $('#createTemplateModal').on('hidden.bs.modal', function() {
                resetModal();
            });

            // Open modal for creating
            $(document).on('click', '#open_modal_create', function() {
                resetModal();
                $('#modalTitle').text('Create Menu Access Template');
                $('#submitBtnText').text('Create Template');
                
                loadMenuAccessItems();
                jQuery.noConflict();
                $('#createTemplateModal').modal('show');
            });

            // Open modal for editing
            $(document).on('click', '#edit_template_btn', function() {
                var id = $(this).data('template_id');
                isEditMode = true;
                
                $('#modalTitle').text('Edit Menu Access Template');
                $('#submitBtnText').text('Update Template');
                $('#formMethod').val('PUT');
                $('#templateId').val(id);

                $.ajax({
                    url: "/menu-access-templates/edit/" + id,
                    type: 'GET',
                    success: function(data) {
                        $('#templateName').val(data.template_name);
                        $('#divisionId').val(data.division_id);
                        $('#templateDescription').val(data.description);
                        
                        // Load selected menus
                        if (data.menu_access_ids && data.menu_access_ids.length > 0) {
                            selectedMenus = data.menu_access_ids;
                        }
                        
                        loadMenuAccessItems(function() {
                            // Mark selected items as active after menu items are loaded
                            selectedMenus.forEach(id => {
                                $(`.menu-item[data-id="${id}"]`).addClass('active');
                            });
                            updateSelectedMenus();
                        });

                        jQuery.noConflict();
                        $('#createTemplateModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Error loading template data.');
                    }
                });
            });

            // Load menu access items
            function loadMenuAccessItems(callback) {
                $.ajax({
                    url: "{{ route('menu.access.templates.get_menu_accesses') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html = '';

                        data.menu_accesses.forEach(function(menu) {
                            html += `<div class="mb-3">
                                <h6 class="font-weight-bold">${menu.mt_title}</h6>
                                <div class="row">`;

                            menu.menu_accesses.forEach(function(access) {
                                html += `
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="menu-item p-2 border rounded" 
                                             data-id="${access.id}" 
                                             data-name="${access.ma_title}"
                                             style="cursor: pointer;">
                                            ${access.ma_title}
                                        </div>
                                    </div>`;
                            });

                            html += `</div></div>`;
                        });

                        $('#menu_access_items').html(html);
                        
                        if (callback) callback();
                    },
                    error: function(xhr) {
                        toastr.error('Error fetching menu access items.');
                    }
                });
            }

            // Form submission
            $('#templateForm').on('submit', function(e) {
                e.preventDefault();

                if (selectedMenus.length === 0) {
                    toastr.error('Please select at least one menu item');
                    return;
                }

                let formData = $(this).serializeArray();
                selectedMenus.forEach(id => {
                    formData.push({
                        name: 'menu_access_ids[]',
                        value: id
                    });
                });

                const method = $('#formMethod').val();
                const templateId = $('#templateId').val();
                const url = isEditMode ? 
                    "/menu-access-templates/update/" + templateId : 
                    "{{ route('menu.access.templates.store') }}";

                $.ajax({
                    url: url,
                    type: method === 'PUT' ? 'POST' : 'POST',
                    data: $.param(formData) + (method === 'PUT' ? '&_method=PUT' : ''),
                    success: function(response) {
                        $('#createTemplateModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(isEditMode ? 'Template updated successfully!' : 'Template created successfully!');
                        resetModal();
                    },
                    error: function(xhr) {
                        toastr.error('Error saving template. Please try again.');
                    }
                });
            });

            // View template details
            $(document).on('click', '.view-template', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "/menu-access-template/" + id,
                    type: 'GET',
                    success: function(data) {
                        $('#templateDetails').html(data);
                        $('#viewTemplateModal').modal('show');
                    }
                });
            });

            // Delete template
            $(document).on('click', '#delete_template_btn', function() {
                var id = $(this).data('template_id');

                if (confirm('Are you sure you want to delete this template?')) {
                    $.ajax({
                        url: "/menu-access-templates/delete/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload();
                            toastr.success('Template deleted successfully!');
                        },
                        error: function(xhr) {
                            toastr.error('Error deleting template.');
                        }
                    });
                }
            });
        });
    </script>
