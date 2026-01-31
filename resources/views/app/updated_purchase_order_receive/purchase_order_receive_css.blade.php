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
    min-width: 1200px;
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
    align-items: center;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}
.datatable-pagination a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    color: #374151;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}
.datatable-pagination a:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}
.datatable-pagination a.active {
    background-color: #3b82f6;
    color: #ffffff;
    border-color: #3b82f6;
}
.datatable-pagination a[aria-disabled="true"] {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
.datatable-info {
    font-size: 0.875rem;
    color: #6b7280;
}

/* Custom pagination */
.custom-pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}
.custom-pagination button {
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    color: #374151;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
}
.custom-pagination button:hover:not(:disabled) {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}
.custom-pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.custom-pagination button.bg-blue-600 {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Button styles */
.btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-sm {
    padding: 0.125rem 0.5rem;
    font-size: 0.75rem;
}
.btn-primary {
    background-color: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background-color: #2563eb;
}
.btn-success {
    background-color: #10b981;
    color: white;
}
.btn-success:hover {
    background-color: #059669;
}
.btn-danger {
    background-color: #ef4444;
    color: white;
}
.btn-danger:hover {
    background-color: #dc2626;
}
.btn-warning {
    background-color: #f59e0b;
    color: white;
}
.btn-warning:hover {
    background-color: #d97706;
}

/* Modal table styling */
#PenerimaanModal table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

#PenerimaanModal table thead {
    background-color: #f9fafb;
}

#PenerimaanModal table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

#PenerimaanModal table td {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: #111827;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: top;
}

#PenerimaanModal table tbody tr:hover {
    background-color: #f9fafb;
}

#PenerimaanModal .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

#penerimaan_detail_content {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f9fafb;
}
</style>
