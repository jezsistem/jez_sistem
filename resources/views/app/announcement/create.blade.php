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
                <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
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
                            
                            <div class="col-md-5">
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
                                
                                <!-- Target Type -->
                                <div class="form-group" style="width: 150px">
                                    <label for="target_date">Target Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="target_date" name="target_date" required>
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
                                <div class="form-group bg-light p-4 rounded border" id="usersGroup" style="display: none;">
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
                                                @endphp
                                                <div class="user-item" data-user-id="{{ $user->id }}" data-user-name="{{ strtolower($user->u_name) }}" data-user-nip="{{ $user->u_nip }}" data-division-id="{{ $divisionId }}" data-division-name="{{ $divisionName }}">
                                                    <label class="checkbox-inline mb-2">
                                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox">
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
                                            <span id="selectedCount">0</span> user(s) selected
                                        </small>
                                    </div>
                                    
                                    <!-- Selected Users Tags -->
                                    <div class="mt-4" id="selectedUsersTags" style="display: none;">
                                        <label class="small text-muted mb-2">Selected Users:</label>
                                        <div class="d-flex flex-wrap gap-1" id="selectedUsersContainer">
                                            <!-- Selected user tags will be displayed here -->
                                        </div>
                                    </div>
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
                    var checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
                    if (checkedCheckboxes.length === 0) {
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
        
        // Initialize user selection functionality
        initializeUserSelection();
        
        function initializeUserSelection() {
            var userDivisionFilter = document.getElementById('user_division_filter');
            var userSearch = document.getElementById('user_search');
            var selectAllBtn = document.getElementById('selectAllBtn');
            var clearAllBtn = document.getElementById('clearAllBtn');
            var userCheckboxes = document.querySelectorAll('.user-checkbox');
            var selectedCountSpan = document.getElementById('selectedCount');
            var visibleUserCountSpan = document.getElementById('visibleUserCount');
            
            // Division filter change handler
            if (userDivisionFilter) {
                userDivisionFilter.addEventListener('change', function() {
                    filterUsers();
                });
            }
            
            // Search input handler
            if (userSearch) {
                userSearch.addEventListener('input', function() {
                    filterUsers();
                });
            }
            
            // Select all users
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    var visibleCheckboxes = getVisibleUserCheckboxes();
                    visibleCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = true;
                    });
                    updateSelectedCount();
                });
            }
            
            // Clear all selections
            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', function() {
                    userCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });
                    updateSelectedCount();
                });
            }
            

            

            
            // Update selected count when checkboxes change
            userCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            // Initial count update
            updateSelectedCount();
            
            function filterUsers() {
                var selectedDivision = userDivisionFilter ? userDivisionFilter.value : '';
                var searchTerm = userSearch ? userSearch.value.toLowerCase() : '';
                
                var userItems = document.querySelectorAll('.user-item');
                var visibleCount = 0;
                
                userItems.forEach(function(userItem) {
                    var userName = userItem.dataset.userName || '';
                    var userNip = userItem.dataset.userNip || '';
                    var divisionId = userItem.dataset.divisionId || '';
                    
                    // Handle "No Division" case
                    var matchesDivision = true;
                    if (selectedDivision) {
                        if (selectedDivision === 'no_division') {
                            // Show only users with no division
                            matchesDivision = divisionId === '';
                        } else if (divisionId === '') {
                            // If filtering by specific division and user has no division, don't show
                            matchesDivision = false;
                        } else {
                            matchesDivision = divisionId === selectedDivision;
                        }
                    }
                    
                    var matchesSearch = !searchTerm || 
                        userName.includes(searchTerm) || 
                        userNip.includes(searchTerm);
                    
                    if (matchesDivision && matchesSearch) {
                        userItem.style.display = 'block';
                        visibleCount++;
                    } else {
                        userItem.style.display = 'none';
                    }
                });
                
                // Update visible user count
                if (visibleUserCountSpan) {
                    visibleUserCountSpan.textContent = visibleCount;
                }
                
                updateSelectedCount();
            }
            
            function getVisibleUserCheckboxes() {
                var visibleUserItems = document.querySelectorAll('.user-item[style*="display: block"], .user-item:not([style*="display: none"])');
                var visibleCheckboxes = [];
                
                visibleUserItems.forEach(function(userItem) {
                    var checkbox = userItem.querySelector('.user-checkbox');
                    if (checkbox) {
                        visibleCheckboxes.push(checkbox);
                    }
                });
                
                return visibleCheckboxes;
            }
            
            function updateSelectedCount() {
                var checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
                if (selectedCountSpan) {
                    selectedCountSpan.textContent = checkedCheckboxes.length;
                }
                
                // Update selected users tags
                updateSelectedUsersTags();
            }
            
            function updateSelectedUsersTags() {
                var selectedUsersTags = document.getElementById('selectedUsersTags');
                var selectedUsersContainer = document.getElementById('selectedUsersContainer');
                var checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
                
                if (checkedCheckboxes.length === 0) {
                    selectedUsersTags.style.display = 'none';
                    return;
                }
                
                selectedUsersTags.style.display = 'block';
                selectedUsersContainer.innerHTML = '';
                
                checkedCheckboxes.forEach(function(checkbox) {
                    var userItem = checkbox.closest('.user-item');
                    if (userItem) {
                        var userId = userItem.dataset.userId;
                        var userName = userItem.querySelector('strong').textContent;
                        var userNip = userItem.dataset.userNip;
                        var divisionName = userItem.dataset.divisionName;
                        
                        var tag = document.createElement('div');
                        tag.className = 'badge badge-primary d-flex align-items-center';
                        tag.style.cssText = 'font-size: 0.9rem; padding: 0.5rem 0.7rem; margin: 0.2rem; cursor: pointer;';
                        
                        tag.innerHTML = `
                            <span class="mr-2">${userName} (${userNip})</span>
                            <small class="text-white-50 mr-2">${divisionName}</small>
                            <i class="fas fa-times text-white" onclick="removeUserSelection('${userId}')"></i>
                        `;
                        
                        selectedUsersContainer.appendChild(tag);
                    }
                });
            }
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
    
    // Global function for removing user selection
    window.removeUserSelection = function(userId) {
        var userItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (userItem) {
            var checkbox = userItem.querySelector('.user-checkbox');
            if (checkbox) {
                checkbox.checked = false;
                // Trigger change event to update counts and tags
                var event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        }
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
@include('app._partials.js')
