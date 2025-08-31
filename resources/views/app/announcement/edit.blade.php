@extends('app.structure')

<style>
.user-item {
    padding: 10px;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
    border-radius: 4px;
    margin-bottom: 2px;
}

.user-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.user-item label {
    margin-bottom: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    width: 100%;
}

.user-item input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.1);
}

.user-item strong {
    color: #495057;
    font-weight: 600;
}

.user-item small {
    font-size: 0.85em;
}

.user-item .badge {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}

#usersList {
    max-height: 300px;
    overflow-y: auto;
}

#usersList::-webkit-scrollbar {
    width: 6px;
}

#usersList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#usersList::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#usersList::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.btn-group-sm .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

#user_division_filter,
#user_search {
    font-size: 1rem;
}

.selected-users-summary {
    background-color: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 4px;
    padding: 8px;
    margin-top: 10px;
}

#selectedUsersTags .badge {
    transition: all 0.2s ease;
    border: 1px solid #007bff;
}

#selectedUsersTags .badge:hover {
    background-color: #0056b3 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

#selectedUsersTags .badge i {
    transition: all 0.2s ease;
}

#selectedUsersTags .badge:hover i {
    transform: scale(1.1);
}

#selectedUsersContainer {
    max-height: 200px;
    overflow-y: auto;
}
</style>

