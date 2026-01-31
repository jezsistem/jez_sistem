<style>
    /* Simple DataTables styling */
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
    /* Container for horizontal scroll */
    .datatable-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .datatable-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        table-layout: auto;
    }
    
    /* History table specific - wider */
    #Historytb {
        min-width: 1000px;
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
    
    /* Button styling in table */
    #PermissionDatatb button,
    #Invoicetb button,
    #InvoiceDetailtb button,
    #Trackingtb button,
    #Historytb button,
    #PermissionDatatb .btn,
    #Invoicetb .btn,
    #InvoiceDetailtb .btn,
    #Trackingtb .btn,
    #Historytb .btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    /* Input and select styling in table */
    .datatable-table input,
    .datatable-table select {
        padding: 0.25rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        width: 100%;
    }
    .datatable-table input:focus,
    .datatable-table select:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: #3b82f6;
    }
    
    /* Editor panel styling */
    .editor_panel {
        transition: opacity 0.3s;
    }
    
    .editor_panel.hidden {
        display: none;
    }
    
    /* Horizontal scroll for wide tables */
    .overflow-x-auto {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

