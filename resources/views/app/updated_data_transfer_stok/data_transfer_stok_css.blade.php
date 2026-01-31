<style>
    /* Select2 styling */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
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

    /* DataTables styling */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.75rem;
        margin: 0 0.125rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb;
        color: white !important;
        border-color: #2563eb;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.875rem;
        color: #4b5563;
        padding: 0.5rem 0;
    }

    /* Table cell styling */
    #StockTransferDatatb td, #Historytb td, #StockTransferDataAccepttb td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    /* Clickable row styling */
    #StockTransferDatatb tbody tr {
        cursor: pointer;
    }
    #StockTransferDatatb tbody tr:hover {
        background-color: #f3f4f6;
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
    .btn-light {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }

    /* Form control styling */
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1f2937;
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }

    /* Accept qty input */
    input.accept_qty {
        width: 70px;
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-align: center;
    }

    /* Table danger row */
    .table-danger {
        background-color: #fee2e2 !important;
    }

    /* Date Range Picker styling */
    .daterangepicker {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
    }
    .daterangepicker .ranges li.active {
        background-color: #2563eb;
    }
    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #2563eb;
    }
    .daterangepicker .drp-buttons .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    /* Toastr styling */
    .toast-success {
        background-color: #10b981 !important;
    }
    .toast-error {
        background-color: #ef4444 !important;
    }
    .toast-warning {
        background-color: #f59e0b !important;
    }
    .toast-info {
        background-color: #3b82f6 !important;
    }

    /* QR Scanner styling */
    #reader_tf_receive {
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    #reader_tf_receive video {
        border-radius: 0.375rem;
    }
</style>
