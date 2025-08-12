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
                            <i class="fas fa-tags text-primary"></i>
                        </span>
                        <h3 class="card-label">Announcement Categories</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="#" class="btn btn-primary font-weight-bolder" onclick="addCategory()">
                            <span class="svg-icon svg-icon-md">
                                <i class="fas fa-plus"></i>
                            </span>
                            Add Category
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
                    <table class="table table-bordered table-hover table-checkable" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th>Announcements Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?: '-' }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                        {{ $category->color }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $category->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">
                                        {{ $category->announcements_count ?? 0 }} announcements
                                    </span>
                                </td>
                                <td nowrap="nowrap">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->color }}', '{{ $category->status }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-clean btn-icon" onclick="deleteCategory({{ $category->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--end::Datatable-->
                    
                    <!--begin::Pagination-->
                    <div class="d-flex justify-content-center">
                        {{ $categories->links() }}
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

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="id">
                    
                    <div class="form-group">
                        <label for="categoryName">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoryColor">Color <span class="text-danger">*</span></label>
                        <input type="color" class="form-control" id="categoryColor" name="color" required>
                    </div>
                    
                    <div class="form-group" id="statusGroup" style="display: none;">
                        <label for="categoryStatus">Status</label>
                        <select class="form-control" id="categoryStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Wait for full page load with multiple fallbacks
(function() {
    function initializePage() {
        console.log('Initializing categories page...');
        
        // Global function definitions
        window.addCategory = function() {
            console.log('Add category clicked');
            
            document.getElementById('categoryModalTitle').textContent = 'Add Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('statusGroup').style.display = 'none';
            
            // Show modal with multiple attempts
            var modal = document.getElementById('categoryModal');
            
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
    }
    
    // Multiple ways to ensure page is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePage);
    } else {
        initializePage();
    }
    
    // Also try window load as backup
    window.addEventListener('load', initializePage);
})();

// Add editCategory to global scope as well
window.editCategory = function(id, name, description, color, status) {
    console.log('Edit category clicked for ID:', id);
    
    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryDescription').value = description;
    document.getElementById('categoryColor').value = color;
    document.getElementById('categoryStatus').value = status;
    document.getElementById('statusGroup').style.display = 'block';
    
    // Show modal with multiple attempts
    var modal = document.getElementById('categoryModal');
    
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

// Add deleteCategory to global scope
window.deleteCategory = function(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        // Use fetch for more reliable AJAX
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/announcement-categories/${id}`, {
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
            alert('Error deleting category');
        });
    }
};

// Wait for DOM ready with multiple fallbacks
function initializeFormHandling() {
    var form = document.getElementById('categoryForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = document.getElementById('categoryId').value;
        var url = id ? `/announcement-categories/${id}` : '/announcement-categories';
        var method = id ? 'PUT' : 'POST';
        
        // Add CSRF token and method override
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', token);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
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
                // Hide modal
                var modal = document.getElementById('categoryModal');
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $(modal).modal('hide');
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    var backdrop = document.getElementById('manual-backdrop');
                    if (backdrop) backdrop.remove();
                }
                
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
            alert('Error saving category');
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFormHandling);
} else {
    initializeFormHandling();
}
</script>

@endsection
