<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
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

@include('app.chat_modal.chat_modal_js')