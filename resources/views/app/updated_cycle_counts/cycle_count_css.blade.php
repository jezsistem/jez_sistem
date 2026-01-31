<style>
    /* Fix for scrolling issue - ensure main content area can scroll */
    body > div.flex > main {
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }
    
    /* Ensure content container can expand beyond viewport */
    body > div.flex > main > div.space-y-6 {
        min-height: auto !important;
    }
    
    /* CRITICAL: Prevent any hidden element from blocking clicks */
    .hidden {
        display: none !important;
    }
    
    #loader.hidden,
    #loader_download.hidden,
    #ImportModal.hidden {
        display: none !important;
        pointer-events: none !important;
        z-index: -9999 !important;
    }

    /* Simple DataTables styling */
    .datatable-wrapper {
        padding: 1rem 0;
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
        border-collapse: collapse;
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
    .datatable-pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .datatable-pagination a {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        text-decoration: none;
        color: #374151;
        font-size: 0.875rem;
    }
    .datatable-pagination a:hover {
        background-color: #f3f4f6;
    }
    .datatable-pagination a.active {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    .datatable-info {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Select2 styling */
    .select2-container { 
        width: 100% !important; 
        position: relative;
        z-index: 1;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
        padding: 0.25rem 0;
        cursor: pointer;
        pointer-events: auto;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.75rem;
        cursor: pointer;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        cursor: pointer;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        z-index: 9999 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
    }
    
    /* Ensure select element is clickable */
    #bin_filter {
        cursor: pointer !important;
        pointer-events: auto !important;
        position: relative;
        z-index: 1;
    }
    
    /* Ensure bin_filter_parent doesn't block clicks */
    #bin_filter_parent {
        position: relative;
        z-index: 1;
        pointer-events: auto !important;
    }
    
    /* Ensure Select2 container is clickable */
    .select2-container--default {
        pointer-events: auto !important;
        cursor: pointer !important;
        z-index: 1;
    }
    
    /* Ensure Select2 selection is clickable */
    .select2-selection {
        pointer-events: auto !important;
        cursor: pointer !important;
    }

    /* Loader styling - ALWAYS hidden by default */
    #loader, #loader_download {
        display: none !important;
        visibility: hidden !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: -1 !important;
        text-align: center;
        pointer-events: none !important;
        opacity: 0 !important;
    }
    
    /* Loaders are ONLY visible when explicitly shown with class show-loader */
    #loader.show-loader, #loader_download.show-loader {
        display: flex !important;
        visibility: visible !important;
        pointer-events: auto !important;
        opacity: 1 !important;
        z-index: 9999 !important;
    }
    
    /* Ensure loaders are really hidden when not shown */
    #loader.hidden, #loader_download.hidden,
    #loader:not(.show-loader), #loader_download:not(.show-loader) {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        opacity: 0 !important;
        z-index: -1 !important;
    }
    
    /* Ensure modals don't block clicks when hidden */
    #ImportModal.hidden {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        opacity: 0 !important;
        z-index: -1 !important;
    }
    
    /* Ensure Select2 dropdown doesn't block clicks */
    .select2-container {
        z-index: 9999;
    }
    
    .select2-dropdown {
        z-index: 9999 !important;
    }
    
    /* Prevent body overflow-hidden from blocking clicks */
    body:not(.modal-open) {
        overflow: auto !important;
    }
    
    /* Ensure any hidden fixed elements don't block clicks */
    .fixed.hidden,
    .fixed[style*="display: none"],
    .fixed[style*="visibility: hidden"],
    [style*="position: fixed"][style*="display: none"],
    [style*="position: fixed"].hidden,
    #loader:not(.show-loader),
    #loader_download:not(.show-loader),
    #ImportModal.hidden {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        opacity: 0 !important;
        z-index: -1 !important;s
        position: fixed !important;
    }
    
    /* Select2 dropdown styling */
    .select2-container--open .select2-dropdown {
        z-index: 9999 !important;
    }
    
    /* Remove any Select2 backdrop */
    .select2-backdrop {
        display: none !important;
    }
    
    /* Ensure modal is properly hidden */
    #ImportModal:not(.hidden) {
        z-index: 50 !important;
    }
    #loader img, #loader_download img {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 10px;
        border-radius: 10px;
        width: 20%;
    }
    .loading-text {
        position: absolute;
        top: 60%;
        left: 50%;
        margin-top: 20px;
        transform: translateX(-50%);
        font-size: 1.5em;
        font-weight: bold;
        color: #333;
        font-family: Arial, sans-serif;
    }
    .dots::after {
        content: "";
        animation: dots 1s steps(4, end) infinite;
    }
    @keyframes dots {
        0%, 100% { content: ""; }
        25% { content: "."; }
        50% { content: ".."; }
        75% { content: "..."; }
    }
</style>
