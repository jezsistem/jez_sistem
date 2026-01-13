<style>
    /* Custom colors matching pos_v2 */
    .bg-cyan {
        background-color: #06b6d4;
    }

    .bg-calculator {
        background-color: #f59e0b;
    }

    .bg-calculator-dark {
        background-color: #d97706;
    }

    .bg-body {
        background-color: #f8f8f8;
    }
    .min-w-20vw {
        min-width: 20vw;
    }

    /* Loading overlay */
    #loader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        text-align: center;
    }

    #loader .loading-content {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    #loader .loading-text {
        font-size: 1.5em;
        font-weight: bold;
        color: #333;
        margin-top: 20px;
    }

    .loading-text .dots::after {
        content: "";
        animation: dots 1s steps(4, end) infinite;
    }

    @keyframes dots {
        0%, 100% { content: ""; }
        25% { content: "."; }
        50% { content: ".."; }
        75% { content: "..."; }
    }

    /* Hide number input spinner */
    input[id^="nameset_price"]::-webkit-inner-spin-button,
    input[id^="nameset_price"]::-webkit-outer-spin-button,
    input[id^="marketplace_price"]::-webkit-inner-spin-button,
    input[id^="marketplace_price"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[id^="nameset_price"],
    input[id^="marketplace_price"] {
        -moz-appearance: textfield;
    }

    /* Ensure modal close button is visible */
    #close-modal-customer,
    #cancel-modal-customer {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    #close-modal-customer svg,
    #cancel-modal-customer {
        display: block !important;
        visibility: visible !important;
    }

    /* DataTable styling for Product Barcode Modal */
    #Ptb_wrapper {
        width: 100%;
    }

    #Ptb_wrapper .dataTables_filter input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        margin-left: 0.5rem;
    }

    #Ptb_wrapper .dataTables_length select {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        margin: 0 0.5rem;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: white;
        color: #374151;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button.current {
        background: #ef4444;
        color: white !important;
        border-color: #ef4444;
    }

    #Ptb_wrapper .dataTables_info {
        padding: 0.5rem 0;
        color: #6b7280;
    }

    #Ptb tbody tr {
        transition: background-color 0.2s;
        border-color: #d1d5db !important;
    }

    #Ptb tbody tr:hover {
        background-color: #f9fafb;
    }

    #Ptb tbody td {
        padding: 0.5rem 1rem;
        vertical-align: middle;
    }

    /* Ensure modal backdrop is visible */
    [data-modal-backdrop] {
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Flowbite table styling */
    #Ptb_wrapper .dataTables_wrapper {
        padding: 0;
    }

    /* Smaller font for table and pagination */
    #Ptb {
        font-size: 0.875rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    #Ptb thead th {
        font-size: 0.875rem;
        padding: 0.875rem 0.75rem;
        font-weight: 500 !important;
        border-bottom: 1px solid #d1d5db !important;
    }

    #Ptb tbody td {
        font-size: 0.875rem;
        padding: 0.65rem 0.75rem;
    }

    #Ptb_wrapper .dataTables_length,
    #Ptb_wrapper .dataTables_info,
    #Ptb_wrapper .dataTables_paginate {
        padding: 0.5rem 0;
        font-size: 0.875rem;
    }

    #Ptb_wrapper .dataTables_length label,
    #Ptb_wrapper .dataTables_info {
        color: #6b7280;
        font-size: 0.875rem;
    }

    #Ptb_wrapper .dataTables_length select {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        width:  4.25rem;
    }

    #Ptb_wrapper .dataTables_paginate {
        font-size: 0.875rem;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.6rem;
        margin: 0 0.125rem;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        background: white;
        color: #374151;
        cursor: pointer;
        font-size: 0.875rem;
        line-height: 1.25;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button.current {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    #Ptb_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* DataTable processing indicator */
    #Ptb_processing {
        position: absolute;
        top: 50%;
        left: 60%;
        transform: translate(-50%, -50%);
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Search loading indicator */
    #p_search_loading {
        pointer-events: none;
    }
</style>