/**
 * TikTok Live Manager - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize dark mode
    initTheme();

    // Initialize mobile menu
    initMobileMenu();

    // Initialize dropdowns
    initDropdowns();

    // Initialize modals
    initModals();

    // Initialize alerts auto-dismiss
    initAlerts();

    // Initialize table sorting
    initTableSorting();
});

/**
 * Theme Management
 */
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

function updateThemeIcon(theme) {
    const toggleBtns = document.querySelectorAll('.theme-toggle');
    toggleBtns.forEach(btn => {
        if (theme === 'dark') {
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>`;
        } else {
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>`;
        }
    });
}

/**
 * Mobile Menu
 */
function initMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay?.classList.toggle('active');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
}

/**
 * Dropdowns
 */
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (trigger && menu) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.classList.toggle('active');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
            menu.classList.remove('active');
        });
    });
}

/**
 * Modals
 */
function initModals() {
    // Open modal buttons
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.getAttribute('data-modal');
            openModal(modalId);
        });
    });

    // Close buttons
    document.querySelectorAll('.modal-close, [data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const overlay = btn.closest('.modal-overlay');
            closeModal(overlay);
        });
    });

    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(overlay);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(overlay) {
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Alerts
 */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

/**
 * Form Helpers
 */
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = value;
    }
}

function parseCurrency(value) {
    return parseInt(value.replace(/[^\d]/g, '')) || 0;
}

/**
 * Confirmation Dialog
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Form Submission with Loading
 */
