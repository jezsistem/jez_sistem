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
    #TransferBintb td, #InTransferBintb td, #TransferHistorytb td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    /* Button styling from server response */
    #TransferBintb .btn,
    #InTransferBintb .btn,
    #TransferHistorytb .btn {
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
    #TransferBintb .btn-sm,
    #InTransferBintb .btn-sm,
    #TransferHistorytb .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
    }
    #TransferBintb .btn-primary,
    #InTransferBintb .btn-primary,
    #TransferHistorytb .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
    #TransferBintb .btn-primary:hover,
    #InTransferBintb .btn-primary:hover,
    #TransferHistorytb .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    #TransferBintb .btn-success,
    #InTransferBintb .btn-success,
    #TransferHistorytb .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    #TransferBintb .btn-warning,
    #InTransferBintb .btn-warning,
    #TransferHistorytb .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }
    #TransferBintb .btn-danger,
    #InTransferBintb .btn-danger,
    #TransferHistorytb .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }
    #TransferBintb .btn-info,
    #InTransferBintb .btn-info,
    #TransferHistorytb .btn-info {
        background-color: #06b6d4;
        border-color: #06b6d4;
        color: #fff;
    }
    #TransferBintb .btn-light,
    #InTransferBintb .btn-light,
    #TransferHistorytb .btn-light {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }

    /* Form control styling */
    #TransferBintb .form-control,
    #InTransferBintb .form-control {
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
    #TransferBintb .form-control:focus,
    #InTransferBintb .form-control:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }

    /* Transfer qty input */
    input.transfer_qty {
        width: 70px;
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-align: center;
    }

    /* Utility classes */
    .d-flex { display: flex !important; }
    .d-none { display: none !important; }
    .align-items-center { align-items: center !important; }
    .mb-2 { margin-bottom: 0.5rem !important; }
    .pb-2 { padding-bottom: 0.5rem !important; }
</style>
