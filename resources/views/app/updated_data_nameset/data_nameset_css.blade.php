<style>
    /* Container for horizontal scroll */
    .datatable-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Simple DataTables styling */
    .datatable-wrapper {
        padding: 1rem 0;
        overflow-x: auto;
        width: 100%;
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
        max-width: 300px;
    }
    
    /* Specific column widths */
    .datatable-table td:nth-child(1),
    .datatable-table th:nth-child(1) {
        width: 5%;
        min-width: 50px;
    }
    
    .datatable-table td:nth-child(2),
    .datatable-table th:nth-child(2) {
        width: 12%;
        min-width: 120px;
    }
    
    .datatable-table td:nth-child(3),
    .datatable-table th:nth-child(3) {
        width: 10%;
        min-width: 100px;
    }
    
    .datatable-table td:nth-child(4),
    .datatable-table th:nth-child(4) {
        width: 30%;
        min-width: 250px;
        max-width: 400px;
        word-break: break-word;
    }
    
    .datatable-table td:nth-child(5),
    .datatable-table th:nth-child(5) {
        width: 15%;
        min-width: 150px;
    }
    
    .datatable-table td:nth-child(6),
    .datatable-table th:nth-child(6) {
        width: 18%;
        min-width: 180px;
        max-width: 250px;
        word-break: break-word;
    }
    
    .datatable-table td:nth-child(7),
    .datatable-table th:nth-child(7) {
        width: 10%;
        min-width: 100px;
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
    
    /* Button styling in table */
    #NamesetDatatb button,
    #NamesetDatatb .btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
</style>
