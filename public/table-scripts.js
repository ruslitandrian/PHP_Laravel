// ============================================
// Table search, sorting, and pagination features
// Pure JavaScript implementation (no framework)
// ============================================

// Original data
const originalData = [
    { name: 'Prescott Barlett', position: 'Technical Author', office: 'London', age: 27, startDate: '2011/05/07' },
    { name: 'Gavin Cortez', position: 'Team Leader', office: 'San Francisco', age: 22, startDate: '2008/10/26' },
    { name: 'Gloria Little', position: 'System Administrator', office: 'New York', age: 59, startDate: '2009/04/01' },
    { name: 'Lael Greet', position: 'Support Lead', office: 'London', age: 30, startDate: '2011/04/25' },
    { name: 'Bradley Greer', position: 'Software Engineer', office: 'London', age: 41, startDate: '2012/10/13' },
    { name: 'Dai Rios', position: 'Personnel Lead', office: 'Edinburgh', age: 35, startDate: '2012/09/26' },
    { name: 'Jenette Caldwell', position: 'Development Lead', office: 'New York', age: 30, startDate: '2011/09/03' },
    { name: 'Yuri Berry', position: 'Chief Marketing Officer', office: 'New York', age: 40, startDate: '2009/06/25' },
    { name: 'Caesar Vance', position: 'Pre-Sales Support', office: 'New York', age: 21, startDate: '2011/12/12' },
    { name: 'Doris Wilder', position: 'Sales Assistant', office: 'Sydney', age: 23, startDate: '2010/09/20' },
    { name: 'Angelica Ramos', position: 'Chief Executive Officer', office: 'London', age: 47, startDate: '2009/10/09' },
    { name: 'Gavin Joyce', position: 'Developer', office: 'Edinburgh', age: 42, startDate: '2010/12/22' },
    { name: 'Jennifer Chang', position: 'Regional Director', office: 'Singapore', age: 28, startDate: '2010/11/14' },
    { name: 'Brenden Wagner', position: 'Software Engineer', office: 'San Francisco', age: 28, startDate: '2011/06/07' },
    { name: 'Fiona Green', position: 'Chief Operating Officer', office: 'San Francisco', age: 48, startDate: '2010/03/11' },
    { name: 'Shou Itou', position: 'Regional Marketing', office: 'Tokyo', age: 20, startDate: '2011/08/14' },
    { name: 'Michelle House', position: 'Integration Specialist', office: 'Sydney', age: 37, startDate: '2011/06/02' },
    { name: 'Suki Burks', position: 'Developer', office: 'London', age: 53, startDate: '2009/10/22' },
    { name: 'Prescott Bartlett', position: 'Technical Author', office: 'London', age: 27, startDate: '2011/05/07' },
    { name: 'Gavin Cortez', position: 'Team Leader', office: 'San Francisco', age: 22, startDate: '2008/10/26' }
];

// Application state
const state = {
    data: [...originalData],
    filteredData: [...originalData],
    sortedData: [...originalData],
    currentPage: 1,
    pageSize: 10,
    sortColumn: null,
    sortDirection: null, // 'asc' or 'desc'
    searchTerm: ''
};

// DOM elements
const elements = {
    searchInput: document.getElementById('searchInput'),
    clearSearch: document.getElementById('clearSearch'),
    tableBody: document.getElementById('tableBody'),
    searchResultInfo: document.getElementById('searchResultInfo'),
    currentCount: document.getElementById('currentCount'),
    totalCount: document.getElementById('totalCount'),
    pageSizeSelect: document.getElementById('pageSizeSelect'),
    prevPage: document.getElementById('prevPage'),
    nextPage: document.getElementById('nextPage'),
    pageNumbers: document.getElementById('pageNumbers'),
    pageStart: document.getElementById('pageStart'),
    pageEnd: document.getElementById('pageEnd'),
    noResults: document.getElementById('noResults'),
    sortableHeaders: document.querySelectorAll('th.sortable')
};

