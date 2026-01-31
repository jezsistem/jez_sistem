<style>
    /* Select2 styling */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
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
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 0.25rem 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3b82f6;
        border: none;
        color: white;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin: 0.125rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 0.25rem;
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

    /* Stock Data Table specific */
    #StockDatatb th {
        width: 80%;
        white-space: nowrap;
        padding: 10px;
    }
    #StockDatatb th.hidden {
        width: 20%;
    }
    #StockDatatb td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        vertical-align: top;
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
    .custom-defect {
        background-color: #784800;
        color: #fff;
    }

    /* QR Scanner styling */
    #reader_main, #reader_change_display {
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    #reader_main video, #reader_change_display video {
        border-radius: 0.375rem;
    }

    /* Responsive table */
    @media only screen and (max-width: 1080px) {
        #StockDatatb tbody tr {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        #StockDatatb tbody td {
            display: block;
            text-align: left;
            border-bottom: 1px solid #ddd;
            padding: 10px 5px;
            position: relative;
        }
        #StockDatatb tbody td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
        }
    }

    /* Badge styling */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }
    .badge-primary { background-color: #3b82f6; color: white; }
    .badge-success { background-color: #10b981; color: white; }
    .badge-warning { background-color: #f59e0b; color: white; }
    .badge-danger { background-color: #ef4444; color: white; }
    .badge-info { background-color: #06b6d4; color: white; }
</style>
