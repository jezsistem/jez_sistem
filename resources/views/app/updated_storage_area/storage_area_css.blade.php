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
        cursor: pointer;
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

    /* Select2 styling */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
        padding: 0.25rem 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
    }

    /* Bin list styling */
    .bin-button {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        margin: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .bin-button-remove {
        background-color: #6b7280;
        color: white;
    }
    .bin-button-remove:hover {
        background-color: #4b5563;
    }
    .bin-group-header {
        cursor: pointer;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .bin-group-content {
        border: 1px solid #ddd;
        border-top: none;
        padding: 10px;
        border-radius: 0 0 5px 5px;
    }
</style>