// ============================================
// Search functionality
// ============================================
function performSearch() {
    const searchTerm = state.searchTerm.toLowerCase().trim();
    
    if (!searchTerm) {
        state.filteredData = [...originalData];
    } else {
        state.filteredData = originalData.filter(item => {
            return Object.values(item).some(value => {
                return String(value).toLowerCase().includes(searchTerm);
            });
        });
    }
    
    // Reset to the first page
    state.currentPage = 1;
    
    // Reapply sorting if present
    if (state.sortColumn) {
        applySort(state.sortColumn, state.sortDirection);
    } else {
        state.sortedData = [...state.filteredData];
    }
    
    renderTable();
    updatePagination();
    updateSearchInfo();
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search event listener
const debouncedSearch = debounce(performSearch, 300);

elements.searchInput.addEventListener('input', (e) => {
    state.searchTerm = e.target.value;
    
    // Show or hide the clear button
    if (state.searchTerm) {
        elements.clearSearch.style.display = 'block';
    } else {
        elements.clearSearch.style.display = 'none';
    }
    
    debouncedSearch();
});

elements.clearSearch.addEventListener('click', () => {
    elements.searchInput.value = '';
    state.searchTerm = '';
    elements.clearSearch.style.display = 'none';
    performSearch();
});

// ============================================
// Sorting functionality
// ============================================
function applySort(column, direction) {
    if (!column) {
        state.sortedData = [...state.filteredData];
        return;
    }
    
    state.sortedData = [...state.filteredData].sort((a, b) => {
        let aValue = a[column];
        let bValue = b[column];
        
        // Handle different data types
        if (column === 'age') {
            aValue = parseInt(aValue);
            bValue = parseInt(bValue);
        } else if (column === 'startDate') {
            aValue = new Date(aValue);
            bValue = new Date(bValue);
        } else {
            aValue = String(aValue).toLowerCase();
            bValue = String(bValue).toLowerCase();
        }
        
        if (direction === 'asc') {
            return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
        } else {
            return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
        }
    });
}

function handleSort(column) {
    // Toggle the sort direction when clicking the same column
    if (state.sortColumn === column) {
        if (state.sortDirection === 'asc') {
            state.sortDirection = 'desc';
        } else if (state.sortDirection === 'desc') {
            // Third click: cancel sorting
            state.sortColumn = null;
            state.sortDirection = null;
        }
    } else {
        // New column defaults to ascending
        state.sortColumn = column;
        state.sortDirection = 'asc';
    }
    
    applySort(state.sortColumn, state.sortDirection);
    updateSortIcons();
    renderTable();
    updatePagination();
}

function updateSortIcons() {
    elements.sortableHeaders.forEach(header => {
        const column = header.getAttribute('data-sort');
        const icon = header.querySelector('.sort-icon');
        
        // Remove all styles
        header.classList.remove('sorted');
        icon.classList.remove('asc', 'desc');
        
        // If this is the current sort column, add styles
        if (column === state.sortColumn) {
            header.classList.add('sorted');
            if (state.sortDirection === 'asc') {
                icon.classList.add('asc');
            } else if (state.sortDirection === 'desc') {
                icon.classList.add('desc');
            }
        }
    });
}

// Sort event listeners
elements.sortableHeaders.forEach(header => {
    header.addEventListener('click', () => {
        const column = header.getAttribute('data-sort');
        handleSort(column);
    });
});

// ============================================
// Pagination functionality
// ============================================
function getPaginatedData() {
    const start = (state.currentPage - 1) * state.pageSize;
    const end = start + state.pageSize;
    return state.sortedData.slice(start, end);
}

function getTotalPages() {
    return Math.ceil(state.sortedData.length / state.pageSize);
}

function updatePagination() {
    const totalPages = getTotalPages();
    const totalItems = state.sortedData.length;
    
    // Update page number info
    if (totalItems === 0) {
        elements.pageStart.textContent = '0';
        elements.pageEnd.textContent = '0';
    } else {
        const start = (state.currentPage - 1) * state.pageSize + 1;
        const end = Math.min(state.currentPage * state.pageSize, totalItems);
        elements.pageStart.textContent = start;
        elements.pageEnd.textContent = end;
    }
    
    // Update the previous and next buttons
    elements.prevPage.disabled = state.currentPage === 1;
    elements.nextPage.disabled = state.currentPage === totalPages || totalPages === 0;
    
    // Generate page numbers
    generatePageNumbers(totalPages);
}

function generatePageNumbers(totalPages) {
    elements.pageNumbers.innerHTML = '';
    
    if (totalPages === 0) {
        return;
    }
    
    const maxVisiblePages = 7;
    let startPage = 1;
    let endPage = totalPages;
    
    if (totalPages > maxVisiblePages) {
        if (state.currentPage <= 4) {
            endPage = maxVisiblePages;
        } else if (state.currentPage >= totalPages - 3) {
            startPage = totalPages - maxVisiblePages + 1;
        } else {
            startPage = state.currentPage - 3;
            endPage = state.currentPage + 3;
        }
    }
    
    // First page
    if (startPage > 1) {
        createPageNumber(1);
        if (startPage > 2) {
            createDots();
        }
    }
    
    // Middle page numbers
    for (let i = startPage; i <= endPage; i++) {
        createPageNumber(i);
    }
    
    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            createDots();
        }
        createPageNumber(totalPages);
    }
}

