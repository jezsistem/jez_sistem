@extends('app.structure')
@section('content')

<style>
    /* Modal styles for attachment preview */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fefefe;
        padding: 0;
        border: 1px solid #888;
        width: 90%;
        max-width: 800px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 15px;
    }

    .modal-footer {
        padding: 15px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }

    .close:hover {
        color: #000;
    }

    .modal-open {
        overflow: hidden;
    }
</style>

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <!-- <li class="breadcrumb-item">
                            <a href="{{ route('announcements.index') }}" class="text-muted">Announcements</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted">{{ $announcement->title }}</span>
                        </li> -->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-12">
                    <!--begin::Announcement Card (Full View)-->
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">{{ $announcement->title }}</h3>
                            </div>
                            <div class="card-toolbar">
                                @if($announcement->is_pinned)
                                    <span class="d-flex align-items-center badge badge-light-blue mr-1" style="color: #007bff !important;">
                                        <i class="ki-outline ki-pin mr-2 fs-7" style="color: #007bff;"></i> Pinned
                                    </span>
                                @endif
                                @if($announcement->category)
                                    <span class="badge badge-pill font-weight-bold mr-2" 
                                          style="background-color: {{ $announcement->category->color }}15; color: {{ $announcement->category->color }};">
                                        {{ $announcement->category->name }}
                                    </span>
                                @endif
                                <span class="badge badge-light-green">
                                    {{ ucfirst($announcement->target_type) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Sender Info -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40 symbol-light-primary mr-3">
                                <span class="symbol symbol-lg-35 symbol-25 symbol-primary">
                                    <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($announcement->creator->u_name, 0, 1) }}</span>
                                </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold fs-6">{{ $announcement->creator->u_name ?? 'Unknown' }}</span>
                                    <span class="text-muted fs-7">{{ $announcement->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="text-gray-600 fs-5 mb-6 mt-6">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                            
                            <!-- Attachments -->
                            @if($announcement->attachments && $announcement->attachments->count() > 0)
                                <div class="pb-2 pt-2">
                                    <div class="d-flex flex-wrap">
                                        @foreach($announcement->attachments as $attachment)
                                            <div class="mr-3 mb-2">
                                                <button type="button" class="btn btn-light-primary btn-sm" onclick="viewAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->file_type }}')">
                                                    <i class="ki-outline ki-file"></i> {{ $attachment->original_name }}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        
                            
                            
                            <!-- @if($announcement->userReactions && $announcement->userReactions->count() > 0)
                                <div class="mb-2 mt-4">
                                    <h6 class="text-dark fw-bold mb-3">Reactions:</h6>
                                    <div class="d-flex flex-wrap">
                                        @foreach($announcement->userReactions->groupBy('reaction.name') as $reactionName => $reactions)
                                            <div class="mr-3 mb-2">
                                                <span class="badge badge-light-primary">
                                                    {{ $reactionName }} ({{ $reactions->count() }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif -->
                            
                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center mt-4 pt-4 border-top border-gray-200">
                                <button class="btn btn-sm btn-light mr-2" onclick="showReactionDetails({{ $announcement->id }})">
                                    <i class="ki-solid ki-emoji-happy text-gray-600"></i> View Reactions ({{ $announcement->userReactions->count() }})
                                </button>
                                <button class="btn btn-sm btn-light mr-2" onclick="showViewDetails({{ $announcement->id }})">
                                    <i class="ki-solid ki-eye text-gray-600"></i> Viewers ({{ $announcement->views_count ?? 0 }})
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Announcement Card-->
                </div>
                
                <!--begin::Sidebar-->
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="d-none d-lg-block">
                        <!-- Sender Info Card -->
                        <div class="card card-custom mb-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Sender Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50 symbol-light-primary mr-4">
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-primary">
                                        <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($announcement->creator->u_name, 0, 1) }}</span>
                                    </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold fs-6">{{ $announcement->creator->u_name ?? 'Unknown' }}</span>
                                        <span class="text-muted fs-8">{{ $announcement->division_name ?? 'Unknown' }}</span>
                                        <span class="text-muted fs-8">Created at: {{ $announcement->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Target Date</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold fs-6">
                                            {{ 
                                                $announcement->target_date 
                                                    ? \Carbon\Carbon::parse($announcement->target_date)->translatedFormat('l, d F Y') 
                                                    : ($announcement->published_at 
                                                        ? \Carbon\Carbon::parse($announcement->published_at)->translatedFormat('l, d F Y') 
                                                        : 'Unknown'
                                                    ) 
                                            }}
                                        </span>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recipients Info Card -->
                        <div class="card card-custom mb-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Target Audience</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($announcement->target_type === 'all')
                                    <div class="d-flex align-items-center">
                                        <!-- <div class="symbol symbol-40 symbol-light-primary mr-3">
                                            <span class="symbol-label">
                                                <i class="ki-outline ki-users text-primary"></i>
                                            </span>
                                        </div> -->
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">All Users</span>
                                            <span class="text-muted fs-7">Visible to all users in the system</span>
                                        </div>
                                    </div>
                                @elseif($announcement->target_type === 'division')
                                    @if($announcement->recipients && $announcement->recipients->count() > 0)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-4">
                                                <!-- <div class="symbol symbol-35 symbol-light-primary mr-2">
                                                    <span class="symbol-label">
                                                        <i class="ki-outline ki-abstract-26 text-primary"></i>
                                                    </span>
                                                </div> -->
                                                <span class="text-dark fw-bolder fs-6">Divisions ({{ $announcement->recipients->count() }})</span>
                                            </div>
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                @foreach($announcement->recipients as $recipient)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="symbol symbol-lg-35 symbol-25 symbol-light-primary mr-4">
                                                            <span class="symbol-label font-size-h5 font-weight-bold">
                                                            {{ substr($recipient->recipient->ud_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-dark fw-bold fs-6">{{ $recipient->recipient->ud_name ?? 'Unknown Division' }}</span>
                                                            <span class="text-muted fs-7">{{ $recipient->recipient->ud_description ?? 'No description' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <!-- <div class="symbol symbol-40 symbol-light-primary mr-3">
                                                <span class="symbol-label">
                                                    <i class="ki-outline ki-abstract-26 text-primary"></i>
                                                </span>
                                            </div> -->
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bold fs-6">Division</span>
                                                <span class="text-muted fs-7">No specific divisions assigned</span>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($announcement->target_type === 'individual')
                                    @if($announcement->recipients && $announcement->recipients->count() > 0)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-4">
                                                <!-- <div class="symbol symbol-30 symbol-light-primary mr-2">
                                                    <span class="symbol-label">
                                                        <i class="ki-outline ki-user text-primary"></i>
                                                    </span>
                                                </div> -->
                                                <span class="text-dark fw-bolder fs-6">Individual Users ({{ $announcement->recipients->count() }})</span>
                                            </div>
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                @foreach($announcement->recipients as $recipient)
                                                    <div class="d-flex align-items-center mb-2">
                                                    <div class="symbol symbol-lg-35 symbol-25 symbol-light-primary mr-4">
                                                    <span class="symbol-label font-size-h5 font-weight-bold">
                                                            {{ substr($recipient->recipient->u_name, 0, 1)}}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-dark fw-bold fs-6">{{ $recipient->recipient->u_name ?? 'Unknown User' }}</span>
                                                            <span class="text-muted fs-7">{{ $recipient->recipient->userDivision->ud_name ?? 'No division' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <!-- <div class="symbol symbol-40 symbol-light-primary mr-3">
                                                <span class="symbol-label">
                                                    <i class="ki-outline ki-user text-primary"></i>
                                                </span>
                                            </div> -->
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bold fs-6">Individual</span>
                                                <span class="text-muted fs-7">No specific users assigned</span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40 symbol-light-primary mr-3">
                                            <span class="symbol-label">
                                                <i class="ki-outline ki-information text-primary"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold fs-6">{{ ucfirst($announcement->target_type) }}</span>
                                            <span class="text-muted fs-7">Custom target audience</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Announcement Info Card -->
                        <div class="card card-custom">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Announcement Info</h3>
                                </div>
                            </div>
                            <div class="card-body pt-4 pb-6">
                                <div class="list-group list-group-flush fs-6">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Status</span>
                                        <span class="badge badge-{{ $announcement->status == 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($announcement->status) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Pinned</span>
                                        <span class="badge badge-{{ $announcement->is_pinned ? 'primary' : 'secondary' }}">
                                            {{ $announcement->is_pinned ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Target</span>
                                        <span class="badge badge-info">
                                            {{ ucfirst($announcement->target_type) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Views</span>
                                        <span class="badge badge-light-primary">
                                            {{ $announcement->views_count ?? 0 }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Reactions</span>
                                        <span class="badge badge-light-primary">
                                            {{ $announcement->userReactions->count() }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Attachments</span>
                                        <span class="badge badge-light-primary">
                                            {{ $announcement->attachments->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!--begin::Actions Card-->
                        <div class="card card-custom mt-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Actions</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <a href="{{ route('announcements.manage') }}" class="btn btn-secondary mb-2">
                                        <i class="ki-outline ki-arrow-left"></i> Back to Manage
                                    </a>
                                    @if(auth()->id() == $announcement->created_by)
                                        <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-light-warning mb-2">
                                            <i class="ki-outline ki-pencil"></i> Edit Announcement
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--end::Actions Card-->
                    </div>
                </div>
                <!--end::Sidebar-->
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 80vh;">
            <div class="modal-header bg-light border-0 py-4">
                <h5 class="modal-title text-dark font-weight-bold" id="viewDetailsModalLabel">
                    Announcement Viewers
                </h5>
                <button type="button" class="close border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;">
                    <i class="ki ki-close text-muted"></i>
                </button>
            </div>
            <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                <div id="viewDetailsContent" class="p-4">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-white font-weight-bold px-4 text-dark" onclick="closeModal('viewDetailsModal')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reaction Details Modal -->
<div class="modal fade" id="reactionDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 80vh;">
            <div class="modal-header bg-light border-0 py-4">
                <h5 class="modal-title text-dark font-weight-bold" id="reactionDetailsModalTitle">
                    Announcement Reactions
                </h5>
                <button type="button" class="close border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;">
                    <i class="ki ki-close text-muted"></i>
                </button>
            </div>
            <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                <div id="reactionDetailsContent" class="p-4">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-white font-weight-bold px-4 text-dark" onclick="closeModal('reactionDetailsModal')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Attachment View Modal -->
<div id="attachmentModal" class="modal">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 90vh; min-width: 30vw;">
            <div class="modal-header bg-light border-0 py-4">
                <h5 class="modal-title" id="attachmentModalLabel">View Attachment</h5>
                <button type="button" class="close" onclick="hideModal('attachmentModal')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="attachmentContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('attachmentModal')">Close</button>
                <a href="#" id="downloadAttachment" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>

@endsection

@include('app._partials.js')

<script>
// Function to view attachment
function viewAttachment(filePath, originalName, fileType) {
    console.log('Viewing attachment:', filePath, originalName, fileType);
    
    const modal = document.getElementById('attachmentModal');
    const content = document.getElementById('attachmentContent');
    const downloadLink = document.getElementById('downloadAttachment');
    const modalLabel = document.getElementById('attachmentModalLabel');
    
    // Set modal title
    modalLabel.textContent = originalName;
    
    // Set download link
    downloadLink.href = '/storage/' + filePath;
    downloadLink.download = originalName;
    
    // Clear previous content
    content.innerHTML = '';
    
    // Check file type and display accordingly
    if (fileType && fileType.startsWith('image/')) {
        // Display image
        const img = document.createElement('img');
        img.src = '/storage/' + filePath;
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
        img.alt = originalName;
        content.appendChild(img);
    } else if (fileType && fileType.startsWith('video/')) {
        // Display video
        const video = document.createElement('video');
        video.src = '/storage/' + filePath;
        video.controls = true;
        video.style.maxWidth = '100%';
        video.style.height = 'auto';
        content.appendChild(video);
    } else if (fileType && fileType.startsWith('audio/')) {
        // Display audio
        const audio = document.createElement('audio');
        audio.src = '/storage/' + filePath;
        audio.controls = true;
        audio.style.width = '100%';
        content.appendChild(audio);
    } else if (fileType && fileType === 'application/pdf') {
        // Display PDF
        const iframe = document.createElement('iframe');
        iframe.src = '/storage/' + filePath;
        iframe.style.width = '100%';
        iframe.style.height = '500px';
        iframe.style.border = 'none';
        content.appendChild(iframe);
    } else {
        // Display file info for other types
        const fileInfo = document.createElement('div');
        fileInfo.innerHTML = `
            <div class="text-center">
                <i class="ki-outline ki-file text-primary" style="font-size: 4rem;"></i>
                <h5 class="mt-3">${originalName}</h5>
                <p class="text-muted">File type: ${fileType || 'Unknown'}</p>
                <p class="text-muted">Click download to view this file</p>
            </div>
        `;
        content.appendChild(fileInfo);
    }
    
    // Show modal
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}

// Function to hide modal
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const attachmentModal = document.getElementById('attachmentModal');
    if (event.target === attachmentModal) {
        hideModal('attachmentModal');
    }
}

// Add event listeners for modal close buttons
document.addEventListener('DOMContentLoaded', function() {
    // Close button for reaction details modal
    const reactionCloseBtn = document.querySelector('#reactionDetailsModal .close');
    if (reactionCloseBtn) {
        reactionCloseBtn.addEventListener('click', function() {
            closeModal('reactionDetailsModal');
        });
    }
    
    // Close button for view details modal
    const viewCloseBtn = document.querySelector('#viewDetailsModal .close');
    if (viewCloseBtn) {
        viewCloseBtn.addEventListener('click', function() {
            closeModal('viewDetailsModal');
        });
    }
    
    // Close modal when clicking outside
    const reactionModal = document.getElementById('reactionDetailsModal');
    if (reactionModal) {
        reactionModal.addEventListener('click', function(event) {
            if (event.target === reactionModal) {
                closeModal('reactionDetailsModal');
            }
        });
    }
    
    const viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('click', function(event) {
            if (event.target === viewModal) {
                closeModal('viewDetailsModal');
            }
        });
    }
});

// Function to show reaction details
window.showReactionDetails = function(announcementId) {
    fetch(`/announcements/${announcementId}/reactions`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('reactionDetailsContent').innerHTML = data.html;
            
            var modal = document.getElementById('reactionDetailsModal');
            if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                $(modal).modal('show');
            } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            } else {
                console.warn('No modal framework available, using basic display');
                modal.style.display = 'block';
                modal.classList.add('show');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('role', 'dialog');
                
                if (!document.getElementById('manual-backdrop')) {
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.id = 'manual-backdrop';
                    document.body.appendChild(backdrop);
                    document.body.classList.add('modal-open');
                }
            }
        } else {
            alert('Error loading reaction details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading reaction details');
    });
};

// Function to show view details
window.showViewDetails = function(announcementId) {
    // Show loading state
    document.getElementById('viewDetailsContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    // Show modal - try multiple approaches
    const modal = document.getElementById('viewDetailsModal');
    
    // Try jQuery first
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $('#viewDetailsModal').modal('show');
    }
    // Try Bootstrap 5
    else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
    // Fallback to vanilla JavaScript
    else {
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        
        if (!document.getElementById('manual-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'manual-backdrop';
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');
        }
    }
    
    // Load viewer details
    fetch(`/announcements/${announcementId}/viewers`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('viewDetailsContent').innerHTML = data.html;
        } else {
            document.getElementById('viewDetailsContent').innerHTML = '<div class="text-center text-danger">Error loading viewer details</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('viewDetailsContent').innerHTML = '<div class="text-center text-danger">Error loading viewer details</div>';
    });
};

// Global function to close modals
window.closeModal = function(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $(modal).modal('hide');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    } else {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
        
        var backdrop = document.getElementById('manual-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
    }
};
</script>r