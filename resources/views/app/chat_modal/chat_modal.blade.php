<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="chatModalLabel">
                    Chat TRX : <span class="trx_number_title">No TRX</span>
                </h5>
                <button type="button" class="close text-danger close-modal" onclick="closeChat()" aria-label="Close">
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
                    <button class="btn btn-secondary mr-2" type="button" data-toggle="modal"
                        data-target="#uploadFileModal">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="text" class="form-control" id="text_input" placeholder="Type your message..."
                        onkeypress="if(event.keyCode==13){ sendChatMessage(); }">
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
<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadFileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFileModalLabel">Send File/Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="file_input">Choose File/Image</label>
                    <div class="drop-zone" id="dropZone">
                        <div class="drop-zone-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <p class="mb-2">Drag & drop file here or click to browse</p>
                            <small class="text-muted">Supported: Images, PDF, Word, Excel (Max 3MB)</small>
                        </div>
                        <input type="file" class="form-control-file d-none" id="file_input">
                    </div>
                    <div id="filePreview" class="mt-3 d-none">
                        <div class="alert alert-info d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file mr-2"></i>
                                <span id="fileName"></span>
                                <small class="text-muted ml-2" id="fileSize"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="file_caption">Caption (Optional)</label>
                    <textarea class="form-control" id="file_caption" rows="3" placeholder="Add a caption..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendFileMessage()">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
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

    .drop-zone {
        border: 2px dashed #007bff;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .drop-zone:hover {
        border-color: #0056b3;
        background-color: #e7f3ff;
    }

    .drop-zone.dragover {
        border-color: #28a745;
        background-color: #d4edda;
    }

    .drop-zone-content {
        pointer-events: none;
    }
</style>

@include('app.chat_modal.chat_modal_js')
