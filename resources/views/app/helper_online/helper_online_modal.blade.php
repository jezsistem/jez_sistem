<div class="modal fade" id="addStorageAreaModal" tabindex="-1" aria-labelledby="addStorageAreaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStorageAreaModalLabel">Tambah Area Penyimpanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="addStorageAreaForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailStorageAreaModal" tabindex="-1" aria-labelledby="detailStorageAreaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <input type="hidden" id="storage_area_id" name="storage_area_id">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_name"></h5>
                <div>
                    <button type="button" class="btn btn-dark me-2 mr-3" id="editStorageAreaBtn">Edit Area Penyimpanan</button>
                    <button type="button" class="btn btn-danger me-2 mr-10" id="deleteStorageAreaBtn">Hapus Area Penyimpanan</button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Pada Area Ini</label>
                    <div id="bin_list" class="border min-h-50px"></div>
                </div>
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Yang Akan Ditambahkan</label>
                    <div id="bin_list_temp" class="border min-h-50px"></div>
                    <button type="button" class="btn btn-primary mt-2" id="add_bin_to_area">Save Changes</button>
                </div>
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Belum Setup</label>
                    <div id="bin_list_no_area"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStorageAreaModal" tabindex="-1" aria-labelledby="editStorageAreaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStorageAreaModalLabel">Edit Area Penyimpanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editStorageAreaForm">
                @csrf
                <input type="hidden" id="edit_storage_area_id" name="storage_area_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_description" name="edit_description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="chatModalLabel">
                    Chat TRX : <span class="trx_number_title">No TRX</span>
                </h5>
                <button type="button" class="close text-danger" onclick="closeChat()" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Chat Messages Container -->
                <div class="chat-container">
                    <div class="chat-messages p-3">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Chat Input -->
                <div class="input-group w-100">
                    <input type="text" class="form-control" id="text_input" placeholder="Type your message..." onkeypress="if(event.keyCode==13){ sendChatMessage(); }">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="sendChatMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    height: 70vh;
    background-color: #f8f9fa;
}

.chat-messages {
    height: 100%;
    overflow-y: auto;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 3px;
}
</style>