function submitForm(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="18" height="18">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg> Memproses...`;

        // Re-enable after form submission (fallback)
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, 10000);
    }
    return true;
}

/**
 * Delete confirmation
 */
function confirmDelete(formId) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById(formId).submit();
    }
}

/* Spin animation for loading */
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
`;
document.head.appendChild(style);

/**
 * Admin FAB Menu Toggle
 */
function toggleAdminMenu() {
    const menu = document.getElementById('admin-fab-menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Close admin fab menu when clicking outside
document.addEventListener('click', function (e) {
    const fabTrigger = document.getElementById('admin-fab-trigger');
    const fabMenu = document.getElementById('admin-fab-menu');

    if (fabTrigger && fabMenu && !fabTrigger.contains(e.target)) {
        fabMenu.classList.remove('active');
    }
});

/**
 * Password Visibility Toggle
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
    } else {
        passwordInput.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
    }
}

/**
 * Table Sorting
 */
function initTableSorting() {
    const tables = document.querySelectorAll('table.table');
    
    // Inject CSS styles for sortable headers
    const css = `
        .sortable-header {
            cursor: pointer !important;
            position: relative !important;
            padding-right: 24px !important;
            user-select: none !important;
            transition: background-color 0.2s ease !important;
        }
        .sortable-header:hover {
            background-color: rgba(99, 102, 241, 0.08) !important;
        }
        .sortable-header::after {
            content: ' ↕';
            position: absolute;
            right: 8px;
            opacity: 0.35;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .sortable-header.asc::after {
            content: ' ▲';
            opacity: 1;
            color: var(--primary);
        }
        .sortable-header.desc::after {
            content: ' ▼';
            opacity: 1;
            color: var(--primary);
        }
    `;
    const styleEl = document.createElement('style');
    styleEl.textContent = css;
    document.head.appendChild(styleEl);

    tables.forEach(table => {
        const headers = table.querySelectorAll('thead th');
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        headers.forEach((th, index) => {
            const text = th.textContent.trim().toLowerCase();
            const hasCheckbox = th.querySelector('input[type="checkbox"]');
            const hasSortableLink = th.querySelector('.sortable-link');

            // Skip checkboxes, Actions, Aksi, empty headers, or headers with existing server sorting links
            if (hasCheckbox || hasSortableLink || !text || text === 'aksi' || text === 'action') {
                return;
            }

            th.classList.add('sortable-header');
            let direction = 'desc';

            th.addEventListener('click', () => {
                // Remove active classes from other headers in the same table
                headers.forEach((h, i) => {
                    if (i !== index) {
                        h.classList.remove('asc', 'desc');
                    }
                });

                // Toggle direction
                if (th.classList.contains('desc')) {
                    direction = 'asc';
                    th.classList.remove('desc');
                    th.classList.add('asc');
                } else {
                    direction = 'desc';
                    th.classList.remove('asc');
                    th.classList.add('desc');
                }

                const rows = Array.from(tbody.querySelectorAll('tr'));
                // Filter out empty placeholder rows
                if (rows.length <= 1 && rows[0]?.querySelector('td[colspan]')) {
                    return;
                }

                const sortedRows = rows.sort((a, b) => {
                    const cellA = a.children[index]?.textContent || '';
                    const cellB = b.children[index]?.textContent || '';

                    const valA = parseCellValue(cellA);
                    const valB = parseCellValue(cellB);

                    if (typeof valA === 'number' && typeof valB === 'number') {
                        return direction === 'asc' ? valA - valB : valB - valA;
                    }
                    
                    const strA = String(valA);
                    const strB = String(valB);
                    return direction === 'asc' 
                        ? strA.localeCompare(strB, undefined, { numeric: true, sensitivity: 'base' })
                        : strB.localeCompare(strA, undefined, { numeric: true, sensitivity: 'base' });
                });

                sortedRows.forEach(row => tbody.appendChild(row));
            });
        });
    });
}

function parseCellValue(text) {
    text = text.trim();
    if (!text) return '';

    // Check if it's a duration (e.g. "2 jam 30 menit" or "2 jam 0 menit")
    if (text.includes('jam') || text.includes('menit')) {
        const jamMatch = text.match(/(\d+)\s*jam/i);
        const menitMatch = text.match(/(\d+)\s*menit/i);
        let totalMinutes = 0;
        if (jamMatch) totalMinutes += parseInt(jamMatch[1]) * 60;
        if (menitMatch) totalMinutes += parseInt(menitMatch[1]);
        return totalMinutes;
    }

    // Check if it's currency (e.g. "Rp 45.000") or formatted number (e.g. "1.234")
    if (text.includes('Rp') || /^\d+(\.\d{3})+$/.test(text)) {
        const cleanText = text.replace(/Rp|\./g, '').trim();
        const num = parseFloat(cleanText);
        if (!isNaN(num)) return num;
    }

    // Check if it's a standard simple number
    const simpleNum = parseFloat(text);
    if (!isNaN(simpleNum) && String(simpleNum) === text) {
        return simpleNum;
    }

    // Check if it's a date (e.g., "15 Jun 2026" or "15 Jun 2026 18:00" or Indonesian months)
    const indonesianMonths = {
        'jan': 0, 'feb': 1, 'mar': 2, 'apr': 3, 'mei': 4, 'jun': 5,
        'jul': 6, 'agu': 7, 'sep': 8, 'okt': 9, 'nov': 10, 'des': 11,
        'januari': 0, 'februari': 1, 'maret': 2, 'april': 3, 'mei': 4, 'juni': 5,
        'juli': 6, 'agustus': 7, 'september': 8, 'oktober': 9, 'november': 10, 'desember': 11
    };
    
    const cleanDateText = text.replace(/\s+/g, ' ');
    const parts = cleanDateText.split(' ');
    if (parts.length >= 3) {
        const day = parseInt(parts[0]);
        const monthStr = parts[1].toLowerCase();
        const year = parseInt(parts[2]);
        if (!isNaN(day) && !isNaN(year)) {
            let month = -1;
            if (indonesianMonths[monthStr] !== undefined) {
                month = indonesianMonths[monthStr];
            } else {
                const prefix = monthStr.substring(0, 3);
                if (indonesianMonths[prefix] !== undefined) {
                    month = indonesianMonths[prefix];
                }
            }

            if (month !== -1) {
                let date = new Date(year, month, day);
                if (parts[3]) {
                    const timeParts = parts[3].split(':');
                    if (timeParts.length >= 2) {
                        date.setHours(parseInt(timeParts[0]), parseInt(timeParts[1]));
                    }
                }
                return date.getTime();
            }
        }
    }

    const parsed = Date.parse(text);
    if (!isNaN(parsed)) return parsed;

    return text.toLowerCase();
}


/**
 * Smooth Page Transition Progress Bar & Fast AJAX Filter/Tab Navigation
 */
let __activeFilterAbortController = null;

function showProgressBar() {
    let progressBar = document.getElementById('page-progress-bar');
    if (!progressBar) return;
    progressBar.style.opacity = '1';
    progressBar.style.width = '40%';
    clearTimeout(progressBar.__t1);
    clearTimeout(progressBar.__t2);
    progressBar.__t1 = setTimeout(() => { progressBar.style.width = '75%'; }, 120);
    progressBar.__t2 = setTimeout(() => { progressBar.style.width = '90%'; }, 350);
}

function hideProgressBar() {
    let progressBar = document.getElementById('page-progress-bar');
    if (!progressBar) return;
    clearTimeout(progressBar.__t1);
    clearTimeout(progressBar.__t2);
    progressBar.style.width = '100%';
    setTimeout(() => {
        progressBar.style.opacity = '0';
        setTimeout(() => { progressBar.style.width = '0%'; }, 200);
    }, 80);
}

window.navigateFilterUrl = async function (url, pushHistory = true) {
    const currentMain = document.querySelector('main.page-content');
    if (!currentMain || !window.fetch || !window.DOMParser) {
        window.location.href = url;
        return;
    }

    if (__activeFilterAbortController) {
        __activeFilterAbortController.abort();
    }
    __activeFilterAbortController = new AbortController();

    showProgressBar();
    currentMain.classList.add('is-loading-content');

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html,application/xhtml+xml'
            },
            credentials: 'same-origin',
            signal: __activeFilterAbortController.signal
        });

        if (!response.ok || response.redirected && response.url.includes('/login')) {
            window.location.href = url;
            return;
        }

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(htmlText, 'text/html');
        const newMain = newDoc.querySelector('main.page-content');

        if (!newMain) {
            window.location.href = url;
            return;
        }

        // Destroy any existing Chart.js instances inside currentMain before swapping DOM
        if (window.Chart) {
            currentMain.querySelectorAll('canvas').forEach(canvas => {
                try {
                    const chart = Chart.getChart(canvas);
                    if (chart) chart.destroy();
                } catch (e) {}
            });
        }

        // Update page title & top header-left if changed
        if (newDoc.title) {
            document.title = newDoc.title;
        }
        const currentHeaderLeft = document.querySelector('.header .header-left');
        const newHeaderLeft = newDoc.querySelector('.header .header-left');
        if (currentHeaderLeft && newHeaderLeft) {
            currentHeaderLeft.innerHTML = newHeaderLeft.innerHTML;
        }

        // Swap main page content
        currentMain.innerHTML = newMain.innerHTML;
        currentMain.classList.remove('is-loading-content');
        document.body.style.overflow = '';

        // Update URL in browser address bar
        const finalUrl = response.url || url;
        if (pushHistory) {
            window.history.pushState({ ajaxSwap: true }, '', finalUrl);
        }

        // Collect inline scripts to execute (from newMain and pushed body scripts after .app-container)
        const scriptsToRun = [];
        newDoc.body.querySelectorAll('script:not([src])').forEach(scr => {
            const code = scr.textContent || '';
            if (code.includes('function confirmDelete(formId)')) {
                return; // Skip global layout script
            }
            scriptsToRun.push(code);
        });

        // Temporarily intercept document.addEventListener('DOMContentLoaded') so page scripts run immediately
        const origAddEventListener = document.addEventListener;
        const pendingDomReadyCallbacks = [];
        document.addEventListener = function (type, listener, options) {
            if (type === 'DOMContentLoaded' && typeof listener === 'function') {
                pendingDomReadyCallbacks.push(listener);
                return;
            }
            return origAddEventListener.call(document, type, listener, options);
        };

        try {
            scriptsToRun.forEach(code => {
                try {
                    const scriptEl = document.createElement('script');
                    scriptEl.textContent = code;
                    document.body.appendChild(scriptEl);
                    scriptEl.remove();
                } catch (err) {
                    console.error('Error executing page script:', err);
                }
            });
        } finally {
            document.addEventListener = origAddEventListener;
        }

        // Execute any captured DOMContentLoaded callbacks
        pendingDomReadyCallbacks.forEach(fn => {
            try {
                fn(new Event('DOMContentLoaded'));
            } catch (err) {
                console.error('Error in DOMContentLoaded callback:', err);
            }
        });

        // Re-initialize UI components inside the swapped main content
        initDropdowns();
        initModals();
        initAlerts();
        initTableSorting();
        hideProgressBar();
    } catch (err) {
        if (err && err.name === 'AbortError') {
            return; // Superseded by a newer filter selection
        }
        currentMain.classList.remove('is-loading-content');
        window.location.href = url;
    }
};

