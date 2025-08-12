@extends('app.structure')
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
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="fas fa-plus text-primary"></i>
                        </span>
                        <h3 class="card-label">Create New Announcement</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('announcements.manage') }}" class="btn btn-light-primary font-weight-bolder">
                            <i class="fas fa-arrow-left"></i>
                            Back to Manage
                        </a>
                    </div>
                </div>
                <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <!-- Content -->
                                <div class="form-group">
                                    <label for="content">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="8" required></textarea>
                                </div>
                                
                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category_id">Category <span class="text-danger">*</span></label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Attachments -->
                                <div class="form-group">
                                    <label for="attachments">Attachments (Optional)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        <label class="custom-file-label" for="attachments">Choose files...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Supported formats: Images (JPG, PNG, GIF), Documents (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX). Max size: 10MB per file.
                                    </small>
                                    <div id="attachment-preview" class="mt-3"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Target Type -->
                                <div class="form-group">
                                    <label for="target_type">Target Audience <span class="text-danger">*</span></label>
                                    <select class="form-control" id="target_type" name="target_type" required>
                                        <option value="">Select Target</option>
                                        <option value="all">All Users</option>
                                        <option value="division">Specific Division</option>
                                        <option value="individual">Specific Users</option>
                                    </select>
                                </div>
                                
                                <!-- Division Selection (hidden by default) -->
                                <div class="form-group" id="divisionGroup" style="display: none;">
                                    <label for="division_id">Select Division <span class="text-danger">*</span></label>
                                    <select class="form-control" id="division_id" name="division_id">
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Individual Users Selection (hidden by default) -->
                                <div class="form-group" id="usersGroup" style="display: none;">
                                    <label for="user_ids">Select Users <span class="text-danger">*</span></label>
                                    <select class="form-control" id="user_ids" name="user_ids[]" multiple>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->u_name }} ({{ $user->u_nip }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Pinned -->
                                <div class="form-group">
                                    <div class="checkbox-inline">
                                        <label class="checkbox">
                                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1">
                                            <span></span>
                                            Pin this announcement
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Publish Now -->
                                <div class="form-group">
                                    <div class="checkbox-inline">
                                        <label class="checkbox">
                                            <input type="checkbox" id="publish_now" name="publish_now" value="1" checked>
                                            <span></span>
                                            Publish immediately
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Publish Date (hidden by default) -->
                                <div class="form-group" id="publishDateGroup" style="display: none;">
                                    <label for="published_at">Publish Date & Time</label>
                                    <input type="datetime-local" class="form-control" id="published_at" name="published_at">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-light-primary font-weight-bold mr-2" onclick="history.back()">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold" id="submitBtn">
                                <i class="fas fa-save"></i>
                                Create Announcement
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
// Vanilla JavaScript version - no jQuery dependencies
(function() {
    function initializeCreateAnnouncementPage() {
        console.log('Initializing create announcement page...');
        console.log('Divisions available:', {{ count($divisions) }});
        console.log('Users available:', {{ count($users) }});
        

        
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
            console.log('Form found:', form);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            // Function to submit form via fetch API
            function submitFormViaFetch() {
                console.log('=== SUBMITTING FORM VIA FETCH API ===');
                
                // Validate required fields
                var title = document.getElementById('title').value.trim();
                var content = document.getElementById('content').value.trim();
                var categoryId = document.getElementById('category_id').value;
                var targetType = document.getElementById('target_type').value;
                
                console.log('Form values:', { title, content, categoryId, targetType });
                
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
                
                console.log('=== VALIDATION PASSED - SUBMITTING VIA FETCH ===');
                
                // Show loading state
                var submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                }
                
                // Prepare form data
                var formData = new FormData(form);
                
                // Debug form data
                console.log('=== FORM DATA BEING SENT ===');
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
                    alert('Error submitting form: ' + error.message);
                })
                .finally(function() {
                    // Reset button state
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Create Announcement';
                    }
                });
                
                return false;
            }
            
            // Add click handler to submit button ONLY (no form submit handler)
            var submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    console.log('=== SUBMIT BUTTON CLICKED ===');
                    e.preventDefault();
                    e.stopPropagation();
                    submitFormViaFetch();
                    return false;
                });
                
                console.log('Submit button click handler attached');
            }
            
            console.log('Fetch API form handlers attached');
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
        
        // Make multiple select more user-friendly by default
        var userSelect = document.getElementById('user_ids');
        if (userSelect && userSelect.multiple) {
            // Add instruction text
            var instructionDiv = document.createElement('div');
            instructionDiv.className = 'small text-muted mt-1';
            instructionDiv.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Hold Ctrl (Cmd on Mac) to select multiple users';
            userSelect.parentNode.appendChild(instructionDiv);
        }
    }
    
    // Global function for removing files
    window.removeFile = function(index) {
        var fileInput = document.getElementById('attachments');
        if (!fileInput) return;
        
        var dt = new DataTransfer();
        var files = Array.from(fileInput.files);
        
        files.forEach(function(file, i) {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        fileInput.files = dt.files;
        
        // Trigger change event to update preview
        var event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
    };
    
    // Initialize when DOM is ready (only once)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCreateAnnouncementPage);
    } else {
        initializeCreateAnnouncementPage();
    }
    
    // Global error handler
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        console.error('Error message:', e.message);
        console.error('Error filename:', e.filename);
        console.error('Error line:', e.lineno);
    });
    
})();
</script>

@endsection
