@extends('app.structure')
@section('content')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .btn-group-vertical .btn {
            margin-bottom: 2px;
            width: 30px;
            height: 30px;
            padding: 5px;
            font-size: 12px;
        }

        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }

        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 12px;
        }

        .table td {
            font-size: 12px;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .02);
        }

        /* Custom CSS for Metronic dropdown menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .menu.menu-sub-dropdown {
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            margin-top: 5px !important;
            min-width: 150px !important;
            background: white !important;
            border: 1px solid #e4e6ef !important;
            border-radius: 0.475rem !important;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }

        /* Fix for menu positioning in table cells */
        #announcementsTable td {
            position: relative;
        }

        /* Modal styles for attachment preview */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
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

        /* Menu item styling */
        .menu-item .menu-link {
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #3f4254 !important;
            font-weight: 500;
            font-size: 1rem;
        }

        .menu-item .menu-link:hover {
            background-color: #f3f6f9 !important;
            color: #3699FF !important;
        }

        .menu-item .menu-link.text-danger {
            color: #f64e60 !important;
        }

        .menu-item .menu-link.text-danger:hover {
            background-color: #ffe2e5 !important;
            color: #f64e60 !important;
        }

        /* Button styling for menu trigger */
        [data-kt-menu-trigger="click"] {
            cursor: pointer;
            user-select: none;
        }

        /* SVG icon styling */
        .svg-icon {
            display: inline-block;
            vertical-align: middle;
        }

        .svg-icon svg {
            width: 1em;
            height: 1em;
        }

        /* Fallback menu system styles */
        .menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .menu:not(.show) {
            display: none !important;
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
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
                <!--begin::Toolbar-->
                <div class="d-flex align-items-center">
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary font-weight-bolder">
                        <span class="svg-icon svg-icon-md">
                            <i class="fas fa-plus"></i>
                        </span>
                        Create Announcement
                    </a>
                </div>
                <!--end::Toolbar-->
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
                            <h3 class="card-label">Manage Announcements</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="input-icon position-relative">
                                                <input type="text" class="form-control" id="searchInput"
                                                    placeholder="Search by title, content, or sender..."
                                                    value="{{ request('search') }}" />
                                                <span class="input-icon-addon">
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-clean position-absolute"
                                                    id="clearSearchBtn"
                                                    style="right: 5px; top: 50%; transform: translateY(-50%); display: none;">
                                                    <i class="ki-outline ki-cross text-muted"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <select class="form-control" id="categoryFilter">
                                                <option value="">All Categories</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <select class="form-control" id="divisionFilter">
                                                <option value="">All Divisions</option>
                                                @foreach ($divisions as $division)
                                                    <option value="{{ $division->id }}"
                                                        {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                        {{ $division->ud_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <select class="form-control" id="statusFilter">
                                                <option value="">All Status</option>
                                                <option value="active"
                                                    {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive"
                                                    {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-xl-4 text-right">
                                    <button type="button" class="btn btn-primary mr-2" id="searchBtn">
                                        <i class="ki-outline ki-magnifier"></i>
                                        Search
                                    </button>
                                    <button type="button" class="btn btn-light" id="resetBtn">
                                        <i class="ki-outline ki-refresh"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ki-outline ki-information-5"></i>
                                    Type to search automatically, or use filters and click search
                                </small>
                            </div>
                        </div>
                        <!--end::Search Form-->

                        <!--begin::Datatable-->
                        <table class="table table-bordered table-hover table-checkable" id="announcementsTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Pinned</th>
                                    <th>Published</th>
                                    <th>Reactions</th>
                                    <th>Attachments</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($announcements as $announcement)
                                    <tr>
                                        <td style="cursor: pointer;"
                                            onclick="window.location.href='{{ route('announcements.show', $announcement->id) }}'"
                                            onmouseover="this.style.backgroundColor='#f8f9fa'"
                                            onmouseout="this.style.backgroundColor=''">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 symbol-light-primary mr-4">
                                                    <span class="symbol-label">
                                                        <i class="ki-outline ki-speaker text-primary"></i>
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bold fs-6">{{ $announcement->title }}</span>
                                                    <span
                                                        class="text-muted fw-semibold fs-7">{{ Str::limit($announcement->content, 50) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $announcement->category->color }}; color: white;">
                                                {{ $announcement->category->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucfirst($announcement->target_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $announcement->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($announcement->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $announcement->is_pinned ? 'warning' : 'light' }}">
                                                {{ $announcement->is_pinned ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-semibold fs-7">
                                                {{ $announcement->published_at ? $announcement->published_at->format('M d, Y H:i') : 'Draft' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">
                                                {{ $announcement->userReactions->count() }} reactions
                                            </span>
                                        </td>
                                        <td>
                                            @if ($announcement->attachments->count() > 0)
                                                <div class="d-flex flex-column">
                                                    <!-- <span class="badge badge-info mb-1">{{ $announcement->attachments->count() }} files</span> -->
                                                    @foreach ($announcement->attachments->take(2) as $attachment)
                                                        <div class="d-flex align-items-center mb-1 justify-content-center">
                                                            <!-- @if ($attachment->is_image)
    <i class="fas fa-file-image text-primary mr-1"></i>
@elseif(strpos($attachment->mime_type, 'pdf') !== false)
    <i class="fas fa-file-pdf text-danger mr-1"></i>
@elseif(strpos($attachment->mime_type, 'word') !== false)
    <i class="fas fa-file-word text-info mr-1"></i>
@else
    <i class="fas fa-file text-secondary mr-1"></i>
    @endif -->
                                                            <button class="btn btn-xs btn-light-primary"
                                                                onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                    @if ($announcement->attachments->count() > 2)
                                                        <small
                                                            class="text-muted">+{{ $announcement->attachments->count() - 2 }}
                                                            more</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted fw-semibold fs-7">
                                                {{ $announcement->creator->u_name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Actions
                                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                                </a>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('announcements.edit', $announcement->id) }}"
                                                            class="menu-link px-3">
                                                            Edit
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            onclick="deleteAnnouncement({{ $announcement->id }}, '{{ $announcement->title }}')">
                                                            Delete
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu-->
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--end::Datatable-->

                        <!--begin::Pagination-->
                        <div class="d-flex justify-content-center">
                            {{ $announcements->links() }}
                        </div>
                        <!--end::Pagination-->
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->

    <script>
        // Global function for deleting announcements
        window.deleteAnnouncement = function(id, title) {
            if (confirm('Are you sure you want to delete the announcement "' + title +
                    '"? This action cannot be undone.')) {
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/announcements/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            var successMsg = document.createElement('div');
                            successMsg.innerHTML = '✓ Announcement deleted successfully!';
                            successMsg.style.cssText =
                                'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
                            document.body.appendChild(successMsg);
                            setTimeout(() => document.body.removeChild(successMsg), 3000);

                            // Reload page after short delay
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting announcement. Please try again.');
                    });
            }
        };

        // Simple working dropdown solution
        function initializeSimpleDropdown() {
            console.log('Initializing simple dropdown system for Announcements Manage');

            // Remove any existing event handlers
            $(document).off('click', '[data-kt-menu-trigger="click"]');

            // Add click handler for dropdown toggle
            $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $this = $(this);
                var $menu = $this.siblings('.menu');
                var $cardBody = $this.closest('.card.card-custom').find('> .card-body');
                var $row = $this.closest('tr');

                // Tutup semua menu lain
                $('.menu').not($menu).removeClass('show');
                $cardBody.removeClass('pb-extra'); // reset padding

                // Toggle menu ini
                $menu.toggleClass("show");

                // Jika menu terbuka & baris ini adalah row terakhir
                if ($menu.hasClass('show') && $row.is(':last-child')) {
                    $cardBody.addClass('pb-extra');
                }
            });

            document.getElementById('divisionFilter').addEventListener('change', function() {
                let divisionId = this.value;
                let url = new URL("{{ route('announcements.manage') }}", window.location.origin);
                if (divisionId) {
                    url.searchParams.set('division_id', divisionId);
                } else {
                    url.searchParams.delete('division_id');
                }
                window.location.href = url.toString();
            });

            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.menu').removeClass('show');
                }
            });

            // Close menu when clicking on menu items
            $(document).on('click', '.menu-link', function(e) {
                if ($(this).attr('onclick')) {
                    // For buttons with onclick, let the onclick handle it
                    return;
                }
                // For other links, close menu after a short delay
                setTimeout(function() {
                    $('.menu').removeClass('show');
                }, 100);
            });

            console.log('Simple dropdown system initialized for Announcements Manage');
        }

        // Initialize dropdown menu when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeSimpleDropdown();
        });

        // Modal functions for attachment preview
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.classList.add('modal-open');
            }
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        }

        // Function to view announcement attachment
        function viewAnnouncementAttachment(filePath, fileName, fileType, fileSize) {
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
            content.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading attachment...</p></div>';

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

        // Handle modal backdrop click and escape key
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideModal(this.id);
                    }
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal').forEach(function(modal) {
                        if (modal.classList.contains('show')) {
                            hideModal(modal.id);
                        }
                    });
                }
            });
        });
    </script>

    @include('app.announcement.ajax-search')

    <!-- Attachment Preview Modal -->
    <div id="attachmentModal" class="modal">
        <div class="modal-content" style="max-width: 1200px; max-height: 800px;">
            <div class="modal-header">
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
@endsection
@include('app._partials.js')
