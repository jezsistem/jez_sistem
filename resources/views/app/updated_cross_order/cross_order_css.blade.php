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
}
.datatable-sorter {
    cursor: pointer;
    user-select: none;
}
.datatable-sorter:hover {
    color: #3b82f6;
}

/* Column widths */
#CrossOrdertb th:nth-child(1), #CrossOrdertb td:nth-child(1) { width: 5%; }
#CrossOrdertb th:nth-child(2), #CrossOrdertb td:nth-child(2) { width: 10%; }
#CrossOrdertb th:nth-child(3), #CrossOrdertb td:nth-child(3) { width: 10%; }
#CrossOrdertb th:nth-child(4), #CrossOrdertb td:nth-child(4) { width: 12%; }
#CrossOrdertb th:nth-child(5), #CrossOrdertb td:nth-child(5) { width: 12%; }
#CrossOrdertb th:nth-child(6), #CrossOrdertb td:nth-child(6) { width: 10%; }
#CrossOrdertb th:nth-child(7), #CrossOrdertb td:nth-child(7) { width: 10%; }
#CrossOrdertb th:nth-child(8), #CrossOrdertb td:nth-child(8) { width: 8%; }
#CrossOrdertb th:nth-child(9), #CrossOrdertb td:nth-child(9) { width: 10%; }
#CrossOrdertb th:nth-child(10), #CrossOrdertb td:nth-child(10) { width: 8%; }
#CrossOrdertb th:nth-child(11), #CrossOrdertb td:nth-child(11) { width: 5%; }

#Confirmationtb th:nth-child(1), #Confirmationtb td:nth-child(1) { width: 5%; }
#Confirmationtb th:nth-child(2), #Confirmationtb td:nth-child(2) { width: 30%; }
#Confirmationtb th:nth-child(3), #Confirmationtb td:nth-child(3) { width: 10%; }
#Confirmationtb th:nth-child(4), #Confirmationtb td:nth-child(4) { width: 15%; }
#Confirmationtb th:nth-child(5), #Confirmationtb td:nth-child(5) { width: 10%; }
#Confirmationtb th:nth-child(6), #Confirmationtb td:nth-child(6) { width: 30%; }

#Detailtb th:nth-child(1), #Detailtb td:nth-child(1) { width: 5%; }
#Detailtb th:nth-child(2), #Detailtb td:nth-child(2) { width: 30%; }
#Detailtb th:nth-child(3), #Detailtb td:nth-child(3) { width: 10%; }
#Detailtb th:nth-child(4), #Detailtb td:nth-child(4) { width: 15%; }
#Detailtb th:nth-child(5), #Detailtb td:nth-child(5) { width: 10%; }
#Detailtb th:nth-child(6), #Detailtb td:nth-child(6) { width: 30%; }

#Historytb th:nth-child(1), #Historytb td:nth-child(1) { width: 5%; }
#Historytb th:nth-child(2), #Historytb td:nth-child(2) { width: 25%; }
#Historytb th:nth-child(3), #Historytb td:nth-child(3) { width: 10%; }
#Historytb th:nth-child(4), #Historytb td:nth-child(4) { width: 15%; }
#Historytb th:nth-child(5), #Historytb td:nth-child(5) { width: 15%; }
#Historytb th:nth-child(6), #Historytb td:nth-child(6) { width: 15%; }
#Historytb th:nth-child(7), #Historytb td:nth-child(7) { width: 15%; }

/* Button styles in table */
#CrossOrdertb .btn,
#Confirmationtb .btn,
#Detailtb .btn,
#Historytb .btn {
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

#CrossOrdertb .btn-sm,
#Confirmationtb .btn-sm,
#Detailtb .btn-sm,
#Historytb .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#CrossOrdertb .btn-primary,
#Confirmationtb .btn-primary,
#Detailtb .btn-primary,
#Historytb .btn-primary {
    background-color: #3b82f6;
    color: white;
}
#CrossOrdertb .btn-primary:hover,
#Confirmationtb .btn-primary:hover,
#Detailtb .btn-primary:hover,
#Historytb .btn-primary:hover {
    background-color: #2563eb;
}

#CrossOrdertb .btn-success,
#Confirmationtb .btn-success,
#Detailtb .btn-success,
#Historytb .btn-success {
    background-color: #22c55e;
    color: white;
}
#CrossOrdertb .btn-success:hover,
#Confirmationtb .btn-success:hover,
#Detailtb .btn-success:hover,
#Historytb .btn-success:hover {
    background-color: #16a34a;
}

#CrossOrdertb .btn-danger,
#Confirmationtb .btn-danger,
#Detailtb .btn-danger,
#Historytb .btn-danger {
    background-color: #ef4444;
    color: white;
}
#CrossOrdertb .btn-danger:hover,
#Confirmationtb .btn-danger:hover,
#Detailtb .btn-danger:hover,
#Historytb .btn-danger:hover {
    background-color: #dc2626;
}

#CrossOrdertb .btn-info,
#Confirmationtb .btn-info,
#Detailtb .btn-info,
#Historytb .btn-info {
    background-color: #3abff8;
    color: white;
}
#CrossOrdertb .btn-info:hover,
#Confirmationtb .btn-info:hover,
#Detailtb .btn-info:hover,
#Historytb .btn-info:hover {
    background-color: #0ea5e9;
}

#CrossOrdertb .btn-warning,
#Confirmationtb .btn-warning,
#Detailtb .btn-warning,
#Historytb .btn-warning {
    background-color: #fbbf24;
    color: white;
}
#CrossOrdertb .btn-warning:hover,
#Confirmationtb .btn-warning:hover,
#Detailtb .btn-warning:hover,
#Historytb .btn-warning:hover {
    background-color: #f59e0b;
}
</style>
