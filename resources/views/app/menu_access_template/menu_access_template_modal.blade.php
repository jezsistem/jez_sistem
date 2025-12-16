<!--begin::Modal - Create/Edit Template-->
<div class="modal fade" id="createTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create Menu Access Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="templateForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="template_id" id="templateId" value="">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="template_name" id="templateName" placeholder="e.g., Admin Template, User Template" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Division <span class="text-danger">*</span></label>
                        <select class="form-control" name="division_id" id="divisionId" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="templateDescription" rows="3" placeholder="Template description (optional)"></textarea>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="form-group">
                        <div class="border rounded p-3 bg-light">
                            <label class="font-weight-bold mb-2">Selected Menu Items (<span id="selectedCount">0</span>)</label>
                            <div id="selectedMenus" class="d-flex flex-wrap gap-2">
                                <span class="text-muted">No menu items selected</span>
                            </div>
                        </div>

                        <label class="mt-5">Select Menu Access <span class="text-danger">*</span></label>
                        <small class="form-text text-muted mb-3">Click on menu items to add them to the template</small>
                        
                        <div class="mb-4" id="menu_access_items">
                            <!-- Menu items will be loaded here via JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="submitBtn">
                        <i class="fas fa-save"></i> <span id="submitBtnText">Create Template</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->

<script>
// Function to open modal for creating
function openCreateModal() {
    $('#modalTitle').text('Create Menu Access Template');
    $('#submitBtnText').text('Create Template');
    $('#formMethod').val('POST');
    $('#templateId').val('');
    $('#templateForm')[0].reset();
    $('#selectedMenus').html('<span class="text-muted">No menu items selected</span>');
    $('#selectedCount').text('0');
    $('#createTemplateModal').modal('show');
}

// Function to open modal for editing
function openEditModal(templateData) {
    $('#modalTitle').text('Edit Menu Access Template');
    $('#submitBtnText').text('Update Template');
    $('#formMethod').val('PUT');
    $('#templateId').val(templateData.id);
    $('#templateName').val(templateData.template_name);
    $('#divisionId').val(templateData.division_id);
    $('#templateDescription').val(templateData.description);
    
    // Load selected menus if available
    if (templateData.selected_menus) {
        // Populate selected menus - adjust based on your data structure
        // updateSelectedMenus(templateData.selected_menus);
    }
    
    $('#createTemplateModal').modal('show');
}
</script>