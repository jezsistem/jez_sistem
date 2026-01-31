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
    min-width: 600px;
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

/* Column widths for tables */
#Onlinetb th:nth-child(1), #Onlinetb td:nth-child(1) { width: 5%; }
#Onlinetb th:nth-child(2), #Onlinetb td:nth-child(2) { width: 30%; }
#Onlinetb th:nth-child(3), #Onlinetb td:nth-child(3) { width: 30%; }
#Onlinetb th:nth-child(4), #Onlinetb td:nth-child(4) { width: 35%; }

#Offlinetb th:nth-child(1), #Offlinetb td:nth-child(1) { width: 5%; }
#Offlinetb th:nth-child(2), #Offlinetb td:nth-child(2) { width: 30%; }
#Offlinetb th:nth-child(3), #Offlinetb td:nth-child(3) { width: 30%; }
#Offlinetb th:nth-child(4), #Offlinetb td:nth-child(4) { width: 35%; }

#SalesDetailtb th:nth-child(1), #SalesDetailtb td:nth-child(1) { width: 5%; }
#SalesDetailtb th:nth-child(2), #SalesDetailtb td:nth-child(2) { width: 25%; }
#SalesDetailtb th:nth-child(3), #SalesDetailtb td:nth-child(3) { width: 20%; }
#SalesDetailtb th:nth-child(4), #SalesDetailtb td:nth-child(4) { width: 20%; }
#SalesDetailtb th:nth-child(5), #SalesDetailtb td:nth-child(5) { width: 30%; }

#SalesItemDetailtb th:nth-child(1), #SalesItemDetailtb td:nth-child(1) { width: 5%; }
#SalesItemDetailtb th:nth-child(2), #SalesItemDetailtb td:nth-child(2) { width: 40%; }
#SalesItemDetailtb th:nth-child(3), #SalesItemDetailtb td:nth-child(3) { width: 15%; }
#SalesItemDetailtb th:nth-child(4), #SalesItemDetailtb td:nth-child(4) { width: 20%; }
#SalesItemDetailtb th:nth-child(5), #SalesItemDetailtb td:nth-child(5) { width: 20%; }

#ProductRatingtb th:nth-child(1), #ProductRatingtb td:nth-child(1) { width: 5%; }
#ProductRatingtb th:nth-child(2), #ProductRatingtb td:nth-child(2) { width: 15%; }
#ProductRatingtb th:nth-child(3), #ProductRatingtb td:nth-child(3) { width: 15%; }
#ProductRatingtb th:nth-child(4), #ProductRatingtb td:nth-child(4) { width: 25%; }
#ProductRatingtb th:nth-child(5), #ProductRatingtb td:nth-child(5) { width: 10%; }
#ProductRatingtb th:nth-child(6), #ProductRatingtb td:nth-child(6) { width: 15%; }
#ProductRatingtb th:nth-child(7), #ProductRatingtb td:nth-child(7) { width: 15%; }

/* Button styles in table */
#Onlinetb .btn,
#Offlinetb .btn,
#SalesDetailtb .btn,
#SalesItemDetailtb .btn {
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

#Onlinetb .btn-sm,
#Offlinetb .btn-sm,
#SalesDetailtb .btn-sm,
#SalesItemDetailtb .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#Onlinetb .btn-primary,
#Offlinetb .btn-primary,
#SalesDetailtb .btn-primary,
#SalesItemDetailtb .btn-primary {
    background-color: #3b82f6;
    color: white;
}
#Onlinetb .btn-primary:hover,
#Offlinetb .btn-primary:hover,
#SalesDetailtb .btn-primary:hover,
#SalesItemDetailtb .btn-primary:hover {
    background-color: #2563eb;
}

#Onlinetb .btn-success,
#Offlinetb .btn-success,
#SalesDetailtb .btn-success,
#SalesItemDetailtb .btn-success {
    background-color: #22c55e;
    color: white;
}
#Onlinetb .btn-success:hover,
#Offlinetb .btn-success:hover,
#SalesDetailtb .btn-success:hover,
#SalesItemDetailtb .btn-success:hover {
    background-color: #16a34a;
}

/* Date range picker button */
.btn-date-info {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}
</style>
