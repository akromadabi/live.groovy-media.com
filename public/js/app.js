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
