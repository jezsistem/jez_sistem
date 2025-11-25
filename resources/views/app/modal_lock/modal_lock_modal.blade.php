<!-- Modal Config -->
<div class="modal fade" id="modalConfig" tabindex="-1" aria-labelledby="modalConfigLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfigLabel">Configuration</h5>
                <button type="button" class="close close_modal_config" >
                   <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="config_table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Value</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal_config" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addConfig()">Add New</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Allowed Models -->
<div class="modal fade" id="modalAllowedModels" tabindex="-1" aria-labelledby="modalAllowedModelsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAllowedModelsLabel">Allowed Models</h5>
                <button type="button" class="close close_modal_allowed_models" >
                   <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="allowed_models_table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Type</th>
                                <th>Model</th>
                                <th>Identifier</th>
                                <th>Is Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal_allowed_models" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addAllowedModel()">Add New</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Config -->
<div class="modal fade" id="modalAddEditConfig" tabindex="-1" aria-labelledby="modalAddEditConfigLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddEditConfigLabel">Add Configuration</h5>
                <button type="button" class="close close_modal_add_edit_config" >
                   <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="formConfig">
                    <input type="hidden" id="configAction" name="action" value="add">
                    <input type="hidden" id="configId" name="config_id">
                    
                    <div class="mb-3">
                        <label for="configName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="configName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="configValue" class="form-label">Value</label>
                        <input type="text" class="form-control" id="configValue" name="value" required>
                    </div>

                    <div class="mb-3">
                        <label for="configDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="configDescription" name="description" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal_add_edit_config">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveConfig()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Allowed Model -->
<div class="modal fade" id="modalAddEditAllowedModel" tabindex="-1" aria-labelledby="modalAddEditAllowedModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddEditAllowedModelLabel">Add Allowed Model</h5>
                <button type="button" class="close close_modal_add_edit_allowed_model" >
                   <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAllowedModel">
                    <input type="hidden" id="allowedModelAction" name="action" value="add">
                    <input type="hidden" id="allowedModelId" name="allowed_model_id">

                    <div class="mb-3">
                        <label for="allowedModelType" class="form-label">Type</label>
                        <input type="text" class="form-control" id="allowedModelType" name="model_type" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allowedModelName" class="form-label">Model</label>
                        <input type="text" class="form-control" id="allowedModelName" name="model_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allowedModelIdentifier" class="form-label">Identifier</label>
                        <input type="text" class="form-control" id="allowedModelIdentifier" name="identifier" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allowedModelIsActive" class="form-label">Is Active</label>
                        <select class="form-select form-control" id="allowedModelIsActive" name="is_active" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal_add_edit_allowed_model">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAllowedModel()">Save</button>
            </div>
        </div>
    </div>
</div>