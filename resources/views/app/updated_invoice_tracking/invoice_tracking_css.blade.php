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
    min-width: 1400px;
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
#InvoiceTrackingtb th:nth-child(1), #InvoiceTrackingtb td:nth-child(1) { width: 5%; }
#InvoiceTrackingtb th:nth-child(2), #InvoiceTrackingtb td:nth-child(2) { width: 10%; }
#InvoiceTrackingtb th:nth-child(3), #InvoiceTrackingtb td:nth-child(3) { width: 10%; }
#InvoiceTrackingtb th:nth-child(4), #InvoiceTrackingtb td:nth-child(4) { width: 12%; }
#InvoiceTrackingtb th:nth-child(5), #InvoiceTrackingtb td:nth-child(5) { width: 10%; }
#InvoiceTrackingtb th:nth-child(6), #InvoiceTrackingtb td:nth-child(6) { width: 10%; }
#InvoiceTrackingtb th:nth-child(7), #InvoiceTrackingtb td:nth-child(7) { width: 8%; }
#InvoiceTrackingtb th:nth-child(8), #InvoiceTrackingtb td:nth-child(8) { width: 10%; }
#InvoiceTrackingtb th:nth-child(9), #InvoiceTrackingtb td:nth-child(9) { width: 10%; }
#InvoiceTrackingtb th:nth-child(10), #InvoiceTrackingtb td:nth-child(10) { width: 10%; }
#InvoiceTrackingtb th:nth-child(11), #InvoiceTrackingtb td:nth-child(11) { width: 5%; }

/* Button styles in table */
#InvoiceTrackingtb .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    display: inline-block;
    white-space: nowrap;
}

#InvoiceTrackingtb .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#InvoiceTrackingtb .btn-primary {
    background-color: #3b82f6;
    color: white;
}
#InvoiceTrackingtb .btn-primary:hover {
    background-color: #2563eb;
}

#InvoiceTrackingtb .btn-success {
    background-color: #22c55e;
    color: white;
}
#InvoiceTrackingtb .btn-success:hover {
    background-color: #16a34a;
}

#InvoiceTrackingtb .btn-danger {
    background-color: #ef4444;
    color: white;
}
#InvoiceTrackingtb .btn-danger:hover {
    background-color: #dc2626;
}

#InvoiceTrackingtb .btn-info {
    background-color: #3abff8;
    color: white;
}
#InvoiceTrackingtb .btn-info:hover {
    background-color: #0ea5e9;
}

#InvoiceTrackingtb .btn-warning {
    background-color: #fbbf24;
    color: white;
}
#InvoiceTrackingtb .btn-warning:hover {
    background-color: #f59e0b;
}
</style>
