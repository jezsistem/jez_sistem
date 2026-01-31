<style>
    /* Simple DataTables styling */
    .datatable-wrapper {
        padding: 1rem 0;
    }
    .datatable-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .datatable-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .datatable-search {
        position: relative;
    }
    .datatable-search input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        width: 250px;
    }
    .datatable-search input:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: #3b82f6;
    }
    .datatable-selector {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    .datatable-table {
        width: 100%;
        border-collapse: collapse;
    }
    .datatable-table thead {
        background-color: #f9fafb;
    }
    .datatable-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }
    .datatable-table td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #111827;
        border-bottom: 1px solid #e5e7eb;
    }
    .datatable-table tbody tr:hover {
        background-color: #f9fafb;
    }
    .datatable-sorter {
        cursor: pointer;
        user-select: none;
    }
    .datatable-sorter:hover {
        color: #3b82f6;
    }
    .datatable-pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .datatable-pagination a {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        text-decoration: none;
        color: #374151;
        font-size: 0.875rem;
    }
    .datatable-pagination a:hover {
        background-color: #f3f4f6;
    }
    .datatable-pagination a.active {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    .datatable-info {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Button styling from server response */
    .btn {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
    }
    .btn-xs {
        padding: 0.125rem 0.375rem;
        font-size: 0.75rem;
    }
    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }
    .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }
    .btn-info {
        background-color: #06b6d4;
        border-color: #06b6d4;
        color: #fff;
    }
    .btn-secondary {
        background-color: #6b7280;
        border-color: #6b7280;
        color: #fff;
    }

    /* Clickable row */
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f3f4f6;
    }
</style>
