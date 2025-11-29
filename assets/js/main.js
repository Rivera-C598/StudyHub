// assets/js/main.js

// Theme Management
function toggleTheme() {
    const body = document.body;
    const themeIcon = document.getElementById('theme-icon');
    
    if (body.classList.contains('light-theme')) {
        body.classList.remove('light-theme');
        localStorage.setItem('theme', 'dark');
        if (themeIcon) themeIcon.textContent = '🌙';
    } else {
        body.classList.add('light-theme');
        localStorage.setItem('theme', 'light');
        if (themeIcon) themeIcon.textContent = '☀️';
    }
}

function loadTheme() {
    const savedTheme = localStorage.getItem('theme');
    const themeIcon = document.getElementById('theme-icon');
    
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
        if (themeIcon) themeIcon.textContent = '☀️';
    }
}

// Load theme on page load
loadTheme();

async function loadStudyTip() {
    const tipText = document.getElementById('study-tip-text');
    if (!tipText) return;

    tipText.textContent = 'Loading tip...';
    try {
        const res = await fetch('../api/motivation.php');
        const data = await res.json();
        tipText.textContent = data.tip;
    } catch (err) {
        tipText.textContent = 'Failed to load tip. Try again.';
    }
}

async function toggleStatus(resourceId) {
    try {
        const res = await fetch('../api/toggle_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: resourceId })
        });

        const data = await res.json();
        if (!data.success) {
            alert(data.error || 'Failed to toggle status.');
            return;
        }

        // Update the badge in the corresponding row
        const row = document.querySelector(`tr[data-id="${resourceId}"]`);
        if (!row) return;

        const badge = row.querySelector('td:nth-child(4) .badge');
        if (!badge) return;

        const newStatus = data.new_status;
        badge.textContent = newStatus;

        // Update badge color
        badge.classList.remove('bg-success', 'bg-warning', 'bg-secondary');

        if (newStatus === 'done') {
            badge.classList.add('bg-success');
        } else if (newStatus === 'in_progress') {
            badge.classList.add('bg-warning');
        } else {
            badge.classList.add('bg-secondary');
        }
    } catch (err) {
        console.error(err);
        alert('Error talking to the server.');
    }
}

async function loadTemplate(templateName) {
    try {
        const res = await fetch(`templates.php?template=${templateName}`);
        const template = await res.json();
        
        document.querySelector('input[name="title"]').value = template.title;
        document.querySelector('input[name="subject"]').value = template.subject;
        document.querySelector('select[name="resource_type"]').value = template.resource_type;
        document.querySelector('textarea[name="notes"]').value = template.notes;
        
        // Focus on title for customization
        document.querySelector('input[name="title"]').focus();
        document.querySelector('input[name="title"]').select();
    } catch (err) {
        console.error('Failed to load template:', err);
    }
}