@section('content')

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
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        <a href="{{ route('announcements.index') }}" class="btn btn-light-primary font-weight-bolder">
                            <i class="fas fa-arrow-left"></i>
                            Back
                        </a>
                    </div>
                </div>
                <form id="announcementForm" method="POST" action="{{ route('announcements.update', $announcement->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $announcement->title }}" required>
                                </div>
                                
                                <!-- Content -->
                                <div class="form-group">
                                    <label for="content">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="8" required>{{ $announcement->content }}</textarea>
                                </div>
                                
                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category_id">Category <span class="text-danger">*</span></label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == $announcement->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Current Attachments -->
                                @if($announcement->attachments->count() > 0)
                                <div class="form-group">
                                    <label>Current Attachments</label>
                                    <div class="border rounded p-3 bg-light">
                                        @foreach($announcement->attachments as $attachment)
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    @if($attachment->is_image)
                                                        <i class="ki-outline ki-image text-primary mr-2"></i>
                                                    @else
                                                        <i class="ki-outline ki-file text-secondary mr-2"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $attachment->original_name }}</strong><br>
                                                        <small class="text-muted">
                                                            @if($attachment->file_size)
                                                                {{ number_format($attachment->file_size / 1024, 2) }} KB
                                                            @endif
                                                            • {{ strtoupper($attachment->file_type) }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-dark" onclick="viewAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', {{ $attachment->file_size }})">
                                                        <i class="ki-outline ki-eye"></i> View
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeAttachment({{ $attachment->id }})">
                                                        <i class="ki-outline ki-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- New Attachments -->
                                <div class="form-group">
                                    <label for="attachments">
                                        @if($announcement->attachments->count() > 0)
                                            Update Attachments (Optional)
                                        @else
                                            Attachments (Optional)
                                        @endif
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        <label class="custom-file-label" for="attachments">Choose files...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        @if($announcement->attachments->count() > 0)
                                            <strong>Note:</strong> If you upload new files, they will replace the current attachments
                                        @else
                                            Supported formats: Images (JPG, PNG, GIF), Documents (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX). Max size: 10MB per file.
                                        @endif
                                    </small>
                                    <div id="attachment-preview" class="mt-3"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <!-- Target Type -->
                                <div class="form-group">
                                    <label for="target_type">Target Audience <span class="text-danger">*</span></label>
                                    <select class="form-control" id="target_type" name="target_type" required>
                                        <option value="">Select Target</option>
                                        <option value="all" {{ $announcement->target_type == 'all' ? 'selected' : '' }}>All Users</option>
                                        <option value="division" {{ $announcement->target_type == 'division' ? 'selected' : '' }}>Specific Division</option>
                                        <option value="individual" {{ $announcement->target_type == 'individual' ? 'selected' : '' }}>Specific Users</option>
                                    </select>
                                </div>
                                
                                <!-- Division Selection -->
                                <div class="form-group" id="divisionGroup" style="display: {{ $announcement->target_type == 'division' ? 'block' : 'none' }};">
                                    <label for="division_id">Select Division</label>
                                    <select class="form-control" id="division_id" name="division_id">
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" {{ $announcement->recipients->where('recipient_type', 'division')->where('recipient_id', $division->id)->first() ? 'selected' : '' }}>
                                                {{ $division->ud_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Individual Users Selection -->
                                <div class="form-group bg-light p-4 rounded border" id="usersGroup" style="display: {{ $announcement->target_type == 'individual' ? 'block' : 'none' }};">
                                    <label for="user_ids">Select Users <span class="text-danger">*</span></label>
                                    <div class="mb-3">
                                        <label for="user_division_filter" class="small text-muted">Filter by Division (Optional)</label>
                                        <select class="form-control" id="user_division_filter">
                                            <option value="">All Divisions</option>
                                            <option value="no_division">No Division</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Search Users -->
                                    <div class="mb-5">
                                        <input type="text" class="form-control" id="user_search" placeholder="Search users by name or NIP...">
                                        <small class="form-text text-muted">
                                            <span id="visibleUserCount">{{ count($users) }}</span> users available
                                        </small>
                                    </div>
                                    
                                    <!-- User Selection Methods -->
                                    <div class="mb-3">
                                        <div class="w-100" role="group">
                                            <button type="button" class="btn btn-primary mr-3" id="selectAllBtn">Select All</button>
                                            <button type="button" class="btn btn-dark" id="clearAllBtn">Clear All</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Users List -->
                                    <div class="border bg-white rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                        <div id="usersList">
                                            @foreach($users as $user)
                                                @php
                                                    // Get division name from divisions collection
                                                    $division = $divisions->firstWhere('id', $user->ud_id);
                                                    $divisionName = $division ? $division->ud_name : 'No Division';
                                                    $divisionId = $user->ud_id ?? '';
                                                    $isSelected = $announcement->recipients->where('recipient_type', 'user')->where('recipient_id', $user->id)->first() ? true : false;
                                                @endphp
                                                <div class="user-item" data-user-id="{{ $user->id }}" data-user-name="{{ strtolower($user->u_name) }}" data-user-nip="{{ $user->u_nip }}" data-division-id="{{ $divisionId }}" data-division-name="{{ $divisionName }}">
                                                    <label class="checkbox-inline mb-2">
                                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox" {{ $isSelected ? 'checked' : '' }}>
                                                        <span></span>
                                                        <strong>{{ $user->u_name }}</strong>
                                                        <small class="text-muted ml-2">({{ $user->u_nip }})</small>
                                                        <small class="badge badge-light ml-2">{{ $divisionName }}</small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <!-- Selected Users Summary -->
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <span id="selectedCount">{{ $announcement->recipients->where('recipient_type', 'user')->count() }}</span> user(s) selected
                                        </small>
                                    </div>
                                    
                                    <!-- Selected Users Tags -->
                                    <div class="mt-4" id="selectedUsersTags" style="display: {{ $announcement->recipients->where('recipient_type', 'user')->count() > 0 ? 'block' : 'none' }};">
                                        <label class="small text-muted mb-2">Selected Users:</label>
                                        <div class="d-flex flex-wrap gap-1" id="selectedUsersContainer">
                                            @foreach($announcement->recipients->where('recipient_type', 'user') as $recipient)
                                                @php
                                                    $user = $users->firstWhere('id', $recipient->recipient_id);
                                                @endphp
                                                @if($user)
                                                    <span class="badge badge-primary d-flex align-items-center" data-user-id="{{ $user->id }}">
                                                        {{ $user->u_name }}
                                                        <i class="ki-outline ki-cross ml-1" onclick="removeUser({{ $user->id }})" style="cursor: pointer;"></i>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active" {{ $announcement->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $announcement->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Pinned -->
                                <div class="form-group">
                                    <div class="checkbox-inline">
                                        <label class="checkbox">
                                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1" {{ $announcement->is_pinned ? 'checked' : '' }}>
                                            <span></span>
                                            Pin this announcement
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Publish Now -->
                                <div class="form-group">
                                    <div class="checkbox-inline">
                                        <label class="checkbox">
                                            <input type="checkbox" id="publish_now" name="publish_now" value="1" {{ !$announcement->published_at ? 'checked' : '' }}>
                                            <span></span>
                                            Publish immediately
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Publish Date -->
                                <div class="form-group" id="publishDateGroup" style="display: {{ $announcement->published_at ? 'block' : 'none' }};">
                                    <label for="published_at">Publish Date & Time</label>
                                    <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                                           value="{{ $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                
                                <!-- Created Info -->
                                <div class="form-group">
                                    <label>Created</label>
                                    <div class="text-muted fs-7">
                                        By: {{ $announcement->creator->u_name ?? 'Unknown' }}<br>
                                        On: {{ $announcement->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-light-primary font-weight-bold mr-2" onclick="history.back()">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold" id="submitBtn" onclick="console.log('Submit button clicked');">
                                <i class="fas fa-save"></i>
                                Update Announcement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
// Vanilla JavaScript version for edit announcement - FETCH API APPROACH
(function() {
    function initializeEditAnnouncementPage() {
        console.log('Initializing edit announcement page...');
        console.log('Divisions available:', {{ count($divisions) }});
        console.log('Users available:', {{ count($users) }});
        
        // Function to submit form via fetch API
        function submitEditFormViaFetch() {
            console.log('=== SUBMITTING EDIT FORM VIA FETCH API ===');
            
            // Validate required fields
            var title = document.getElementById('title').value.trim();
            var content = document.getElementById('content').value.trim();
            var categoryId = document.getElementById('category_id').value;
            var targetType = document.getElementById('target_type').value;
            
            console.log('Edit form values:', { title, content, categoryId, targetType });
            
            if (!title || !content || !categoryId || !targetType) {
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Validate target-specific fields
            if (targetType === 'division') {
                var divisionId = document.getElementById('division_id').value;
                if (!divisionId) {
                    alert('Please select a division.');
                    return false;
                }
            }
            
            if (targetType === 'individual') {
                var userIds = document.getElementById('user_ids');
                var selectedUsers = Array.from(userIds.selectedOptions);
                if (selectedUsers.length === 0) {
                    alert('Please select at least one user.');
                    return false;
                }
            }
            
            console.log('=== VALIDATION PASSED - SUBMITTING EDIT VIA FETCH ===');
            
            // Show loading state
            var submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            }
            
            // Prepare form data
            var formData = new FormData(form);
            
            // Debug form data
            console.log('=== EDIT FORM DATA BEING SENT ===');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Submit via fetch API
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (response.status === 422) {
                    // Handle validation errors
                    return response.json().then(function(errorData) {
                        console.log('Validation errors:', errorData);
                        var errorMessage = 'Validation errors:\n';
                        if (errorData.errors) {
                            for (var field in errorData.errors) {
                                errorMessage += field + ': ' + errorData.errors[field].join(', ') + '\n';
                            }
                        }
                        alert(errorMessage);
                        throw new Error('Validation failed');
                    });
                } else if (response.redirected) {
                    console.log('Redirected to:', response.url);
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(function(data) {
                if (data) {
                    console.log('Response data:', data);
                    // If no redirect, try to parse as JSON
                    try {
                        var jsonData = JSON.parse(data);
                        if (jsonData.success) {
                            window.location.href = '/announcements';
                        } else {
                            alert('Error: ' + (jsonData.message || 'Unknown error'));
                        }
                    } catch (e) {
                        console.log('Response is not JSON:', data);
                        // If response contains HTML, it might be a redirect
                        if (data.includes('window.location')) {
                            window.location.reload();
                        }
                    }
                }
            })
            .catch(function(error) {
                console.error('Fetch error:', error);
                alert('Error updating announcement: ' + error.message);
            })
            .finally(function() {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Announcement';
                }
            });
            
            return false;
        }
        
        // Target type change handler
        var targetTypeSelect = document.getElementById('target_type');
        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', function() {
                var targetType = this.value;
                console.log('Target type changed to:', targetType);
                
                // Hide all groups first
                var divisionGroup = document.getElementById('divisionGroup');
                var usersGroup = document.getElementById('usersGroup');
                
                if (divisionGroup) divisionGroup.style.display = 'none';
                if (usersGroup) usersGroup.style.display = 'none';
                
                // Show relevant group
                if (targetType === 'division' && divisionGroup) {
                    divisionGroup.style.display = 'block';
                    console.log('Showing division group');
                } else if (targetType === 'individual' && usersGroup) {
                    usersGroup.style.display = 'block';
                    console.log('Showing users group');
                    
                    // Style multiple select for better UX
                    var userSelect = document.getElementById('user_ids');
                    if (userSelect) {
                        userSelect.style.height = 'auto';
                        userSelect.style.minHeight = '120px';
                        userSelect.setAttribute('size', '8');
                        userSelect.style.width = '100%';
                    }
                }
            });
        }
        
        // Publish now checkbox handler
        var publishNowCheckbox = document.getElementById('publish_now');
        var publishDateGroup = document.getElementById('publishDateGroup');
        
        if (publishNowCheckbox && publishDateGroup) {
            publishNowCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    publishDateGroup.style.display = 'none';
                } else {
                    publishDateGroup.style.display = 'block';
                }
            });
        }
        
        // Form submission handler - FETCH API APPROACH
        var form = document.getElementById('announcementForm');
        if (form) {
            console.log('Edit form found:', form);
            console.log('Edit form action:', form.action);
            console.log('Edit form method:', form.method);
            
            // Add click handler to submit button ONLY
            var submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    console.log('=== EDIT SUBMIT BUTTON CLICKED ===');
                    e.preventDefault();
                    e.stopPropagation();
                    submitEditFormViaFetch();
                    return false;
                });
                
                console.log('Edit submit button click handler attached');
            }
            
            console.log('Fetch API edit form handlers attached');
        }
        
        // Make multiple select more user-friendly by default
        var userSelect = document.getElementById('user_ids');
        if (userSelect && userSelect.multiple) {
            // Style the existing selected options properly
            userSelect.style.height = 'auto';
            userSelect.style.minHeight = '120px';
            userSelect.setAttribute('size', '8');
            
            // Add instruction text if not exists
            var existingInstruction = userSelect.parentNode.querySelector('.text-muted');
            if (!existingInstruction) {
                var instructionDiv = document.createElement('div');
                instructionDiv.className = 'small text-muted mt-1';
                instructionDiv.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hold Ctrl (Cmd on Mac) to select multiple users';
                userSelect.parentNode.appendChild(instructionDiv);
            }
        }
        
        // File upload handling
        var fileInput = document.getElementById('attachments');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                var files = Array.from(this.files);
                var previewContainer = document.getElementById('attachment-preview');
                var label = document.querySelector('.custom-file-label');
                
                // Update label
                if (label) {
                    if (files.length > 0) {
                        label.textContent = files.length + ' file(s) selected';
                    } else {
                        label.textContent = 'Choose files...';
                    }
                }
                
                // Clear preview
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                    
                    // Show preview for each file
                    files.forEach(function(file, index) {
                        var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                        var isImage = file.type.startsWith('image/');
                        
                        var filePreview = document.createElement('div');
                        filePreview.className = 'd-flex align-items-center mb-2 p-2 border rounded';
                        
                        filePreview.innerHTML = 
                            '<div class="mr-3">' +
                                (isImage ? 
                                    '<i class="fas fa-image text-primary"></i>' : 
                                    '<i class="fas fa-file text-secondary"></i>') +
                            '</div>' +
                            '<div class="flex-grow-1">' +
                                '<div class="font-weight-bold">' + file.name + '</div>' +
                                '<small class="text-muted">' + fileSize + ' MB</small>' +
                            '</div>' +
                            '<button type="button" class="btn btn-sm btn-danger" onclick="removeFile(' + index + ')">' +
                                '<i class="fas fa-times"></i>' +
                            '</button>';
                        
                        previewContainer.appendChild(filePreview);
                    });
                }
            });
        }
    }
    
    // Initialize when DOM is ready (only once)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEditAnnouncementPage);
    } else {
        initializeEditAnnouncementPage();
    }
    
    // Global error handler
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        console.error('Error message:', e.message);
        console.error('Error filename:', e.filename);
        console.error('Error line:', e.lineno);
    });
    
    // Global function for removing user selection
    window.removeUserSelection = function(userId) {
        var userItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (userItem) {
            var checkbox = userItem.querySelector('.user-checkbox');
            if (checkbox) {
                checkbox.checked = false;
                // Trigger change event to update counts and counts
                var event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        }
    };
    
    // Global function for removing file from preview
    window.removeFile = function(index) {
        var fileInput = document.getElementById('attachments');
        var previewContainer = document.getElementById('attachment-preview');
        var label = document.querySelector('.custom-file-label');
        
        if (fileInput && fileInput.files) {
            // Create new FileList without the removed file
            var dt = new DataTransfer();
            var files = Array.from(fileInput.files);
            
            files.forEach(function(file, i) {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            fileInput.files = dt.files;
            
            // Update label
            if (label) {
                if (dt.files.length > 0) {
                    label.textContent = dt.files.length + ' file(s) selected';
                } else {
                    label.textContent = 'Choose new files...';
                }
            }
            
            // Trigger change event to update preview
            var event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    };

    // Modal functions for attachment preview
    window.showModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            document.body.classList.add('modal-open');
        }
    };

    window.hideModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');
            document.body.classList.remove('modal-open');
            
            // Remove all backdrops to prevent issues
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                backdrop.remove();
            });
        }
    };

    // Global function for viewing attachment
    window.viewAttachment = function(filePath, fileName, fileType, fileSize) {
        const modal = document.getElementById('attachmentModal');
        const content = document.getElementById('attachmentContent');
        const downloadLink = document.getElementById('downloadAttachment');
        const modalTitle = document.getElementById('attachmentModalLabel');
        
        // Set modal title
        modalTitle.textContent = `View Attachment: ${fileName}`;
        
        // Set download link
        downloadLink.href = `/storage/${filePath}`;
        downloadLink.download = fileName;
        
        // Clear previous content
        content.innerHTML = '';
        
        // Show loading
        content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading attachment...</p></div>';
        
        // Show modal
        showModal('attachmentModal');
        
        // Load attachment content based on file type
        if (fileType && fileType.includes('image')) {
            // For images, show directly
            content.innerHTML = `
                <div class="text-center">
                    <img src="/storage/${filePath}" alt="${fileName}" class="img-fluid" style="max-height: 500px;">
                    <p class="mt-2"><strong>${fileName}</strong></p>
                    <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                </div>
            `;
        } else if (fileType && fileType.includes('pdf')) {
            // For PDFs, show in iframe
            content.innerHTML = `
                <div class="text-center">
                    <iframe src="/storage/${filePath}" width="100%" height="500" frameborder="0"></iframe>
                    <p class="mt-2"><strong>${fileName}</strong></p>
                    <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                </div>
            `;
        } else {
            // For other file types, show file info
            content.innerHTML = `
                <div class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-file fa-3x mb-3"></i>
                        <h5>${fileName}</h5>
                        <p>This file type cannot be previewed directly.</p>
                        <p>Please download the file to view its contents.</p>
                        <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                    </div>
                </div>
            `;
        }
    };

    // Toast message function with fallback
    function showToastMessage(title, message, type) {
        // Try to use toast function if available
        if (typeof toast === 'function') {
            toast(title, message, type);
        } else {
            // Fallback to alert if toast is not available
            alert(`${title}: ${message}`);
        }
    }

    // Function to format file size
    function formatFileSize(bytes) {
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

    // Global function for removing attachment
    window.removeAttachment = function(attachmentId) {
        if (confirm('Are you sure you want to remove this attachment? This action cannot be undone.')) {
            // Send AJAX request to remove attachment
            fetch('/announcements/attachment/' + attachmentId + '/remove', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the attachment element from DOM
                    const attachmentElement = document.querySelector(`[onclick="removeAttachment(${attachmentId})"]`).closest('.d-flex.align-items-center.justify-content-between');
                    attachmentElement.remove();
                    
                    // Check if no more attachments
                    const currentAttachments = document.querySelectorAll('.border.rounded.p-3.bg-light .d-flex.align-items-center.justify-content-between');
                    if (currentAttachments.length === 0) {
                        // Hide the current attachments section
                        const currentAttachmentsSection = document.querySelector('.border.rounded.p-3.bg-light').closest('.form-group');
                        currentAttachmentsSection.style.display = 'none';
                        
                        // Update label for new attachments
                        const newAttachmentsLabel = document.querySelector('label[for="attachments"]');
                        if (newAttachmentsLabel) {
                            newAttachmentsLabel.textContent = 'Attachments (Optional)';
                        }
                        
                        // Update help text
                        const helpText = document.querySelector('.form-text.text-muted');
                        if (helpText) {
                            helpText.innerHTML = 'Supported formats: Images (JPG, PNG, GIF), Documents (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX). Max size: 10MB per file.';
                        }
                    }
                    
                    // Show success message
                    showToastMessage('Success', 'Attachment removed successfully!', 'success');
                } else {
                    showToastMessage('Error', 'Error removing attachment: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToastMessage('Error', 'Error removing attachment. Please try again.', 'error');
            });
        }
    };

})();
</script>

<!-- Attachment Preview Modal -->
<div id="attachmentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 85vh;">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title text-dark font-weight-bold" id="attachmentModalLabel">
                    <i class="fas fa-paperclip text-warning mr-2"></i>View Attachment
                </h5>
                <button type="button" class="close border-0 bg-transparent" onclick="hideModal('attachmentModal')" aria-label="Close" style="font-size: 1.5rem;">
                    <i class="ki ki-close text-muted"></i>
                </button>
            </div>
            <div class="modal-body p-2" style="max-height: 70vh; overflow-y: auto;">
                <div id="attachmentContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-white font-weight-bold px-4 mr-2" onclick="hideModal('attachmentModal')">
                    <i class="ki-solid ki-cross-square mr-1"></i>Close
                </button>
                <a href="#" id="downloadAttachment" class="btn btn-dark font-weight-bold px-4" download>
                    <i class="ki-solid ki-cloud-download mr-1"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
@include('app._partials.js')
