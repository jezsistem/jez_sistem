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
        background-color: rgba(0,0,0,.02);
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
        left: 0 !important;
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
                        <span class="card-icon">
                            <i class="fas fa-bullhorn text-primary"></i>
                        </span>
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
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search announcements..." id="kt_datatable_search_query" />
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 my-2 my-md-0">
                                        <select class="form-control" id="categoryFilter">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 my-2 my-md-0">
                                        <select class="form-control" id="statusFilter">
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
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
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40 symbol-light-primary mr-4">
                                            <span class="symbol-label">
                                                <i class="fas fa-bullhorn text-primary"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold text-hover-primary fs-6">{{ $announcement->title }}</span>
                                            <span class="text-muted fw-semibold fs-7">{{ Str::limit($announcement->content, 50) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $announcement->category->color }}; color: white;">
                                        {{ $announcement->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ ucfirst($announcement->target_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $announcement->status == 'active' ? 'success' : 'danger' }}">
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
                                    <span class="text-muted fw-semibold fs-7">
                                        {{ $announcement->creator->u_name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('announcements.edit', $announcement->id) }}" class="menu-link px-3">
                                                    Edit
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3 text-danger" onclick="deleteAnnouncement({{ $announcement->id }}, '{{ $announcement->title }}')">
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
// Vanilla JavaScript version
(function() {
    function initializeManageAnnouncementPage() {
        console.log('Initializing manage announcement page...');
        
        // Filter functionality
        var categoryFilter = document.getElementById('categoryFilter');
        var statusFilter = document.getElementById('statusFilter');
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                console.log('Category filter changed:', this.value);
                // TODO: Implement filtering logic
            });
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                console.log('Status filter changed:', this.value);
                // TODO: Implement filtering logic
            });
        }
        
        // Search functionality
        var searchInput = document.getElementById('kt_datatable_search_query');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                console.log('Search:', this.value);
                // TODO: Implement search logic
            });
        }
    }
    
    // Global function for deleting announcements
    window.deleteAnnouncement = function(id, title) {
        if (confirm('Are you sure you want to delete the announcement "' + title + '"? This action cannot be undone.')) {
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
                    successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
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
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeManageAnnouncementPage);
    } else {
        initializeManageAnnouncementPage();
    }
    
    window.addEventListener('load', initializeManageAnnouncementPage);
})();

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
        
        console.log('Dropdown clicked, menu found:', $menu.length);
        
        // Close all other menus first
        $('.menu').not($menu).removeClass('show');
        
        // Toggle current menu
        $menu.toggleClass('show');
        
        console.log('Menu toggled, has show class:', $menu.hasClass('show'));
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
</script>

@endsection
@include('app._partials.js')