function createPageNumber(pageNumber) {
    const pageBtn = document.createElement('button');
    pageBtn.className = 'page-number';
    pageBtn.textContent = pageNumber;
    
    if (pageNumber === state.currentPage) {
        pageBtn.classList.add('active');
    }
    
    pageBtn.addEventListener('click', () => {
        state.currentPage = pageNumber;
        renderTable();
        updatePagination();
        // Scroll to the top of the table
        elements.tableBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    
    elements.pageNumbers.appendChild(pageBtn);
}

function createDots() {
    const dots = document.createElement('span');
    dots.className = 'page-number dots';
    dots.textContent = '...';
    elements.pageNumbers.appendChild(dots);
}

elements.prevPage.addEventListener('click', () => {
    if (state.currentPage > 1) {
        state.currentPage--;
        renderTable();
        updatePagination();
        elements.tableBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

elements.nextPage.addEventListener('click', () => {
    const totalPages = getTotalPages();
    if (state.currentPage < totalPages) {
        state.currentPage++;
        renderTable();
        updatePagination();
        elements.tableBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

elements.pageSizeSelect.addEventListener('change', (e) => {
    state.pageSize = parseInt(e.target.value);
    state.currentPage = 1;
    renderTable();
    updatePagination();
});

// ============================================
// Render the table
// ============================================
function renderTable() {
    const paginatedData = getPaginatedData();
    
    if (paginatedData.length === 0) {
        elements.tableBody.innerHTML = '';
        elements.noResults.style.display = 'block';
        return;
    }
    
    elements.noResults.style.display = 'none';
    
    elements.tableBody.innerHTML = paginatedData.map(item => `
        <tr>
            <td>${escapeHtml(item.name)}</td>
            <td>${escapeHtml(item.position)}</td>
            <td>${escapeHtml(item.office)}</td>
            <td>${escapeHtml(item.age)}</td>
            <td>${escapeHtml(item.startDate)}</td>
        </tr>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// Update search info
// ============================================
function updateSearchInfo() {
    const total = state.filteredData.length;
    const current = state.sortedData.length; // Account for the number displayed after pagination
    
    elements.totalCount.textContent = total;
    
    // Calculate the number of entries on the current page
    const start = (state.currentPage - 1) * state.pageSize;
    const end = Math.min(state.currentPage * state.pageSize, total);
    const currentDisplay = total > 0 ? end - start : 0;
    elements.currentCount.textContent = currentDisplay;
}

// ============================================
// Initialization
// ============================================
function init() {
    // Initialize data
    state.data = [...originalData];
    state.filteredData = [...originalData];
    state.sortedData = [...originalData];
    
    // Render the table
    renderTable();
    updatePagination();
    updateSearchInfo();
    updateSortIcons();
}

// Initialize after the page finishes loading
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}


