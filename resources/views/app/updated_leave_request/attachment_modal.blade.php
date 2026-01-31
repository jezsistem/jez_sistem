<!-- Attachment View Modal -->
<div id="AttachmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeAttachmentModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="attachment_modal_title">View Attachment</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeAttachmentModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div id="attachment_content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" onclick="closeAttachmentModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Close
                </button>
                <a href="#" id="download_attachment_link" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700" download>
                    <i class="fas fa-download mr-2"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function viewAttachment(fileUrl, fileName, fileType) {
    const modal = $('#AttachmentModal');
    const modalTitle = $('#attachment_modal_title');
    const content = $('#attachment_content');
    const downloadLink = $('#download_attachment_link');
    
    modalTitle.text('View Attachment: ' + fileName);
    downloadLink.attr('href', fileUrl);
    downloadLink.attr('download', fileName);
    
    // Determine how to display the file
    if (fileType && fileType.includes('image')) {
        content.html('<img src="' + fileUrl + '" class="max-w-full h-auto" alt="' + fileName + '">');
    } else if (fileType && fileType.includes('pdf')) {
        content.html('<iframe src="' + fileUrl + '" class="w-full h-96" frameborder="0"></iframe>');
    } else {
        content.html('<div class="text-center p-8"><i class="fas fa-file text-6xl text-gray-400 mb-4"></i><p class="text-gray-600">Preview not available for this file type</p><p class="text-sm text-gray-500 mt-2">Please download to view</p></div>');
    }
    
    modal.removeClass('hidden');
}

function closeAttachmentModal() {
    $('#AttachmentModal').addClass('hidden');
}
</script>
