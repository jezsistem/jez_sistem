<script>
// AJAX Search functionality for announcements
class AnnouncementSearch {
    constructor(options = {}) {
        this.options = {
            searchInput: '#searchInput',
            searchBtn: '#searchBtn',
            resetBtn: '#resetBtn',
            categoryFilter: '#categoryFilter',
            statusFilter: '#statusFilter',
            resultsContainer: options.resultsContainer || null,
            searchUrl: options.searchUrl || null,
            debounceDelay: 500,
            ...options
        };
        
        this.searchTimeout = null;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadCurrentFilters();
    }
    
    bindEvents() {
        const searchInput = $(this.options.searchInput);
        const searchBtn = $(this.options.searchBtn);
        const resetBtn = $(this.options.resetBtn);
        const categoryFilter = $(this.options.categoryFilter);
        const statusFilter = $(this.options.statusFilter);
        const clearSearchBtn = $('#clearSearchBtn');
        
        // Auto-search on typing with debounce
        searchInput.on('input', (e) => {
            this.debouncedSearch();
            this.toggleClearButton();
        });
        
        // Auto-search on filter change
        if (categoryFilter.length) {
            categoryFilter.on('change', () => {
                this.performSearch();
            });
        }
        
        if (statusFilter.length) {
            statusFilter.on('change', () => {
                this.performSearch();
            });
        }
        
        // Manual search button
        searchBtn.on('click', () => {
            this.performSearch();
        });
        
        // Reset button
        if (resetBtn.length) {
            resetBtn.on('click', () => {
                this.resetFilters();
            });
        }
        
        // Clear search button
        if (clearSearchBtn.length) {
            clearSearchBtn.on('click', () => {
                this.clearSearch();
            });
        }
        
        // Enter key press
        searchInput.on('keypress', (e) => {
            if (e.which === 13) {
                this.performSearch();
            }
        });
    }
    
    debouncedSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch();
        }, this.options.debounceDelay);
    }
    
    performSearch() {
        const searchTerm = $(this.options.searchInput).val();
        const categoryId = $(this.options.categoryFilter).val();
        const status = $(this.options.statusFilter).val();
        
        // Show loading state
        this.showLoading(true);
        
        // Build search parameters
        const params = new URLSearchParams();
        if (searchTerm.trim()) params.append('search', searchTerm.trim());
        if (categoryId) params.append('category_id', categoryId);
        if (status) params.append('status', status);
        
        // If we have a results container and search URL, use AJAX
        if (this.options.resultsContainer && this.options.searchUrl) {
            this.performAjaxSearch(params);
        } else {
            // Otherwise, redirect to search results
            this.redirectToSearch(params);
        }
    }
    
    performAjaxSearch(params) {
        const url = this.options.searchUrl + '?' + params.toString();
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            success: (response) => {
                $(this.options.resultsContainer).html(response);
                this.updateURL(params);
                this.showLoading(false);
            },
            error: (xhr, status, error) => {
                console.error('Search failed:', error);
                this.showLoading(false);
                // Fallback to redirect
                this.redirectToSearch(params);
            }
        });
    }
    
    redirectToSearch(params) {
        let url = window.location.pathname;
        if (params.toString()) {
            url += '?' + params.toString();
        }
        window.location.href = url;
    }
    
    updateURL(params) {
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    }
    
    resetFilters() {
        $(this.options.searchInput).val('');
        $(this.options.categoryFilter).val('');
        $(this.options.statusFilter).val('');
        this.performSearch();
    }
    
    showLoading(show) {
        const searchBtn = $(this.options.searchBtn);
        if (show) {
            searchBtn.html('<i class="ki-outline ki-loading"></i> Searching...');
            searchBtn.prop('disabled', true);
        } else {
            searchBtn.html('<i class="ki-outline ki-magnifier"></i> Search');
            searchBtn.prop('disabled', false);
        }
    }
    
    loadCurrentFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentSearch = urlParams.get('search');
        const currentCategory = urlParams.get('category_id');
        const currentStatus = urlParams.get('status');
        
        if (currentSearch) $(this.options.searchInput).val(currentSearch);
        if (currentCategory) $(this.options.categoryFilter).val(currentCategory);
        if (currentStatus) $(this.options.statusFilter).val(currentStatus);
        
        // Toggle clear button visibility
        this.toggleClearButton();
    }
    
    clearSearch() {
        $(this.options.searchInput).val('');
        this.performSearch();
        this.toggleClearButton();
    }
    
    toggleClearButton() {
        const clearBtn = $('#clearSearchBtn');
        const searchValue = $(this.options.searchInput).val();
        
        if (clearBtn.length) {
            if (searchValue && searchValue.trim() !== '') {
                clearBtn.show();
            } else {
                clearBtn.hide();
            }
        }
    }
}

// Initialize search when document is ready and jQuery is available
function initializeAnnouncementSearch() {
    // For index page
    if ($('#searchInput').length && !$('#categoryFilter').length) {
        new AnnouncementSearch({
            searchInput: '#searchInput',
            searchBtn: '#searchBtn'
        });
    }
    
    // For manage page
    if ($('#searchInput').length && $('#categoryFilter').length) {
        new AnnouncementSearch({
            searchInput: '#searchInput',
            searchBtn: '#searchBtn',
            resetBtn: '#resetBtn',
            categoryFilter: '#categoryFilter',
            statusFilter: '#statusFilter'
        });
    }
}

// Simple jQuery availability check
if (typeof $ !== 'undefined') {
    $(document).ready(initializeAnnouncementSearch);
} else {
    // Wait for jQuery to be loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            initializeAnnouncementSearch();
        }
    });
}
</script>