window.navigateFilterForm = function (form) {
    if (!form) return;
    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method !== 'GET') {
        form.submit();
        return;
    }
    const action = form.getAttribute('action') || window.location.pathname;
    const formData = new FormData(form);
    const params = new URLSearchParams();
    for (const [key, val] of formData.entries()) {
        if (val !== null && String(val) !== '') {
            params.append(key, String(val));
        }
    }
    const qs = params.toString();
    const targetUrl = action.split('?')[0] + (qs ? '?' + qs : '');
    window.navigateFilterUrl(targetUrl);
};

function initPageProgressBar() {
    let progressBar = document.getElementById('page-progress-bar');
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'page-progress-bar';
        document.body.prepend(progressBar);
    }

    // Inject styles
    if (!document.getElementById('page-progress-bar-style')) {
        const style = document.createElement('style');
        style.id = 'page-progress-bar-style';
        style.textContent = `
            #page-progress-bar {
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                background: linear-gradient(90deg, #6366f1, #818cf8, #a5b4fc);
                z-index: 999999;
                width: 0%;
                opacity: 0;
                transition: width 0.18s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease;
                pointer-events: none;
                box-shadow: 0 0 8px rgba(99, 102, 241, 0.6);
            }
            main.page-content.is-loading-content {
                opacity: 0.58;
                pointer-events: none;
                transition: opacity 0.12s ease;
            }
        `;
        document.head.appendChild(style);
    }

    // Intercept programmatic form.submit() for GET filter forms (e.g., onchange="this.form.submit()")
    if (!HTMLFormElement.prototype.__ajaxPatched) {
        const origSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function () {
            const method = (this.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET' && !this.hasAttribute('data-no-ajax') && this.getAttribute('target') !== '_blank') {
                window.navigateFilterForm(this);
                return;
            }
            showProgressBar();
            return origSubmit.call(this);
        };
        HTMLFormElement.prototype.__ajaxPatched = true;
    }

    // Intercept standard form submit events for GET filter forms
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET' && !form.hasAttribute('data-no-ajax') && form.getAttribute('target') !== '_blank') {
            e.preventDefault();
            window.navigateFilterForm(form);
        } else {
            showProgressBar();
        }
    });

    // Intercept same-page filter / reset / pagination link clicks for fast AJAX swap
    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.hasAttribute('download') || link.getAttribute('target') === '_blank' || link.hasAttribute('data-no-ajax')) {
            return;
        }

        if (link.origin === window.location.origin) {
            const isExportOrPrint = link.pathname.endsWith('/export') || link.pathname.endsWith('/print') || link.pathname.includes('/slip');
            if (isExportOrPrint) return;

            // If same pathname (e.g. changing month/term/status tab, reset filter, or pagination), swap via AJAX!
            if (link.pathname === window.location.pathname) {
                e.preventDefault();
                window.navigateFilterUrl(link.href);
                return;
            }

            // Cross-page navigation: show instant progress bar
            showProgressBar();
        }
    });

    // Handle browser back/forward button for AJAX-swapped filter states
    window.addEventListener('popstate', function () {
        window.navigateFilterUrl(window.location.href, false);
    });

    window.addEventListener('pageshow', function () {
        hideProgressBar();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageProgressBar);
} else {
    initPageProgressBar();
}