function showNotesModal(resource) {
    document.getElementById('modalTitle').textContent = resource.title;
    document.getElementById('modalSubject').textContent = resource.subject;
    document.getElementById('modalType').textContent = resource.resource_type;
    document.getElementById('modalNotes').textContent = resource.notes || 'No notes available';
    
    const urlContainer = document.getElementById('modalUrlContainer');
    const urlLink = document.getElementById('modalUrl');
    if (resource.url) {
        urlLink.href = resource.url;
        urlLink.textContent = resource.url;
        urlContainer.style.display = 'block';
    } else {
        urlContainer.style.display = 'none';
    }
    
    document.getElementById('modalEditBtn').href = `dashboard.php?edit=${resource.id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('notesModal'));
    modal.show();
}

function filterResources() {
    const searchInput = document.getElementById('search-input');
    const filterType = document.getElementById('filter-type');
    const filterStatus = document.getElementById('filter-status');
    
    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase();
    const typeFilter = filterType ? filterType.value : '';
    const statusFilter = filterStatus ? filterStatus.value : '';
    
    const rows = document.querySelectorAll('tbody tr[data-id]');
    
    rows.forEach(row => {
        const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        const subject = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const type = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(4) .badge').textContent.toLowerCase();
        
        const matchesSearch = title.includes(searchTerm) || subject.includes(searchTerm);
        const matchesType = !typeFilter || type === typeFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesSearch && matchesType && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Study tips
    loadStudyTip();
    const tipBtn = document.getElementById('refresh-tip-btn');
    if (tipBtn) {
        tipBtn.addEventListener('click', loadStudyTip);
    }

    // Toggle status buttons
    document.body.addEventListener('click', (e) => {
        if (e.target.classList.contains('toggle-status-btn')) {
            const id = e.target.getAttribute('data-id');
            if (id) {
                toggleStatus(id);
            }
        }
    });

    // Search and filter
    const searchInput = document.getElementById('search-input');
    const filterType = document.getElementById('filter-type');
    const filterStatus = document.getElementById('filter-status');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterResources);
    }
    if (filterType) {
        filterType.addEventListener('change', filterResources);
    }
    if (filterStatus) {
        filterStatus.addEventListener('change', filterResources);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl+N: Focus on title input (new resource)
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const titleInput = document.querySelector('input[name="title"]');
            if (titleInput) titleInput.focus();
        }
        
        // Ctrl+F: Focus on search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('search-input');
            if (searchInput) searchInput.focus();
        }
        
        // Ctrl+S: Submit form (if focused on form)
        if (e.ctrlKey && e.key === 's') {
            const activeElement = document.activeElement;
            const form = activeElement?.closest('form');
            if (form) {
                e.preventDefault();
                form.requestSubmit();
            }
        }
        
        // Escape: Clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('search-input');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                filterResources();
            }
        }
    });
});


// Bulk Actions
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.resource-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    const count = checkboxes.length;
    const bulkBar = document.getElementById('bulkActionsBar');
    const countSpan = document.getElementById('selectedCount');
    
    if (count > 0) {
        bulkBar.classList.remove('d-none');
        countSpan.textContent = count;
    } else {
        bulkBar.classList.add('d-none');
        document.getElementById('selectAll').checked = false;
    }
}

async function bulkUpdateStatus(newStatus) {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (!confirm(`Update ${ids.length} resources to "${newStatus}"?`)) return;
    
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_status', ids, status: newStatus })
        });
        
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to update resources');
        }
    } catch (err) {
        console.error(err);
        alert('Error updating resources');
    }
}

async function bulkDelete() {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (!confirm(`Delete ${ids.length} resources? This cannot be undone!`)) return;
    
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', ids })
        });
        
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to delete resources');
        }
    } catch (err) {
        console.error(err);
        alert('Error deleting resources');
    }
}


// Bulk actions
let selectedResources = [];

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.resource-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });
    
    updateSelectedResources();
}

function updateSelectedResources() {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    selectedResources = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    const bulkActions = document.getElementById('bulk-actions');
    if (bulkActions) {
        bulkActions.style.display = selectedResources.length > 0 ? 'block' : 'none';
    }
    
    const selectedCount = document.getElementById('selected-count');
    if (selectedCount) {
        selectedCount.textContent = selectedResources.length;
    }
}

async function bulkDelete() {
    if (!confirm(`Delete ${selectedResources.length} resources?`)) return;
    
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete',
                ids: selectedResources
            })
        });
        
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to delete resources');
        }
    } catch (err) {
        console.error(err);
        alert('Error communicating with server');
    }
}

async function bulkChangeStatus(status) {
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'change_status',
                ids: selectedResources,
                status: status
            })
        });
        
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to update resources');
        }
    } catch (err) {
        console.error(err);
        alert('Error communicating with server');
    }
}

function showBulkTagModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkTagModal'));
    modal.show();
}

async function bulkAddTags() {
    const tagsInput = document.getElementById('bulk-tags-input');
    const tags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
    
    if (tags.length === 0) {
        alert('Please enter at least one tag');
        return;
    }
    
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'add_tags',
                ids: selectedResources,
                tags: tags
            })
        });
        
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to add tags');
        }
    } catch (err) {
        console.error(err);
        alert('Error communicating with server');
    }
}

// Update event listener for checkboxes
document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('change', (e) => {
        if (e.target.classList.contains('resource-checkbox')) {
            updateSelectedResources();
        }
    });
});
