<style>
    /* Simple DataTables styling */
    .datatable-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .datatable-wrapper {
        padding: 1rem 0;
        overflow-x: auto;
        width: 100%;
        max-width: 100%;
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
        min-width: 800px;
        border-collapse: collapse;
        table-layout: auto;
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
        word-wrap: break-word;
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
    
    /* Column widths */
    #Targettb th:nth-child(1), #Targettb td:nth-child(1) { width: 5%; }
    #Targettb th:nth-child(2), #Targettb td:nth-child(2) { width: 15%; }
    #Targettb th:nth-child(3), #Targettb td:nth-child(3) { width: 20%; }
    #Targettb th:nth-child(4), #Targettb td:nth-child(4) { width: 20%; }
    #Targettb th:nth-child(5), #Targettb td:nth-child(5) { width: 10%; }
    #Targettb th:nth-child(6), #Targettb td:nth-child(6) { width: 30%; }
    
    #TargetDetailtb th:nth-child(1), #TargetDetailtb td:nth-child(1) { width: 5%; }
    #TargetDetailtb th:nth-child(2), #TargetDetailtb td:nth-child(2) { width: 15%; }
    #TargetDetailtb th:nth-child(3), #TargetDetailtb td:nth-child(3) { width: 15%; }
    #TargetDetailtb th:nth-child(4), #TargetDetailtb td:nth-child(4) { width: 15%; }
    #TargetDetailtb th:nth-child(5), #TargetDetailtb td:nth-child(5) { width: 15%; }
    #TargetDetailtb th:nth-child(6), #TargetDetailtb td:nth-child(6) { width: 10%; }
    #TargetDetailtb th:nth-child(7), #TargetDetailtb td:nth-child(7) { width: 15%; }
    #TargetDetailtb th:nth-child(8), #TargetDetailtb td:nth-child(8) { width: 10%; }
    
    /* Button styles in table */
    #Targettb .btn,
    #TargetDetailtb .btn {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    
    #Targettb .btn-sm,
    #TargetDetailtb .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
    }
    
    #Targettb .btn-primary,
    #TargetDetailtb .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    #Targettb .btn-primary:hover,
    #TargetDetailtb .btn-primary:hover {
        color: #fff;
        background-color: #0056b3;
        border-color: #004085;
    }
    
    #Targettb .btn-success,
    #TargetDetailtb .btn-success {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }
    
    #Targettb .btn-success:hover,
    #TargetDetailtb .btn-success:hover {
        color: #fff;
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    #Targettb .btn-danger,
    #TargetDetailtb .btn-danger {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    #Targettb .btn-danger:hover,
    #TargetDetailtb .btn-danger:hover {
        color: #fff;
        background-color: #c82333;
        border-color: #bd2130;
    }
    
    /* Ensure buttons in table cells are clickable */
    #Targettb td .btn,
    #TargetDetailtb td .btn {
        pointer-events: auto;
        position: relative;
        z-index: 1;
    }
</style>
