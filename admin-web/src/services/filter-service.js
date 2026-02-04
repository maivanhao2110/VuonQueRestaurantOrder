/**
 * Filter Service Module - Following SOLID Principles
 * 
 * S - Single Responsibility: Each filter strategy handles one type of filtering
 * O - Open/Closed: Easy to add new filters without modifying existing code
 * L - Liskov Substitution: All filter strategies are interchangeable
 * I - Interface Segregation: Pages only use filters they need
 * D - Dependency Inversion: Pages depend on FilterManager abstraction
 */

// ============================================
// Filter Strategy Interface (Abstract Class)
// ============================================
class FilterStrategy {
    constructor(config) {
        this.id = config.id;
        this.label = config.label;
        this.field = config.field;
    }

    // Abstract method - must be implemented by subclasses
    createUI() {
        throw new Error('createUI() must be implemented');
    }

    // Abstract method - must be implemented by subclasses
    filter(data, value) {
        throw new Error('filter() must be implemented');
    }

    getValue() {
        const element = document.getElementById(this.id);
        return element ? element.value : '';
    }
}

// ============================================
// Text Filter Strategy (Search by text)
// ============================================
class TextFilterStrategy extends FilterStrategy {
    constructor(config) {
        super(config);
        this.placeholder = config.placeholder || 'Tìm kiếm...';
        this.searchFields = config.searchFields || [config.field];
    }

    createUI() {
        const wrapper = document.createElement('div');
        wrapper.className = 'filter-item';
        wrapper.innerHTML = `
            <label for="${this.id}">${this.label}</label>
            <input type="text" 
                   id="${this.id}" 
                   class="filter-search" 
                   placeholder="${this.placeholder}">
        `;
        return wrapper;
    }

    filter(data, value) {
        if (!value || value.trim() === '') return data;
        
        const searchTerm = value.toLowerCase().trim();
        return data.filter(item => {
            return this.searchFields.some(field => {
                const fieldValue = item[field];
                return fieldValue && fieldValue.toString().toLowerCase().includes(searchTerm);
            });
        });
    }
}

// ============================================
// Select Filter Strategy (Dropdown filter)
// ============================================
class SelectFilterStrategy extends FilterStrategy {
    constructor(config) {
        super(config);
        this.options = config.options || [];
        this.allLabel = config.allLabel || 'Tất cả';
        this.dynamicOptions = config.dynamicOptions || null;
    }

    createUI() {
        const wrapper = document.createElement('div');
        wrapper.className = 'filter-item';
        
        let optionsHtml = `<option value="">${this.allLabel}</option>`;
        this.options.forEach(opt => {
            optionsHtml += `<option value="${opt.value}">${opt.label}</option>`;
        });

        wrapper.innerHTML = `
            <label for="${this.id}">${this.label}</label>
            <select id="${this.id}" class="filter-select">
                ${optionsHtml}
            </select>
        `;
        return wrapper;
    }

    // Allow updating options dynamically (e.g., categories from API)
    updateOptions(options) {
        const select = document.getElementById(this.id);
        if (!select) return;

        let optionsHtml = `<option value="">${this.allLabel}</option>`;
        options.forEach(opt => {
            optionsHtml += `<option value="${opt.value}">${opt.label}</option>`;
        });
        select.innerHTML = optionsHtml;
    }

    filter(data, value) {
        if (!value || value === '') return data;

        return data.filter(item => {
            const fieldValue = item[this.field];
            // Handle both string and number comparisons
            return fieldValue != null && fieldValue.toString() === value.toString();
        });
    }
}

// ============================================
// Date Filter Strategy (Date range filter)
// ============================================
class DateFilterStrategy extends FilterStrategy {
    constructor(config) {
        super(config);
        this.fromId = config.id + '_from';
        this.toId = config.id + '_to';
        this.fromLabel = config.fromLabel || 'Từ ngày';
        this.toLabel = config.toLabel || 'Đến ngày';
    }

    createUI() {
        const wrapper = document.createElement('div');
        wrapper.className = 'filter-item filter-date-range';
        wrapper.innerHTML = `
            <div class="date-input-group">
                <label for="${this.fromId}">${this.fromLabel}</label>
                <input type="date" id="${this.fromId}" class="filter-date">
            </div>
            <div class="date-input-group">
                <label for="${this.toId}">${this.toLabel}</label>
                <input type="date" id="${this.toId}" class="filter-date">
            </div>
        `;
        return wrapper;
    }

