@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Create a new announcement to share with your team</p>
        </div>
        <div>
            <a href="{{ route('announcements_v2.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
                    
                    <div class="space-y-5">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter announcement title">
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="10" 
                                      required
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter announcement content"></textarea>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                Attachments (Optional)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <i class="cft-standard-stroke cft-paper-clip text-gray-400 text-3xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload files</span>
                                            <input id="attachments" 
                                                   name="attachments[]" 
                                                   type="file" 
                                                   multiple 
                                                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                                   class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX up to 10MB</p>
                                </div>
                            </div>
                            <div id="attachment-preview" class="mt-3 space-y-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Target & Settings Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Target & Settings</h2>
                    
                    <div class="space-y-5">
                        <!-- Target Type -->
                        <div>
                            <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Audience <span class="text-red-500">*</span>
                            </label>
                            <select id="target_type" 
                                    name="target_type" 
                                    required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Target</option>
                                <option value="all">All Users</option>
                                <option value="division">Specific Division</option>
                                <option value="individual">Specific Users</option>
                            </select>
                        </div>

                        <!-- Target Date -->
                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="target_date" 
                                   name="target_date" 
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Division Selection (hidden by default) -->
                        <div id="divisionGroup" class="hidden">
                            <label for="division_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Division <span class="text-red-500">*</span>
                            </label>
                            <select id="division_id" 
                                    name="division_id"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Individual Users Selection (hidden by default) -->
                        <div id="usersGroup" class="hidden bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Select Users <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- Filter by Division -->
                            <div class="mb-3">
                                <label for="user_division_filter" class="block text-xs text-gray-600 mb-1">Filter by Division (Optional)</label>
                                <select id="user_division_filter" 
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Divisions</option>
                                    <option value="no_division">No Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Search Users -->
                            <div class="mb-3">
                                <input type="text" 
                                       id="user_search" 
                                       placeholder="Search users by name or NIP..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    <span id="visibleUserCount">{{ count($users) }}</span> users available
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2 mb-3">
                                <button type="button" 
                                        id="selectAllBtn"
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    Select All
                                </button>
                                <button type="button" 
                                        id="clearAllBtn"
                                        class="flex-1 px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    Clear All
                                </button>
                            </div>
                            
                            <!-- Users List -->
                            <div class="border border-gray-200 bg-white rounded-lg p-2 max-h-64 overflow-y-auto">
                                <div id="usersList" class="space-y-1">
                                    @foreach($users as $user)
                                        @php
                                            $division = $divisions->firstWhere('id', $user->ud_id);
                                            $divisionName = $division ? $division->ud_name : 'No Division';
                                            $divisionId = $user->ud_id ?? '';
                                        @endphp
                                        <div class="user-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded transition-colors" 
                                             data-user-id="{{ $user->id }}" 
                                             data-user-name="{{ strtolower($user->u_name) }}" 
                                             data-user-nip="{{ $user->u_nip }}" 
                                             data-division-id="{{ $divisionId }}" 
                                             data-division-name="{{ $divisionName }}">
                                            <input type="checkbox" 
                                                   name="user_ids[]" 
                                                   value="{{ $user->id }}" 
                                                   class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $user->u_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->u_nip }} • {{ $divisionName }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Selected Count -->
                            <div class="mt-3">
                                <p class="text-xs text-gray-600">
                                    <span id="selectedCount">0</span> user(s) selected
                                </p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" 
                                    name="status"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Pinned -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_pinned" 
                                   name="is_pinned" 
                                   value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_pinned" class="ml-2 text-sm text-gray-700">
                                Pin this announcement
                            </label>
                        </div>

                        <!-- Publish Now -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="publish_now" 
                                   name="publish_now" 
                                   value="1" 
                                   checked
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="publish_now" class="ml-2 text-sm text-gray-700">
                                Publish immediately
                            </label>
                        </div>

                        <!-- Publish Date (hidden by default) -->
                        <div id="publishDateGroup" class="hidden">
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Publish Date & Time
                            </label>
                            <input type="datetime-local" 
                                   id="published_at" 
                                   name="published_at"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <button type="button" 
                    onclick="history.back()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Cancel
            </button>
            <button type="submit" 
                    id="submitBtn"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-save"></i>
                Create Announcement
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Target type change handler
    $('#target_type').on('change', function() {
        const targetType = $(this).val();
        $('#divisionGroup').addClass('hidden');
        $('#usersGroup').addClass('hidden');
        
        if (targetType === 'division') {
            $('#divisionGroup').removeClass('hidden');
        } else if (targetType === 'individual') {
            $('#usersGroup').removeClass('hidden');
        }
    });

    // Publish now checkbox handler
    $('#publish_now').on('change', function() {
        if ($(this).is(':checked')) {
            $('#publishDateGroup').addClass('hidden');
        } else {
            $('#publishDateGroup').removeClass('hidden');
        }
    });

    // File upload handling
    $('#attachments').on('change', function() {
        const files = Array.from(this.files);
        const previewContainer = $('#attachment-preview');
        previewContainer.empty();
        
        files.forEach(function(file, index) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const isImage = file.type.startsWith('image/');
            
            const filePreview = $(`
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200" data-file-index="${index}">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="cft-standard-stroke ${isImage ? 'cft-picture' : 'cft-file'} text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${fileSize} MB</p>
                    </div>
                    <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700" onclick="removeFilePreview(${index})">
                        <i class="cft-standard-stroke cft-cross"></i>
                    </button>
                </div>
            `);
            
            previewContainer.append(filePreview);
        });
    });

    // User selection functionality
    function initializeUserSelection() {
        // Division filter
        $('#user_division_filter').on('change', filterUsers);
        
        // Search
        $('#user_search').on('input', filterUsers);
        
        // Select all
        $('#selectAllBtn').on('click', function() {
            getVisibleUserCheckboxes().forEach(function(checkbox) {
                $(checkbox).prop('checked', true);
            });
            updateSelectedCount();
        });
        
        // Clear all
        $('#clearAllBtn').on('click', function() {
            $('.user-checkbox').prop('checked', false);
            updateSelectedCount();
        });
        
        // Update count on checkbox change
        $('.user-checkbox').on('change', updateSelectedCount);
        
        updateSelectedCount();
    }

    function filterUsers() {
        const selectedDivision = $('#user_division_filter').val();
        const searchTerm = $('#user_search').val().toLowerCase();
        let visibleCount = 0;
        
        $('.user-item').each(function() {
            const $item = $(this);
            const userName = $item.data('user-name') || '';
            const userNip = $item.data('user-nip') || '';
            const divisionId = $item.data('division-id') || '';
            
            let matchesDivision = true;
            if (selectedDivision) {
                if (selectedDivision === 'no_division') {
                    matchesDivision = divisionId === '';
                } else if (divisionId === '') {
                    matchesDivision = false;
                } else {
                    matchesDivision = divisionId === selectedDivision;
                }
            }
            
            const matchesSearch = !searchTerm || 
                userName.includes(searchTerm) || 
                userNip.includes(searchTerm);
            
            if (matchesDivision && matchesSearch) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });
        
        $('#visibleUserCount').text(visibleCount);
        updateSelectedCount();
    }

    function getVisibleUserCheckboxes() {
        return $('.user-item:visible .user-checkbox').toArray();
    }

    function updateSelectedCount() {
        const checkedCount = $('.user-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);
    }

    // Form submission
    $('#announcementForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const title = $('#title').val().trim();
        const content = $('#content').val().trim();
        const categoryId = $('#category_id').val();
        const targetType = $('#target_type').val();
        
        if (!title || !content || !categoryId || !targetType) {
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Validate target-specific fields
        if (targetType === 'division') {
            const divisionId = $('#division_id').val();
            if (!divisionId) {
                alert('Please select a division.');
                return false;
            }
        }
        
        if (targetType === 'individual') {
            const checkedCount = $('.user-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one user.');
                return false;
            }
        }
        
        // Show loading state
        const $submitBtn = $('#submitBtn');
        const originalHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="cft-standard-stroke cft-loading"></i> Creating...');
        
        // Submit form
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    window.location.href = '{{ route("announcements_v2.index") }}';
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error creating announcement. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('\n');
                }
                alert(errorMsg);
                $submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
        
        return false;
    });

    // Initialize user selection
    initializeUserSelection();
});

// Remove file preview
function removeFilePreview(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    $('#attachments').trigger('change');
}
</script>
@endpush
