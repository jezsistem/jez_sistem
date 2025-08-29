@extends('app.structure')

@section('content')

<div class="content-wrapper">
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

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-toolbar d-flex justify-content-between w-100">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="position_filter">Position</label>
                                        <select class="form-control" id="position_filter">
                                            <option value="">All Positions</option>
                                            @foreach($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->up_code }} - {{ $position->up_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="division_filter">Division</label>
                                        <select class="form-control" id="division_filter">
                                            <option value="">All Divisions</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-dark btn-block" onclick="applyFilters()">
                                            <i class="ki-outline ki-filter-search"></i> Apply Filters
                                        </button>
                                    </div>
                                </div> 
                                <div class="d-flex align-items-center">
                                    <input type="search" class="form-control" style="width: 300px;" id="staff_search" placeholder="Search"/>
                                </div>
                            </div>                          
                        </div>
                        <div class="card-body">
                            <!-- Filter Form -->
                           
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-checkable" id="staffTable">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Staff</th>
                                            <th>Position</th>
                                            <th>Division</th>
                                            <th>User Type</th>
                                            <th>Annual Leave Balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Position Modal -->
<div class="modal fade" id="positionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg w-100 d-flex justify-content-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Position</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="positionForm">
                <div class="modal-body">
                    <input type="hidden" id="positionUserId" name="user_id">
                    <div class="form-group">
                        <label for="up_id">Position</label>
                        <select class="form-control" id="up_id" name="up_id" required>
                            <option value="">Select Position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->up_code }} - {{ $position->up_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Position</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Division Modal -->
<div class="modal fade" id="divisionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg w-100 d-flex justify-content-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Division</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="divisionForm">
                <div class="modal-body">
                    <input type="hidden" id="divisionUserId" name="user_id">
                    <div class="form-group">
                        <label for="ud_id">Division</label>
                        <select class="form-control" id="ud_id" name="ud_id" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Type Modal -->
<div class="modal fade" id="userTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg w-100 d-flex justify-content-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update User Type</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="userTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="userTypeUserId" name="user_id">
                    <div class="form-group">
                        <label for="ut_id">User Type</label>
                        <select class="form-control" id="ut_id" name="ut_id" required>
                            <option value="">Select User Type</option>
                            @foreach($userTypes as $userType)
                                <option value="{{ $userType->id }}">{{ $userType->ut_code }} - {{ $userType->ut_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leave Balance Modal -->
<div class="modal fade" id="leaveBalanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg w-100 d-flex justify-content-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Annual Leave Balance</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="leaveBalanceForm">
                <div class="modal-body">
                    <input type="hidden" id="leaveBalanceUserId" name="user_id">
                    <div class="form-group">
                        <label for="lb_remaining_balance">Remaining Days</label>
                        <input type="number" class="form-control" id="lb_remaining_balance" name="lb_remaining_balance" min="0" required>
                        <small class="form-text text-muted">Enter the remaining annual leave days for this year</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Leave Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
    max-width: 500px;
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

/* .btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
} */

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}
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

/* Ensure proper positioning for DataTables */
.dataTables_wrapper .dataTables_processing {
    z-index: 9998;
}

/* Fix for menu positioning in table cells */
#staffTable td {
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
    background-color: #FFEBEB !important;
    border: 1px solid #ecd8d8;
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

<script>
// Simple modal functions using vanilla JavaScript
function showModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }
}

function hideModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
}

// Close modal when clicking on backdrop or close button
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking on backdrop
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal(modal.id);
            }
        });
    });

    // Close modal when clicking on close button
    var closeButtons = document.querySelectorAll('.modal .close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var modal = button.closest('.modal');
            hideModal(modal.id);
        });
    });

    // Close modal when clicking on Cancel button
    var cancelButtons = document.querySelectorAll('.modal .btn-secondary');
    cancelButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var modal = button.closest('.modal');
            hideModal(modal.id);
        });
    });

    // Form submissions are now handled in staff_js.blade.php
});

// These functions are now handled in staff_js.blade.php
</script>
@endsection

@include('app._partials.js')
@include('app.staff.staff_js') 