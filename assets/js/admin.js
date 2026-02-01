/**
 * EduFlora Admin Panel JavaScript
 * Enhanced responsiveness and user experience
 */

class AdminPanel {
    constructor() {
        this.init();
        this.bindEvents();
        this.setupResponsive();
    }

    init() {
        this.sidebar = document.getElementById('adminSidebar');
        this.mobileToggle = document.querySelector('.mobile-menu-toggle');
        this.adminMain = document.querySelector('.admin-main');
        this.isDesktop = window.innerWidth > 768;
        
        // Initialize components
        this.initAnimations();
        this.initTooltips();
        this.initLoadingStates();
        this.initFormValidation();
    }

    bindEvents() {
        // Mobile menu toggle
        if (this.mobileToggle) {
            this.mobileToggle.addEventListener('click', () => this.toggleSidebar());
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', (e) => this.handleOutsideClick(e));

        // Window resize handler
        window.addEventListener('resize', () => this.handleResize());

        // Form submission handlers - simplified
        document.querySelectorAll('form').forEach(form => {
            // Only add loading state, don't interfere with submission
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    this.setButtonLoading(submitBtn, true);
                });
            }
        });

        // Button click handlers
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleButtonClick(e));
        });

        // Search input enhancements
        const searchInputs = document.querySelectorAll('input[type="search"], input[name="search"]');
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => this.handleSearch(e));
            input.addEventListener('focus', (e) => this.handleSearchFocus(e));
            input.addEventListener('blur', (e) => this.handleSearchBlur(e));
        });

        // Table enhancements
        this.enhanceTable();

        // Modal handlers
        this.initModals();

        // File upload handlers
        this.initFileUploads();
    }

    setupResponsive() {
        // Set initial responsive state
        this.updateResponsiveState();

        // Add responsive classes
        document.body.classList.add(this.isDesktop ? 'desktop' : 'mobile');

        // Setup responsive tables
        this.setupResponsiveTables();

        // Setup responsive forms
        this.setupResponsiveForms();
    }

    toggleSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.toggle('active');
            
            // Add overlay for mobile
            if (!this.isDesktop) {
                this.toggleOverlay();
            }
        }
    }

    toggleOverlay() {
        let overlay = document.querySelector('.sidebar-overlay');
        
        if (this.sidebar.classList.contains('active')) {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999;
                    backdrop-filter: blur(2px);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;
                document.body.appendChild(overlay);
                
                // Trigger animation
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                });
                
                overlay.addEventListener('click', () => this.closeSidebar());
            }
        } else if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 300);
        }
    }

    closeSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.remove('active');
            this.toggleOverlay();
        }
    }

    handleOutsideClick(e) {
        if (!this.isDesktop && 
            this.sidebar && 
            this.sidebar.classList.contains('active') &&
            !this.sidebar.contains(e.target) && 
            !this.mobileToggle?.contains(e.target)) {
            this.closeSidebar();
        }
    }

    handleResize() {
        const wasDesktop = this.isDesktop;
        this.isDesktop = window.innerWidth > 768;
        
        if (wasDesktop !== this.isDesktop) {
            this.updateResponsiveState();
            
            // Close sidebar when switching to desktop
            if (this.isDesktop) {
                this.closeSidebar();
            }
        }

        // Update responsive components
        this.updateResponsiveTables();
        this.updateResponsiveForms();
    }

    updateResponsiveState() {
        document.body.classList.toggle('desktop', this.isDesktop);
        document.body.classList.toggle('mobile', !this.isDesktop);
    }

    initAnimations() {
        // Animate stat cards
        const statCards = document.querySelectorAll('.stat-card');
        this.animateElements(statCards, 'slideInUp', 100);

        // Animate table rows
        const tableRows = document.querySelectorAll('tbody tr');
        this.animateElements(tableRows, 'fadeInUp', 50);

        // Animate quick action cards
        const actionCards = document.querySelectorAll('.quick-action-card');
        this.animateElements(actionCards, 'slideInUp', 150);

        // Animate form sections
        const formSections = document.querySelectorAll('.form-section');
        this.animateElements(formSections, 'slideInUp', 100);
    }

    animateElements(elements, animationName, delayIncrement = 100) {
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.animationDelay = `${index * delayIncrement}ms`;
            element.style.animation = `${animationName} 0.6s ease-out forwards`;
        });
    }

    initTooltips() {
        // Add tooltips to action buttons
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.setAttribute('data-tooltip', 'Edit');
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.setAttribute('data-tooltip', 'Hapus');
        });

        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.setAttribute('data-tooltip', 'Lihat Detail');
        });
    }

    initLoadingStates() {
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Add close buttons to alerts
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.querySelector('.alert-close')) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'alert-close';
                closeBtn.innerHTML = '&times;';
                closeBtn.addEventListener('click', () => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 300);
                });
                alert.appendChild(closeBtn);
            }
        });
    }

    initFormValidation() {
        // Real-time validation
        document.querySelectorAll('input[required], textarea[required], select[required]').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });

        // Character counters for textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            this.addCharacterCounter(textarea);
        });
    }

    validateField(field) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;

        formGroup.classList.remove('error', 'success');

        if (!field.value.trim() && field.hasAttribute('required')) {
            formGroup.classList.add('error');
            this.showFieldError(field, 'Field ini wajib diisi');
        } else if (field.type === 'email' && field.value && !this.isValidEmail(field.value)) {
            formGroup.classList.add('error');
            this.showFieldError(field, 'Format email tidak valid');
        } else if (field.value.trim()) {
            formGroup.classList.add('success');
        }
    }

    clearFieldError(field) {
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.classList.remove('error');
        }
    }

    showFieldError(field, message) {
        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            errorElement.style.cssText = `
                color: #ef4444;
                font-size: 0.75rem;
                margin-top: 0.25rem;
            `;
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    addCharacterCounter(textarea) {
        const maxLength = textarea.getAttribute('maxlength');
        if (!maxLength) return;

        const counter = document.createElement('div');
        counter.className = 'character-counter';
        counter.style.cssText = `
            font-size: 0.75rem;
            color: var(--gray-500);
            text-align: right;
            margin-top: 0.25rem;
        `;
        
        const updateCounter = () => {
            const current = textarea.value.length;
            counter.textContent = `${current}/${maxLength}`;
            counter.style.color = current > maxLength * 0.9 ? '#ef4444' : 'var(--gray-500)';
        };

        textarea.addEventListener('input', updateCounter);
        textarea.parentNode.appendChild(counter);
        updateCounter();
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        
        // Don't prevent form submission, just add loading state
        if (submitBtn && !submitBtn.classList.contains('btn-delete')) {
            this.setButtonLoading(submitBtn, true);
        }
        
        // Allow form to submit normally
        return true;
    }

    handleButtonClick(e) {
        const btn = e.target.closest('.btn');
        if (btn && !btn.classList.contains('btn-delete') && !btn.classList.contains('mobile-menu-toggle')) {
            this.setButtonLoading(btn, true);
            
            setTimeout(() => {
                this.setButtonLoading(btn, false);
            }, 1000);
        }
    }

    setButtonLoading(btn, loading) {
        if (loading) {
            btn.classList.add('loading');
            btn.disabled = true;
        } else {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    }

    handleSearch(e) {
        const input = e.target;
        const searchTerm = input.value.toLowerCase();
        
        // Debounce search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch(searchTerm);
        }, 300);
    }

    handleSearchFocus(e) {
        const inputGroup = e.target.closest('.search-input-group');
        if (inputGroup) {
            inputGroup.style.transform = 'scale(1.02)';
            inputGroup.style.boxShadow = '0 4px 12px rgba(46, 139, 87, 0.15)';
        }
    }

    handleSearchBlur(e) {
        const inputGroup = e.target.closest('.search-input-group');
        if (inputGroup) {
            inputGroup.style.transform = 'scale(1)';
            inputGroup.style.boxShadow = 'none';
        }
    }

    performSearch(searchTerm) {
        // This would typically make an AJAX request
        // For now, we'll just filter visible table rows
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const shouldShow = !searchTerm || text.includes(searchTerm);
            
            row.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) {
                row.style.animation = 'fadeIn 0.3s ease-in';
            }
        });
    }

    enhanceTable() {
        // Add sorting capability
        document.querySelectorAll('th').forEach(th => {
            if (th.textContent.trim() && !th.querySelector('i')) {
                th.style.cursor = 'pointer';
                th.addEventListener('click', () => this.sortTable(th));
            }
        });

        // Add row selection
        this.addRowSelection();

        // Add responsive scroll indicators
        this.addScrollIndicators();
    }

    sortTable(th) {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const columnIndex = Array.from(th.parentNode.children).indexOf(th);
        
        const isAscending = !th.classList.contains('sort-asc');
        
        // Remove existing sort classes
        th.parentNode.querySelectorAll('th').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });
        
        // Add new sort class
        th.classList.add(isAscending ? 'sort-asc' : 'sort-desc');
        
        // Sort rows
        rows.sort((a, b) => {
            const aText = a.children[columnIndex]?.textContent.trim() || '';
            const bText = b.children[columnIndex]?.textContent.trim() || '';
            
            const result = aText.localeCompare(bText, 'id', { numeric: true });
            return isAscending ? result : -result;
        });
        
        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
        
        // Add sort indicator
        this.updateSortIndicator(th, isAscending);
    }

    updateSortIndicator(th, isAscending) {
        let indicator = th.querySelector('.sort-indicator');
        if (!indicator) {
            indicator = document.createElement('i');
            indicator.className = 'sort-indicator fas';
            indicator.style.marginLeft = '0.5rem';
            th.appendChild(indicator);
        }
        
        indicator.className = `sort-indicator fas fa-sort-${isAscending ? 'up' : 'down'}`;
    }

    addRowSelection() {
        const table = document.querySelector('table');
        if (!table) return;

        // Add select all checkbox to header
        const headerRow = table.querySelector('thead tr');
        if (headerRow && !headerRow.querySelector('.select-column')) {
            const selectAllTh = document.createElement('th');
            selectAllTh.className = 'select-column';
            selectAllTh.innerHTML = '<input type="checkbox" class="select-all">';
            headerRow.insertBefore(selectAllTh, headerRow.firstChild);
        }

        // Add checkboxes to each row
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
            if (!row.querySelector('.select-column')) {
                const selectTd = document.createElement('td');
                selectTd.className = 'select-column';
                selectTd.innerHTML = '<input type="checkbox" class="select-row">';
                row.insertBefore(selectTd, row.firstChild);
            }
        });

        // Handle select all
        const selectAll = table.querySelector('.select-all');
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                const checkboxes = table.querySelectorAll('.select-row');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
                this.updateBulkActions();
            });
        }

        // Handle individual selection
        table.querySelectorAll('.select-row').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.updateBulkActions());
        });
    }

    updateBulkActions() {
        const selectedRows = document.querySelectorAll('.select-row:checked');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (selectedRows.length > 0) {
            if (!bulkActions) {
                this.createBulkActions();
            } else {
                bulkActions.style.display = 'flex';
            }
        } else if (bulkActions) {
            bulkActions.style.display = 'none';
        }
    }

    createBulkActions() {
        const contentHeader = document.querySelector('.content-header');
        if (!contentHeader) return;

        const bulkActions = document.createElement('div');
        bulkActions.className = 'bulk-actions';
        bulkActions.style.cssText = `
            display: flex;
            gap: 0.75rem;
            align-items: center;
            padding: 1rem;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
        `;
        
        bulkActions.innerHTML = `
            <span class="bulk-text">Aksi untuk item terpilih:</span>
            <button class="btn btn-danger btn-sm bulk-delete">
                <i class="fas fa-trash"></i> Hapus Terpilih
            </button>
            <button class="btn btn-secondary btn-sm bulk-export">
                <i class="fas fa-download"></i> Export Terpilih
            </button>
        `;
        
        contentHeader.parentNode.insertBefore(bulkActions, contentHeader.nextSibling);
        
        // Add event listeners
        bulkActions.querySelector('.bulk-delete').addEventListener('click', () => this.handleBulkDelete());
        bulkActions.querySelector('.bulk-export').addEventListener('click', () => this.handleBulkExport());
    }

    handleBulkDelete() {
        const selectedRows = document.querySelectorAll('.select-row:checked');
        if (selectedRows.length === 0) return;

        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedRows.length} item terpilih?`)) {
            // This would typically make an AJAX request
            selectedRows.forEach(checkbox => {
                const row = checkbox.closest('tr');
                row.style.opacity = '0';
                row.style.transform = 'translateX(-100%)';
                setTimeout(() => row.remove(), 300);
            });
            
            this.updateBulkActions();
        }
    }

    handleBulkExport() {
        const selectedRows = document.querySelectorAll('.select-row:checked');
        if (selectedRows.length === 0) return;

        // This would typically generate and download a file
        console.log(`Exporting ${selectedRows.length} items...`);
    }

    addScrollIndicators() {
        const tableContainers = document.querySelectorAll('.table-content');
        
        tableContainers.forEach(container => {
            if (container.scrollWidth > container.clientWidth) {
                container.classList.add('table-responsive');
            }
        });
    }

    setupResponsiveTables() {
        this.updateResponsiveTables();
    }

    updateResponsiveTables() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            const container = table.closest('.table-content');
            if (!container) return;

            if (!this.isDesktop) {
                container.style.overflowX = 'auto';
                table.style.minWidth = '800px';
            } else {
                container.style.overflowX = 'visible';
                table.style.minWidth = 'auto';
            }
        });
    }

    setupResponsiveForms() {
        // Add responsive classes to forms
        document.querySelectorAll('.form-grid').forEach(grid => {
            if (!this.isDesktop) {
                grid.style.gridTemplateColumns = '1fr';
            }
        });
    }

    updateResponsiveForms() {
        document.querySelectorAll('.form-grid').forEach(grid => {
            if (!this.isDesktop) {
                grid.style.gridTemplateColumns = '1fr';
            } else {
                grid.style.gridTemplateColumns = '';
            }
        });
    }

    initModals() {
        // Enhanced modal handling
        document.querySelectorAll('.modal').forEach(modal => {
            const closeBtn = modal.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeModal(modal));
            }
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal);
                }
            });
        });

        // ESC key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal[style*="block"]');
                if (openModal) {
                    this.closeModal(openModal);
                }
            }
        });
    }

    closeModal(modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
            modal.style.opacity = '';
            modal.style.transform = '';
        }, 200);
    }

    initFileUploads() {
        // Enhanced file upload handling
        document.querySelectorAll('.file-upload-area').forEach(area => {
            const input = area.querySelector('input[type="file"]');
            if (!input) return;

            // Drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, this.preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                area.addEventListener(eventName, () => area.classList.add('drag-over'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, () => area.classList.remove('drag-over'), false);
            });

            area.addEventListener('drop', (e) => this.handleDrop(e, input), false);

            // File selection
            input.addEventListener('change', () => this.handleFileSelect(input));
        });
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    handleDrop(e, input) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        input.files = files;
        this.handleFileSelect(input);
    }

    handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;

        // Validate file
        if (!this.validateFile(file)) {
            input.value = '';
            return;
        }

        // Preview image
        if (file.type.startsWith('image/')) {
            this.previewImage(file, input);
        }

        // Show file info
        this.showFileInfo(file, input);
    }

    validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (file.size > maxSize) {
            alert('File terlalu besar. Maksimal 5MB.');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
            return false;
        }

        return true;
    }

    previewImage(file, input) {
        const reader = new FileReader();
        const uploadArea = input.closest('.file-upload-area');
        
        reader.onload = (e) => {
            let preview = uploadArea.querySelector('.image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'image-preview';
                uploadArea.appendChild(preview);
            }
            
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-image">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            uploadArea.querySelector('.file-upload-content').style.display = 'none';
            
            // Remove image handler
            preview.querySelector('.remove-image').addEventListener('click', () => {
                input.value = '';
                preview.remove();
                uploadArea.querySelector('.file-upload-content').style.display = 'block';
            });
        };
        
        reader.readAsDataURL(file);
    }

    showFileInfo(file, input) {
        const uploadArea = input.closest('.file-upload-area');
        let fileInfo = uploadArea.querySelector('.file-info');
        
        if (!fileInfo) {
            fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            fileInfo.style.cssText = `
                margin-top: 1rem;
                padding: 0.75rem;
                background: var(--gray-50);
                border-radius: var(--border-radius-sm);
                font-size: 0.875rem;
            `;
            uploadArea.appendChild(fileInfo);
        }
        
        fileInfo.innerHTML = `
            <div><strong>File:</strong> ${file.name}</div>
            <div><strong>Ukuran:</strong> ${this.formatFileSize(file.size)}</div>
            <div><strong>Tipe:</strong> ${file.type}</div>
        `;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Initialize admin panel when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminPanel();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .drag-over {
        border-color: var(--primary-color) !important;
        background: rgba(46, 139, 87, 0.05) !important;
    }
    
    .select-column {
        width: 40px;
        text-align: center;
    }
    
    .bulk-actions {
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .sort-indicator {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }
    
    th:hover .sort-indicator {
        opacity: 1;
    }
    
    .file-info {
        animation: fadeIn 0.3s ease-out;
    }
    
    .image-preview {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
// ===== ENHANCED MOBILE MENU FUNCTIONALITY =====
class MobileMenuManager {
    constructor() {
        this.sidebar = document.getElementById('adminSidebar');
        this.mobileToggle = document.querySelector('.mobile-menu-toggle');
        this.overlay = null;
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        if (!this.sidebar || !this.mobileToggle) return;
        
        // Create overlay
        this.createOverlay();
        
        // Bind events
        this.bindEvents();
        
        // Set initial ARIA attributes
        this.setAriaAttributes();
        
        // Handle window resize
        this.handleResize();
    }
    
    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'sidebar-overlay';
        document.body.appendChild(this.overlay);
    }
    
    bindEvents() {
        // Mobile toggle click
        this.mobileToggle.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });
        
        // Overlay click
        this.overlay.addEventListener('click', () => {
            this.close();
        });
        
        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        // Sidebar links click (close menu on mobile)
        const sidebarLinks = this.sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    this.close();
                }
            });
        });
    }
    
    setAriaAttributes() {
        this.mobileToggle.setAttribute('aria-expanded', 'false');
        this.mobileToggle.setAttribute('aria-controls', 'adminSidebar');
        this.mobileToggle.setAttribute('aria-label', 'Toggle navigation menu');
        this.sidebar.setAttribute('aria-hidden', 'true');
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        this.isOpen = true;
        this.sidebar.classList.add('active');
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Update ARIA attributes
        this.mobileToggle.setAttribute('aria-expanded', 'true');
        this.sidebar.setAttribute('aria-hidden', 'false');
        
        // Focus first link for accessibility
        const firstLink = this.sidebar.querySelector('a');
        if (firstLink) {
            setTimeout(() => firstLink.focus(), 100);
        }
    }
    
    close() {
        this.isOpen = false;
        this.sidebar.classList.remove('active');
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Update ARIA attributes
        this.mobileToggle.setAttribute('aria-expanded', 'false');
        this.sidebar.setAttribute('aria-hidden', 'true');
    }
    
    handleResize() {
        if (window.innerWidth > 768) {
            this.close();
        }
    }
}

// ===== ENHANCED TABLE RESPONSIVENESS =====
class ResponsiveTableManager {
    constructor() {
        this.tables = document.querySelectorAll('table');
        this.init();
    }
    
    init() {
        this.tables.forEach(table => {
            this.makeTableResponsive(table);
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    }
    
    makeTableResponsive(table) {
        const container = table.closest('.table-content');
        if (!container) return;
        
        // Add scroll indicators
        this.addScrollIndicators(container);
        
        // Handle scroll events
        container.addEventListener('scroll', () => {
            this.updateScrollIndicators(container);
        });
    }
    
    addScrollIndicators(container) {
        // Left indicator
        const leftIndicator = document.createElement('div');
        leftIndicator.className = 'scroll-indicator scroll-indicator-left';
        leftIndicator.innerHTML = '<i class="fas fa-chevron-left"></i>';
        
        // Right indicator
        const rightIndicator = document.createElement('div');
        rightIndicator.className = 'scroll-indicator scroll-indicator-right';
        rightIndicator.innerHTML = '<i class="fas fa-chevron-right"></i>';
        
        container.style.position = 'relative';
        container.appendChild(leftIndicator);
        container.appendChild(rightIndicator);
        
        // Initial state
        this.updateScrollIndicators(container);
    }
    
    updateScrollIndicators(container) {
        const leftIndicator = container.querySelector('.scroll-indicator-left');
        const rightIndicator = container.querySelector('.scroll-indicator-right');
        
        if (!leftIndicator || !rightIndicator) return;
        
        const { scrollLeft, scrollWidth, clientWidth } = container;
        
        // Show/hide left indicator
        leftIndicator.style.opacity = scrollLeft > 0 ? '1' : '0';
        
        // Show/hide right indicator
        rightIndicator.style.opacity = scrollLeft < scrollWidth - clientWidth ? '1' : '0';
    }
    
    handleResize() {
        this.tables.forEach(table => {
            const container = table.closest('.table-content');
            if (container) {
                this.updateScrollIndicators(container);
            }
        });
    }
}

// ===== ENHANCED FORM VALIDATION =====
class FormValidationManager {
    constructor() {
        this.forms = document.querySelectorAll('form');
        this.init();
    }
    
    init() {
        this.forms.forEach(form => {
            this.enhanceForm(form);
        });
    }
    
    enhanceForm(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
            
            // Touch-friendly improvements
            if (input.type === 'file') {
                this.enhanceFileInput(input);
            }
        });
        
        // Form submission
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
            }
        });
    }
    
    validateField(field) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return true;
        
        let isValid = true;
        let errorMessage = '';
        
        // Required validation
        if (field.hasAttribute('required') && !field.value.trim()) {
            isValid = false;
            errorMessage = 'Field ini wajib diisi';
        }
        
        // Email validation
        if (field.type === 'email' && field.value && !this.isValidEmail(field.value)) {
            isValid = false;
            errorMessage = 'Format email tidak valid';
        }
        
        // File validation
        if (field.type === 'file' && field.files.length > 0) {
            const file = field.files[0];
            if (!this.isValidFile(file)) {
                isValid = false;
                errorMessage = 'File tidak valid';
            }
        }
        
        // Update UI
        formGroup.classList.toggle('error', !isValid);
        formGroup.classList.toggle('success', isValid && field.value.trim());
        
        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }
        
        return isValid;
    }
    
    validateForm(form) {
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        errorElement.style.cssText = `
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        `;
        
        field.parentNode.appendChild(errorElement);
    }
    
    clearFieldError(field) {
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.classList.remove('error');
            const errorElement = formGroup.querySelector('.field-error');
            if (errorElement) {
                errorElement.remove();
            }
        }
    }
    
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    isValidFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        return file.size <= maxSize && allowedTypes.includes(file.type);
    }
    
    enhanceFileInput(input) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;
        
        // Create drag and drop area
        const dropArea = document.createElement('div');
        dropArea.className = 'file-drop-area';
        dropArea.innerHTML = `
            <div class="file-drop-content">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag & drop file atau klik untuk memilih</p>
                <small>Maksimal 5MB, format: JPG, PNG, GIF, WebP</small>
            </div>
        `;
        
        // Style the drop area
        dropArea.style.cssText = `
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        `;
        
        // Insert after input
        input.style.display = 'none';
        input.parentNode.insertBefore(dropArea, input.nextSibling);
        
        // Handle events
        dropArea.addEventListener('click', () => input.click());
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.style.borderColor = '#2e8b57';
            dropArea.style.backgroundColor = 'rgba(46, 139, 87, 0.05)';
        });
        dropArea.addEventListener('dragleave', () => {
            dropArea.style.borderColor = '#d1d5db';
            dropArea.style.backgroundColor = 'transparent';
        });
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.style.borderColor = '#d1d5db';
            dropArea.style.backgroundColor = 'transparent';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                this.showFilePreview(input, files[0]);
            }
        });
        
        // Handle file selection
        input.addEventListener('change', () => {
            if (input.files.length > 0) {
                this.showFilePreview(input, input.files[0]);
            }
        });
    }
    
    showFilePreview(input, file) {
        const dropArea = input.parentNode.querySelector('.file-drop-area');
        if (!dropArea) return;
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                dropArea.innerHTML = `
                    <div class="file-preview">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                        <p style="margin-top: 0.5rem; font-weight: 500;">${file.name}</p>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.file-drop-area').parentNode.querySelector('input[type=file]').value=''; this.closest('.file-drop-area').innerHTML=this.closest('.file-drop-area').dataset.original;">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            dropArea.innerHTML = `
                <div class="file-preview">
                    <i class="fas fa-file" style="font-size: 3rem; color: #6b7280; margin-bottom: 1rem;"></i>
                    <p style="font-weight: 500;">${file.name}</p>
                    <p style="color: #6b7280; font-size: 0.875rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.file-drop-area').parentNode.querySelector('input[type=file]').value=''; this.closest('.file-drop-area').innerHTML=this.closest('.file-drop-area').dataset.original;">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
            `;
        }
    }
}

// ===== ENHANCED LOADING STATES =====
class LoadingStateManager {
    constructor() {
        this.init();
    }
    
    init() {
        // Auto-hide alerts
        this.handleAlerts();
        
        // Button loading states
        this.handleButtonLoading();
        
        // Form submission loading
        this.handleFormLoading();
    }
    
    handleAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            // Add close button
            if (!alert.querySelector('.alert-close')) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'alert-close';
                closeBtn.innerHTML = '&times;';
                closeBtn.style.cssText = `
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    padding: 0;
                    margin-left: auto;
                    color: inherit;
                    opacity: 0.7;
                    transition: opacity 0.2s ease;
                `;
                closeBtn.addEventListener('click', () => this.hideAlert(alert));
                closeBtn.addEventListener('mouseenter', () => closeBtn.style.opacity = '1');
                closeBtn.addEventListener('mouseleave', () => closeBtn.style.opacity = '0.7');
                alert.appendChild(closeBtn);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(() => this.hideAlert(alert), 5000);
        });
    }
    
    hideAlert(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }
    
    handleButtonLoading() {
        document.querySelectorAll('.btn').forEach(btn => {
            if (btn.type === 'submit' || btn.classList.contains('btn-primary')) {
                btn.addEventListener('click', () => {
                    if (!btn.classList.contains('btn-delete')) {
                        this.setButtonLoading(btn, true);
                        
                        // Auto-remove loading after 3 seconds (fallback)
                        setTimeout(() => {
                            this.setButtonLoading(btn, false);
                        }, 3000);
                    }
                });
            }
        });
    }
    
    handleFormLoading() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    this.setButtonLoading(submitBtn, true);
                }
            });
        });
    }
    
    setButtonLoading(btn, loading) {
        if (loading) {
            btn.classList.add('loading');
            btn.disabled = true;
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        } else {
            btn.classList.remove('loading');
            btn.disabled = false;
            if (btn.dataset.originalText) {
                btn.innerHTML = btn.dataset.originalText;
            }
        }
    }
}

// ===== INITIALIZE ALL MANAGERS =====
document.addEventListener('DOMContentLoaded', () => {
    // Initialize existing AdminPanel
    if (typeof AdminPanel !== 'undefined') {
        new AdminPanel();
    }
    
    // Initialize new managers
    new MobileMenuManager();
    new ResponsiveTableManager();
    new FormValidationManager();
    new LoadingStateManager();
});

// ===== UTILITY FUNCTIONS =====
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar && overlay) {
        const isActive = sidebar.classList.contains('active');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        if (!isActive) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Global function for delete confirmation
function confirmDelete(id, name) {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
        modal.style.display = 'block';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside
window.addEventListener('click', (event) => {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Add CSS for scroll indicators
const scrollIndicatorStyles = `
<style>
.scroll-indicator {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 30px;
    height: 30px;
    background: rgba(46, 139, 87, 0.9);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
}

.scroll-indicator-left {
    left: 10px;
}

.scroll-indicator-right {
    right: 10px;
}

@media (max-width: 768px) {
    .scroll-indicator {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }
}
</style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', scrollIndicatorStyles);