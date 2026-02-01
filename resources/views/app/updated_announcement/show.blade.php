@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $announcement->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('announcements_v2.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-arrow-left"></i>
                Back to Announcements
            </a>
            @if(auth()->id() == $announcement->created_by)
                <a href="{{ route('announcements_v2.edit', $announcement->id) }}" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                    <i class="cft-standard-stroke cft-edit"></i>
                    Edit
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Announcement Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <!-- Header -->
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            @if($announcement->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="cft-standard-stroke cft-pin"></i>
                                    Pinned
                                </span>
                            @endif
                            @if($announcement->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $announcement->category->color }}15; color: {{ $announcement->category->color }};">
                                    {{ $announcement->category->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($announcement->target_type) }}
                            </span>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $announcement->title }}</h2>
                    </div>
                </div>

                <!-- Sender Info -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-bold text-lg">
                            {{ substr($announcement->creator->u_name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $announcement->creator->u_name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="prose max-w-none mb-6">
                    {!! nl2br(e($announcement->content)) !!}
                </div>

                <!-- Attachments -->
                @if($announcement->attachments && $announcement->attachments->count() > 0)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h6 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="cft-standard-stroke cft-paper-clip"></i>
                        Attachments ({{ $announcement->attachments->count() }})
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($announcement->attachments as $attachment)
                        <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                @if($attachment->is_image)
                                    <i class="cft-standard-stroke cft-picture text-blue-600"></i>
                                @else
                                    <i class="cft-standard-stroke cft-file text-blue-600"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->original_name }}</p>
                                <p class="text-xs text-gray-500">{{ $attachment->file_size_human }}</p>
                            </div>
                            <button onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')" 
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors">
                                View
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button onclick="showReactionDetails({{ $announcement->id }})" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-emoji-happy"></i>
                        View Reactions ({{ $announcement->userReactions->count() }})
                    </button>
                    <button onclick="showViewDetails({{ $announcement->id }})" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-eye"></i>
                        Viewers ({{ $announcement->views_count ?? 0 }})
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Sender Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-bold text-lg">
                            {{ substr($announcement->creator->u_name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900">{{ $announcement->creator->u_name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500">{{ $announcement->division_name ?? 'Unknown Division' }}</p>
                        <p class="text-xs text-gray-400 mt-1">Created: {{ $announcement->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Target Date Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Target Date</h3>
                <p class="text-gray-900 font-medium">
                    {{ 
                        $announcement->target_date 
                            ? \Carbon\Carbon::parse($announcement->target_date)->translatedFormat('l, d F Y') 
                            : ($announcement->published_at 
                                ? \Carbon\Carbon::parse($announcement->published_at)->translatedFormat('l, d F Y') 
                                : 'Unknown'
                            ) 
                    }}
                </p>
            </div>

            <!-- Target Audience Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Target Audience</h3>
                @if($announcement->target_type === 'all')
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="cft-standard-stroke cft-users text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">All Users</p>
                            <p class="text-sm text-gray-500">Visible to all users in the system</p>
                        </div>
                    </div>
                @elseif($announcement->target_type === 'division')
                    @if($announcement->recipients && $announcement->recipients->count() > 0)
                        <p class="text-sm text-gray-600 mb-3">Divisions ({{ $announcement->recipients->count() }})</p>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($announcement->recipients as $recipient)
                            <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-xs">
                                        {{ substr($recipient->recipient->ud_name ?? 'D', 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $recipient->recipient->ud_name ?? 'Unknown Division' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $recipient->recipient->ud_description ?? 'No description' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No specific divisions assigned</p>
                    @endif
                @elseif($announcement->target_type === 'individual')
                    @if($announcement->recipients && $announcement->recipients->count() > 0)
                        <p class="text-sm text-gray-600 mb-3">Individual Users ({{ $announcement->recipients->count() }})</p>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($announcement->recipients as $recipient)
                            <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-xs">
                                        {{ substr($recipient->recipient->u_name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $recipient->recipient->u_name ?? 'Unknown User' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $recipient->recipient->userDivision->ud_name ?? 'No division' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No specific users assigned</p>
                    @endif
                @endif
            </div>

            <!-- Announcement Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Announcement Info</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($announcement->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pinned</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->is_pinned ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $announcement->is_pinned ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Target</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ ucfirst($announcement->target_type) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Views</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $announcement->views_count ?? 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Reactions</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $announcement->userReactions->count() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Attachments</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $announcement->attachments->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Viewers Modal -->
<div id="viewersModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeViewersModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Viewers</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeViewersModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-4 md:p-5" id="viewersContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reaction Details Modal -->
<div id="reactionDetailsModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeReactionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Reaction Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeReactionModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-4 md:p-5" id="reactionDetailsContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attachment Viewer Modal -->
<div id="attachmentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAttachmentModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2" id="attachmentModalLabel">
                    <i class="cft-standard-stroke cft-paper-clip text-blue-600"></i>
                    View Attachment
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeAttachmentModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto p-6" id="attachmentContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading attachment...</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeAttachmentModal()">
                    Close
                </button>
                <a href="#" id="downloadAttachment" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2" download>
                    <i class="cft-standard-stroke cft-cloud-download"></i>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Reuse functions from index.blade.php
// Show reaction details
window.showReactionDetails = function(announcementId) {
    $.ajax({
        url: '{{ url("announcements") }}/' + announcementId + '/reactions',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.html) {
                $('#reactionDetailsContent').html(response.html);
                const modalElement = document.getElementById('reactionDetailsModal');
                modalElement.classList.remove('hidden');
                modalElement.setAttribute('aria-hidden', 'false');
                $('body').addClass('overflow-hidden');
            } else {
                $('#reactionDetailsContent').html('<div class="text-center py-8"><p class="text-gray-500">No reaction details available</p></div>');
            }
        },
        error: function() {
            $('#reactionDetailsContent').html('<div class="text-center py-8"><p class="text-red-500">Error loading reaction details</p></div>');
        }
    });
};

// Show viewers
window.showViewDetails = function(announcementId) {
    $.ajax({
        url: '{{ url("announcements") }}/' + announcementId + '/viewers',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.html) {
                $('#viewersContent').html(response.html);
                const modalElement = document.getElementById('viewersModal');
                modalElement.classList.remove('hidden');
                modalElement.setAttribute('aria-hidden', 'false');
                $('body').addClass('overflow-hidden');
            } else {
                $('#viewersContent').html('<div class="text-center py-8"><p class="text-gray-500">No viewers data available</p></div>');
            }
        },
        error: function() {
            $('#viewersContent').html('<div class="text-center py-8"><p class="text-red-500">Error loading viewers</p></div>');
        }
    });
};

// Close modals
window.closeReactionModal = function() {
    const modalElement = document.getElementById('reactionDetailsModal');
    modalElement.classList.add('hidden');
    modalElement.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
};

window.closeViewersModal = function() {
    const modalElement = document.getElementById('viewersModal');
    modalElement.classList.add('hidden');
    modalElement.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
};

// Format file size helper
function formatFileSize(bytes) {
    if (!bytes) return 'Unknown size';
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// View announcement attachment
window.viewAnnouncementAttachment = function(filePath, fileName, mimeType, fileSize) {
    const modalElement = document.getElementById('attachmentModal');
    const contentElement = document.getElementById('attachmentContent');
    const downloadLink = document.getElementById('downloadAttachment');
    const modalTitle = document.getElementById('attachmentModalLabel');
    
    const fileSizeNum = typeof fileSize === 'string' ? parseInt(fileSize) : fileSize;
    modalTitle.innerHTML = '<i class="cft-standard-stroke cft-paper-clip text-blue-600"></i> View Attachment: ' + fileName;
    
    const storageUrl = '{{ url("storage") }}/' + filePath;
    downloadLink.href = storageUrl;
    downloadLink.download = fileName;
    
    modalElement.classList.remove('hidden');
    modalElement.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
    
    contentElement.innerHTML = '<div class="flex items-center justify-center py-8"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div><p class="mt-2 text-sm text-gray-500">Loading attachment...</p></div></div>';
    
    setTimeout(function() {
        if (mimeType && mimeType.includes('image')) {
            contentElement.innerHTML = `
                <div class="text-center">
                    <img src="${storageUrl}" alt="${fileName}" class="mx-auto rounded-lg shadow-lg" style="max-height: 60vh; max-width: 100%;">
                    <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        } else if (mimeType && mimeType.includes('pdf')) {
            contentElement.innerHTML = `
                <div class="text-center">
                    <iframe src="${storageUrl}" width="100%" height="600" frameborder="0" class="rounded-lg"></iframe>
                    <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        } else {
            contentElement.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="cft-standard-stroke cft-file text-blue-600 text-2xl"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900 mb-2">${fileName}</h5>
                    <p class="text-gray-600 mb-2">This file type cannot be previewed directly.</p>
                    <p class="text-gray-500 text-sm mb-4">Please download the file to view its contents.</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        }
    }, 100);
};

window.closeAttachmentModal = function() {
    const modalElement = document.getElementById('attachmentModal');
    modalElement.classList.add('hidden');
    modalElement.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
};

// Close modals with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!$('#reactionDetailsModal').hasClass('hidden')) {
            closeReactionModal();
        }
        if (!$('#viewersModal').hasClass('hidden')) {
            closeViewersModal();
        }
        if (!$('#attachmentModal').hasClass('hidden')) {
            closeAttachmentModal();
        }
    }
});
</script>
@endpush
