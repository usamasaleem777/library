/**
 * Library Catalog JavaScript
 * Handles AJAX filtering, search, pagination, and UI interactions
 */
class CatalogManager {
    constructor() {
        this.currentFilters = {
            search: '',
            availability: [],
            format: [],
            genre: [],
            year_min: 1900,
            year_max: new Date().getFullYear(),
            sort: 'title-asc',
            page: 1
        };
        
        this.isLoading = false;
        this.debounceTimeout = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeFilters();
        this.setupRangeSliders();
    }
    
    bindEvents() {
        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.debounceSearch(e.target.value);
            });
        }
        
        // Filter checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateFilters();
            });
        });
        
        // Sort dropdown
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.currentFilters.sort = e.target.value;
                this.currentFilters.page = 1;
                this.loadBooks();
            });
        }
        
        // Pagination
        document.addEventListener('click', (e) => {
            if (e.target.closest('.pagination-item:not(.disabled) a')) {
                e.preventDefault();
                const page = parseInt(e.target.closest('a').dataset.page);
                if (page && page !== this.currentFilters.page) {
                    this.currentFilters.page = page;
                    this.loadBooks();
                }
            }
        });
        
        // Filter toggle (mobile)
        const filterToggle = document.getElementById('filterToggle');
        const filterSidebar = document.getElementById('filterSidebar');
        if (filterToggle && filterSidebar) {
            filterToggle.addEventListener('click', () => {
                filterSidebar.classList.toggle('mobile-visible');
            });
        }
        
        // Filter section toggles
        document.querySelectorAll('.filter-section h4').forEach(header => {
            header.addEventListener('click', () => {
                header.parentElement.classList.toggle('collapsed');
            });
        });
        
        // View options
        document.querySelectorAll('.view-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.view-option').forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                this.toggleView(option.dataset.view);
            });
        });
        
        // Reset filters
        const resetButton = document.getElementById('resetFilters');
        if (resetButton) {
            resetButton.addEventListener('click', () => {
                this.resetFilters();
            });
        }
        
        // Wishlist buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.wishlist-btn')) {
                e.preventDefault();
                const button = e.target.closest('.wishlist-btn');
                const bookId = button.dataset.bookId;
                this.toggleWishlist(bookId, button);
            }
        });
    }
    
    initializeFilters() {
        // Get current filters from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        this.currentFilters.search = urlParams.get('search') || '';
        this.currentFilters.sort = urlParams.get('sort') || 'title-asc';
        this.currentFilters.page = parseInt(urlParams.get('page')) || 1;
        
        // Parse array filters
        const availability = urlParams.get('availability');
        if (availability) {
            this.currentFilters.availability = availability.split(',');
        }
        
        const format = urlParams.get('format');
        if (format) {
            this.currentFilters.format = format.split(',');
        }
        
        const genre = urlParams.get('genre');
        if (genre) {
            this.currentFilters.genre = genre.split(',');
        }
        
        // Parse year range
        this.currentFilters.year_min = parseInt(urlParams.get('year_min')) || 1900;
        this.currentFilters.year_max = parseInt(urlParams.get('year_max')) || new Date().getFullYear();
        
        // Set form values based on current filters
        this.updateFormFromFilters();
        
        // Load initial results
        this.loadBooks();
    }
    
    setupRangeSliders() {
        const yearRangeSlider = document.getElementById('yearRange');
        if (yearRangeSlider) {
            // Initialize range slider (assuming noUiSlider or similar library)
            if (typeof noUiSlider !== 'undefined') {
                noUiSlider.create(yearRangeSlider, {
                    start: [this.currentFilters.year_min, this.currentFilters.year_max],
                    connect: true,
                    range: {
                        'min': 1900,
                        'max': new Date().getFullYear()
                    },
                    step: 1,
                    format: {
                        to: value => Math.round(value),
                        from: value => Number(value)
                    }
                });
                
                yearRangeSlider.noUiSlider.on('change', (values) => {
                    this.currentFilters.year_min = parseInt(values[0]);
                    this.currentFilters.year_max = parseInt(values[1]);
                    this.currentFilters.page = 1;
                    this.loadBooks();
                });
            }
        }
    }
    
    debounceSearch(searchTerm) {
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
            this.currentFilters.search = searchTerm;
            this.currentFilters.page = 1;
            this.loadBooks();
        }, 300);
    }
    
    updateFilters() {
        // Clear existing filter arrays
        this.currentFilters.availability = [];
        this.currentFilters.format = [];
        this.currentFilters.genre = [];
        
        // Collect checked availability filters
        document.querySelectorAll('input[name="availability"]:checked').forEach(checkbox => {
            this.currentFilters.availability.push(checkbox.value);
        });
        
        // Collect checked format filters
        document.querySelectorAll('input[name="format"]:checked').forEach(checkbox => {
            this.currentFilters.format.push(checkbox.value);
        });
        
        // Collect checked genre filters
        document.querySelectorAll('input[name="genre"]:checked').forEach(checkbox => {
            this.currentFilters.genre.push(checkbox.value);
        });
        
        // Reset to first page when filters change
        this.currentFilters.page = 1;
        
        this.loadBooks();
    }
    
    updateFormFromFilters() {
        // Update search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = this.currentFilters.search;
        }
        
        // Update sort select
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.value = this.currentFilters.sort;
        }
        
        // Update checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            const filterType = checkbox.name;
            const filterValue = checkbox.value;
            
            if (this.currentFilters[filterType] && this.currentFilters[filterType].includes(filterValue)) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
        
        // Update year range display
        const yearMinDisplay = document.getElementById('yearMinDisplay');
        const yearMaxDisplay = document.getElementById('yearMaxDisplay');
        if (yearMinDisplay) yearMinDisplay.textContent = this.currentFilters.year_min;
        if (yearMaxDisplay) yearMaxDisplay.textContent = this.currentFilters.year_max;
    }
    
    async loadBooks() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoadingState();
        
        try {
            // Build query string
            const params = new URLSearchParams();
            
            Object.keys(this.currentFilters).forEach(key => {
                const value = this.currentFilters[key];
                if (Array.isArray(value) && value.length > 0) {
                    params.set(key, value.join(','));
                } else if (value && (typeof value === 'string' || typeof value === 'number')) {
                    params.set(key, value);
                }
            });
            
            // Make AJAX request
            const response = await fetch(`/catalog/books?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update the page content
            this.updateBooksList(data.books);
            this.updatePagination(data.pagination);
            this.updateResultsCount(data.total_results);
            this.updateActiveFilters();
            
            // Update URL without page reload
            this.updateURL();
            
        } catch (error) {
            console.error('Error loading books:', error);
            this.showErrorState();
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }
    
    updateBooksList(books) {
        const booksContainer = document.getElementById('booksContainer');
        if (!booksContainer) return;
        
        if (books.length === 0) {
            booksContainer.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">📚</div>
                    <h3>No books found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                </div>
            `;
            return;
        }
        
        const currentView = document.querySelector('.view-option.active')?.dataset.view || 'grid';
        const booksHTML = books.map(book => this.renderBookCard(book, currentView)).join('');
        booksContainer.innerHTML = booksHTML;
    }
    
    renderBookCard(book, view) {
        const availability = book.is_available ? 'available' : 'checked-out';
        const availabilityText = book.is_available ? 'Available' : 'Checked Out';
        const wishlistIcon = book.in_wishlist ? '❤️' : '🤍';
        
        if (view === 'list') {
            return `
                <div class="book-card list-view" data-book-id="${book.id}">
                    <div class="book-image">
                        <img src="${book.cover_image || '/static/images/default-book.jpg'}" 
                             alt="${book.title}" loading="lazy">
                    </div>
                    <div class="book-info">
                        <h3 class="book-title">
                            <a href="/catalog/book/${book.id}">${book.title}</a>
                        </h3>
                        <p class="book-author">by ${book.author}</p>
                        <p class="book-description">${book.description || 'No description available.'}</p>
                        <div class="book-meta">
                            <span class="book-year">${book.publication_year}</span>
                            <span class="book-genre">${book.genre}</span>
                            <span class="book-format">${book.format}</span>
                        </div>
                    </div>
                    <div class="book-actions">
                        <div class="availability ${availability}">
                            <span class="status-indicator"></span>
                            ${availabilityText}
                        </div>
                        <button class="wishlist-btn" data-book-id="${book.id}" 
                                title="${book.in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'}">
                            ${wishlistIcon}
                        </button>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="book-card grid-view" data-book-id="${book.id}">
                    <div class="book-image">
                        <img src="${book.cover_image || '/static/images/default-book.jpg'}" 
                             alt="${book.title}" loading="lazy">
                        <div class="book-overlay">
                            <a href="/catalog/book/${book.id}" class="view-details">View Details</a>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title">
                            <a href="/catalog/book/${book.id}">${book.title}</a>
                        </h3>
                        <p class="book-author">by ${book.author}</p>
                        <div class="book-meta">
                            <span class="book-year">${book.publication_year}</span>
                            <span class="book-genre">${book.genre}</span>
                        </div>
                        <div class="book-actions">
                            <div class="availability ${availability}">
                                <span class="status-indicator"></span>
                                ${availabilityText}
                            </div>
                            <button class="wishlist-btn" data-book-id="${book.id}" 
                                    title="${book.in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'}">
                                ${wishlistIcon}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    updatePagination(pagination) {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer || !pagination) return;
        
        let paginationHTML = '';
        
        // Previous button
        if (pagination.has_previous) {
            paginationHTML += `
                <div class="pagination-item">
                    <a href="#" data-page="${pagination.current_page - 1}">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </div>
            `;
        }
        
        // Page numbers
        for (let i = pagination.start_page; i <= pagination.end_page; i++) {
            const isActive = i === pagination.current_page;
            paginationHTML += `
                <div class="pagination-item ${isActive ? 'active' : ''}">
                    <a href="#" data-page="${i}">${i}</a>
                </div>
            `;
        }
        
        // Next button
        if (pagination.has_next) {
            paginationHTML += `
                <div class="pagination-item">
                    <a href="#" data-page="${pagination.current_page + 1}">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            `;
        }
        
        paginationContainer.innerHTML = paginationHTML;
    }
    
    updateResultsCount(totalResults) {
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            const startResult = ((this.currentFilters.page - 1) * 12) + 1;
            const endResult = Math.min(this.currentFilters.page * 12, totalResults);
            
            resultsCount.textContent = totalResults > 0 
                ? `Showing ${startResult}-${endResult} of ${totalResults} books`
                : 'No books found';
        }
    }
    
    updateActiveFilters() {
        const activeFiltersContainer = document.getElementById('activeFilters');
        if (!activeFiltersContainer) return;
        
        const activeFilters = [];
        
        // Search filter
        if (this.currentFilters.search) {
            activeFilters.push({
                type: 'search',
                label: `Search: "${this.currentFilters.search}"`,
                value: this.currentFilters.search
            });
        }
        
        // Availability filters
        this.currentFilters.availability.forEach(value => {
            activeFilters.push({
                type: 'availability',
                label: `Availability: ${value}`,
                value: value
            });
        });
        
        // Format filters
        this.currentFilters.format.forEach(value => {
            activeFilters.push({
                type: 'format',
                label: `Format: ${value}`,
                value: value
            });
        });
        
        // Genre filters
        this.currentFilters.genre.forEach(value => {
            activeFilters.push({
                type: 'genre',
                label: `Genre: ${value}`,
                value: value
            });
        });
        
        // Year range filter
        if (this.currentFilters.year_min > 1900 || this.currentFilters.year_max < new Date().getFullYear()) {
            activeFilters.push({
                type: 'year',
                label: `Year: ${this.currentFilters.year_min}-${this.currentFilters.year_max}`,
                value: 'year_range'
            });
        }
        
        if (activeFilters.length === 0) {
            activeFiltersContainer.style.display = 'none';
            return;
        }
        
        activeFiltersContainer.style.display = 'block';
        const filtersHTML = activeFilters.map(filter => `
            <div class="active-filter" data-filter-type="${filter.type}" data-filter-value="${filter.value}">
                <span>${filter.label}</span>
                <button class="remove-filter" data-filter-type="${filter.type}" data-filter-value="${filter.value}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
        
        activeFiltersContainer.innerHTML = `
            <div class="active-filters-header">
                <span>Active Filters:</span>
                <button id="clearAllFilters" class="clear-all-btn">Clear All</button>
            </div>
            <div class="active-filters-list">
                ${filtersHTML}
            </div>
        `;
        
        // Bind remove filter events
        activeFiltersContainer.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                this.removeFilter(btn.dataset.filterType, btn.dataset.filterValue);
            });
        });
        
        // Bind clear all event
        const clearAllBtn = document.getElementById('clearAllFilters');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => {
                this.resetFilters();
            });
        }
    }
    
    removeFilter(filterType, filterValue) {
        switch (filterType) {
            case 'search':
                this.currentFilters.search = '';
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.value = '';
                break;
            case 'availability':
                this.currentFilters.availability = this.currentFilters.availability.filter(v => v !== filterValue);
                break;
            case 'format':
                this.currentFilters.format = this.currentFilters.format.filter(v => v !== filterValue);
                break;
            case 'genre':
                this.currentFilters.genre = this.currentFilters.genre.filter(v => v !== filterValue);
                break;
            case 'year':
                this.currentFilters.year_min = 1900;
                this.currentFilters.year_max = new Date().getFullYear();
                break;
        }
        
        this.currentFilters.page = 1;
        this.updateFormFromFilters();
        this.loadBooks();
    }
    
    resetFilters() {
        this.currentFilters = {
            search: '',
            availability: [],
            format: [],
            genre: [],
            year_min: 1900,
            year_max: new Date().getFullYear(),
            sort: 'title-asc',
            page: 1
        };
        
        this.updateFormFromFilters();
        this.loadBooks();
    }
    
    toggleView(viewType) {
        const booksContainer = document.getElementById('booksContainer');
        if (booksContainer) {
            booksContainer.className = `books-container ${viewType}-view`;
        }
        
        // Store view preference
        if (typeof Storage !== 'undefined') {
            localStorage.setItem('catalog_view', viewType);
        }
    }
    
    async toggleWishlist(bookId, button) {
        try {
            const response = await fetch(`/catalog/wishlist/toggle/${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update button appearance
            if (data.in_wishlist) {
                button.innerHTML = '❤️';
                button.title = 'Remove from wishlist';
                button.classList.add('in-wishlist');
            } else {
                button.innerHTML = '🤍';
                button.title = 'Add to wishlist';
                button.classList.remove('in-wishlist');
            }
            
            // Show success message
            this.showNotification(data.message, 'success');
            
        } catch (error) {
            console.error('Error toggling wishlist:', error);
            this.showNotification('Error updating wishlist. Please try again.', 'error');
        }
    }
    
    updateURL() {
        const params = new URLSearchParams();
        
        Object.keys(this.currentFilters).forEach(key => {
            const value = this.currentFilters[key];
            if (Array.isArray(value) && value.length > 0) {
                params.set(key, value.join(','));
            } else if (value && value !== '' && (typeof value === 'string' || typeof value === 'number')) {
                // Don't include default values in URL
                if (key === 'year_min' && value === 1900) return;
                if (key === 'year_max' && value === new Date().getFullYear()) return;
                if (key === 'sort' && value === 'title-asc') return;
                if (key === 'page' && value === 1) return;
                
                params.set(key, value);
            }
        });
        
        const newURL = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
        window.history.replaceState(null, '', newURL);
    }
    
    showLoadingState() {
        const booksContainer = document.getElementById('booksContainer');
        if (booksContainer) {
            booksContainer.classList.add('loading');
        }
        
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }
    }
    
    hideLoadingState() {
        const booksContainer = document.getElementById('booksContainer');
        if (booksContainer) {
            booksContainer.classList.remove('loading');
        }
        
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }
    
    showErrorState() {
        const booksContainer = document.getElementById('booksContainer');
        if (booksContainer) {
            booksContainer.innerHTML = `
                <div class="error-state">
                    <div class="error-icon">⚠️</div>
                    <h3>Something went wrong</h3>
                    <p>We couldn't load the books. Please try again.</p>
                    <button onclick="window.catalogManager.loadBooks()" class="retry-btn">
                        Try Again
                    </button>
                </div>
            `;
        }
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
        
        // Close button functionality
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.catalogManager = new CatalogManager();
    
    // Initialize view preference
    const savedView = localStorage.getItem('catalog_view') || 'grid';
    const viewOption = document.querySelector(`.view-option[data-view="${savedView}"]`);
    if (viewOption) {
        document.querySelectorAll('.view-option').forEach(opt => opt.classList.remove('active'));
        viewOption.classList.add('active');
        window.catalogManager.toggleView(savedView);
    }
});

// Export for testing or external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CatalogManager;
}