    getValue() {
        const fromEl = document.getElementById(this.fromId);
        const toEl = document.getElementById(this.toId);
        return {
            from: fromEl ? fromEl.value : '',
            to: toEl ? toEl.value : ''
        };
    }

    filter(data, value) {
        const { from, to } = value;
        if (!from && !to) return data;

        return data.filter(item => {
            const itemDate = this.parseDate(item[this.field]);
            if (!itemDate) return true;

            const fromDate = from ? new Date(from) : null;
            const toDate = to ? new Date(to) : null;

            if (fromDate && toDate) {
                return itemDate >= fromDate && itemDate <= toDate;
            } else if (fromDate) {
                return itemDate >= fromDate;
            } else if (toDate) {
                return itemDate <= toDate;
            }
            return true;
        });
    }

    parseDate(dateString) {
        if (!dateString) return null;
        // Handle multiple date formats
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? null : date;
    }
}

// ============================================
// Filter Manager (Manages collection of filters)
// ============================================
class FilterManager {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.filters = [];
        this.originalData = [];
        this.onFilterCallback = options.onFilter || null;
        this.debounceTime = options.debounceTime || 300;
        this.debounceTimer = null;
    }

    // Add a filter strategy
    addFilter(filterStrategy) {
        this.filters.push(filterStrategy);
        return this; // Enable method chaining
    }

    // Render all filter UIs
    render() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Filter container #${this.containerId} not found`);
            return;
        }

        // Create filter bar
        const filterBar = document.createElement('div');
        filterBar.className = 'filter-bar';

        // Add each filter UI
        this.filters.forEach(filter => {
            filterBar.appendChild(filter.createUI());
        });

        // Add clear button
        const clearBtn = document.createElement('button');
        clearBtn.className = 'btn filter-clear-btn';
        clearBtn.textContent = 'Xóa lọc';
        clearBtn.onclick = () => this.clearFilters();
        
        const clearWrapper = document.createElement('div');
        clearWrapper.className = 'filter-item filter-clear-wrapper';
        clearWrapper.appendChild(clearBtn);
        filterBar.appendChild(clearWrapper);

        container.appendChild(filterBar);

        // Bind events
        this.bindEvents();
    }

    // Bind change/input events to all filter elements
    bindEvents() {
        this.filters.forEach(filter => {
            if (filter instanceof DateFilterStrategy) {
                const fromEl = document.getElementById(filter.fromId);
                const toEl = document.getElementById(filter.toId);
                if (fromEl) fromEl.addEventListener('change', () => this.triggerFilter());
                if (toEl) toEl.addEventListener('change', () => this.triggerFilter());
            } else if (filter instanceof TextFilterStrategy) {
                const el = document.getElementById(filter.id);
                if (el) el.addEventListener('input', () => this.triggerFilterDebounced());
            } else {
                const el = document.getElementById(filter.id);
                if (el) el.addEventListener('change', () => this.triggerFilter());
            }
        });
    }

    // Trigger filter with debounce (for text input)
    triggerFilterDebounced() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.triggerFilter(), this.debounceTime);
    }

    // Apply all filters and call callback
    triggerFilter() {
        let filteredData = [...this.originalData];

        this.filters.forEach(filter => {
            const value = filter.getValue();
            filteredData = filter.filter(filteredData, value);
        });

        if (this.onFilterCallback) {
            this.onFilterCallback(filteredData);
        }
    }

    // Set the original data to filter
    setData(data) {
        this.originalData = data || [];
        this.triggerFilter();
    }

    // Clear all filters
    clearFilters() {
        this.filters.forEach(filter => {
            if (filter instanceof DateFilterStrategy) {
                const fromEl = document.getElementById(filter.fromId);
                const toEl = document.getElementById(filter.toId);
                if (fromEl) fromEl.value = '';
                if (toEl) toEl.value = '';
            } else {
                const el = document.getElementById(filter.id);
                if (el) el.value = '';
            }
        });
        this.triggerFilter();
    }

    // Get a specific filter by ID
    getFilter(id) {
        return this.filters.find(f => f.id === id);
    }
}

// Export for use in pages
window.FilterStrategy = FilterStrategy;
window.TextFilterStrategy = TextFilterStrategy;
window.SelectFilterStrategy = SelectFilterStrategy;
window.DateFilterStrategy = DateFilterStrategy;
window.FilterManager = FilterManager;
