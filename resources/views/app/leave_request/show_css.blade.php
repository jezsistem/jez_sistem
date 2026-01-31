<style>
    /* Custom CSS for Metronic dropdown menu */
    /* Modal styles using vanilla CSS (like staff) */
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

    /* Ensure proper positioning for DataTables */
    .dataTables_wrapper .dataTables_processing {
        z-index: 9998;
    }

    /* Fix for menu positioning in table cells */
    #leaveRequestTable td {
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

    .menu-item .menu-link.text-success {
        color: #1bc5bd !important;
    }

    .menu-item .menu-link.text-success:hover {
        background-color: #e1f0ff !important;
        color: #1bc5bd !important;
    }

    .bg-all {
        background-color: #FFEBEB;
    }

    .bg-other {
        background-color: #F1F1F4;
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