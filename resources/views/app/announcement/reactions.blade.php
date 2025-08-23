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
    #reactionsTable td {
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

    /* Modal styling */
    .modal-header .close {
        cursor: pointer;
        padding: 0;
        margin: 0;
        background: none;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
    }

    .modal-header .close:hover {
        opacity: 0.75;
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
                        <h3 class="card-label">Announcement Reactions</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="#" class="btn btn-primary font-weight-bolder" onclick="addReaction()">
                            <span class="svg-icon svg-icon-md">
                                <i class="fas fa-plus"></i>
                            </span>
                            Add Reaction
                        </a>
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
                                            <input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" />
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Search Form-->
                    
                    <!--begin::Datatable-->
                    <table class="table table-bordered table-hover table-checkable" id="reactionsTable">
                        <thead>
                            <tr>
                                <th>Sort Order</th>
                                <th>Name</th>
                                <th>Emoji</th>
                                <th>Color</th>
                                <th>Hide Announcement</th>
                                <th>Status</th>
                                <th>Usage Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reactions as $reaction)
                            <tr>
                                <td>{{ $reaction->sort_order }}</td>
                                <td>{{ $reaction->name }}</td>
                                <td class="text-center">
                                    <span style="font-size: 1.5em;">{{ $reaction->emoji }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $reaction->color }}; color: white;">
                                        {{ $reaction->color }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $reaction->hide_announcement ? 'warning' : 'info' }}">
                                        {{ $reaction->hide_announcement ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $reaction->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($reaction->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">
                                        {{ $reaction->user_reactions_count ?? 0 }} uses
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
                                                <a href="#" class="menu-link px-3" onclick="editReaction({{ $reaction->id }}, '{{ $reaction->name }}', '{{ $reaction->emoji }}', '{{ $reaction->color }}', {{ $reaction->sort_order }}, {{ $reaction->hide_announcement ? 'true' : 'false' }}, '{{ $reaction->status }}')">
                                                    Edit
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3 text-danger" onclick="deleteReaction({{ $reaction->id }})">
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
                        {{ $reactions->links() }}
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

<!-- Add/Edit Reaction Modal -->
<div class="modal fade" id="reactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reactionModalTitle">Add Reaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="reactionForm">
                <div class="modal-body">
                    <input type="hidden" id="reactionId" name="id">
                    
                    <div class="form-group">
                        <label for="reactionName">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reactionName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reactionEmoji">Emoji</label>
                        <input type="text" class="form-control" id="reactionEmoji" name="emoji" placeholder="e.g., 😀 👍 ❤️">
                    </div>
                    
                    <div class="form-group">
                        <label for="reactionColor">Color <span class="text-danger">*</span></label>
                        <input type="color" class="form-control" id="reactionColor" name="color" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reactionSortOrder">Sort Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="reactionSortOrder" name="sort_order" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-inline">
                            <label class="checkbox">
                                <input type="checkbox" id="reactionHideAnnouncement" name="hide_announcement" value="1">
                                <span></span>
                                Hide announcement after this reaction
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="statusGroup" style="display: none;">
                        <label for="reactionStatus">Status</label>
                        <select class="form-control" id="reactionStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" onclick="closeReactionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global modal handling functions for reactions
window.closeReactionModal = function() {
    var modal = document.getElementById('reactionModal');
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $(modal).modal('hide');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        } else {
            modal.style.display = 'none';
            modal.classList.remove('show');
        }
    } else {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        var backdrop = document.getElementById('manual-backdrop');
        if (backdrop) backdrop.remove();
    }
};

// Close modal when clicking on backdrop or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('reactionModal');
    if (modal) {
        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeReactionModal();
            }
        });
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                closeReactionModal();
            }
        });
    }
    
    // Initialize dropdown menu system
    initializeSimpleDropdown();
});

// Simple working dropdown solution
function initializeSimpleDropdown() {
    console.log('Initializing simple dropdown system for Announcement Reactions');
    
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
    
    console.log('Simple dropdown system initialized for Announcement Reactions');
}

// Global function definitions
window.addReaction = function() {
    console.log('Add reaction clicked');
    
    document.getElementById('reactionModalTitle').textContent = 'Add Reaction';
    document.getElementById('reactionForm').reset();
    document.getElementById('reactionId').value = '';
    document.getElementById('statusGroup').style.display = 'none';
    
    // Show modal with multiple attempts
    var modal = document.getElementById('reactionModal');
    
    // Try jQuery first if available
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $(modal).modal('show');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Try Bootstrap 5
        var modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    } else {
        // Fallback to basic display
        console.warn('No modal framework available, using basic display');
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        
        // Add backdrop
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'manual-backdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
};

// Add editReaction to global scope
window.editReaction = function(id, name, emoji, color, sortOrder, hideAnnouncement, status) {
    console.log('Edit reaction clicked for ID:', id);
    
    document.getElementById('reactionModalTitle').textContent = 'Edit Reaction';
    document.getElementById('reactionId').value = id;
    document.getElementById('reactionName').value = name;
    document.getElementById('reactionEmoji').value = emoji;
    document.getElementById('reactionColor').value = color;
    document.getElementById('reactionSortOrder').value = sortOrder;
    document.getElementById('reactionHideAnnouncement').checked = hideAnnouncement;
    document.getElementById('reactionStatus').value = status;
    document.getElementById('statusGroup').style.display = 'block';
    
    // Show modal with multiple attempts
    var modal = document.getElementById('reactionModal');
    
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
        
        // Add backdrop if not exists
        if (!document.getElementById('manual-backdrop')) {
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'manual-backdrop';
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');
        }
    }
};

// Add deleteReaction to global scope
window.deleteReaction = function(id) {
    if (confirm('Are you sure you want to delete this reaction?')) {
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/announcement-reactions/${id}`, {
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
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting reaction');
        });
    }
};

// Wait for DOM ready with multiple fallbacks for form handling
function initializeReactionFormHandling() {
    var form = document.getElementById('reactionForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = document.getElementById('reactionId').value;
        var url = id ? `/announcement-reactions/${id}` : '/announcement-reactions';
        var method = id ? 'PUT' : 'POST';
        
        // Add CSRF token and method override
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', token);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        // Handle checkbox - if not checked, explicitly set to 0
        if (!document.getElementById('reactionHideAnnouncement').checked) {
            formData.append('hide_announcement', '0');
        }
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal using our closeReactionModal function
                closeReactionModal();
                
                // Show success message
                var successMsg = document.createElement('div');
                successMsg.innerHTML = '✓ ' + data.message;
                successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
                document.body.appendChild(successMsg);
                setTimeout(() => document.body.removeChild(successMsg), 3000);
                
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving reaction');
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeReactionFormHandling);
} else {
    initializeReactionFormHandling();
}
</script>

@endsection
@include('app._partials.js')
