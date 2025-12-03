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

    // Fade out
    tipText.style.opacity = '0';
    tipText.style.transform = 'translateY(10px)';
    
    setTimeout(async () => {
        try {
            const res = await fetch('../api/motivation.php');
            const data = await res.json();
            tipText.textContent = data.tip;
            
            // Fade in
            setTimeout(() => {
                tipText.style.opacity = '1';
                tipText.style.transform = 'translateY(0)';
            }, 50);
        } catch (err) {
            tipText.textContent = 'Failed to load tip. Try again.';
            tipText.style.opacity = '1';
            tipText.style.transform = 'translateY(0)';
        }
    }, 300);
}

async function toggleStatus(resourceId) {
    console.log('toggleStatus called with ID:', resourceId);
    
    try {
        const res = await fetch('../api/toggle_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: resourceId })
        });

        console.log('Response status:', res.status);
        const data = await res.json();
        console.log('Response data:', data);
        
        if (!data.success) {
            alert(data.error || 'Failed to toggle status.');
            return;
        }

        // Update the badge in the corresponding row
        const row = document.querySelector(`tr[data-id="${resourceId}"]`);
        console.log('Found row:', row);
        
        if (!row) {
            console.error('Row not found for ID:', resourceId);
            return;
        }

        const badge = row.querySelector('td:nth-child(6) .badge');
        console.log('Found badge:', badge);
        
        if (!badge) {
            console.error('Badge not found in row');
            return;
        }

        const newStatus = data.new_status;
        badge.textContent = newStatus.replace('_', ' ');

        // Update badge color
        badge.classList.remove('bg-success', 'bg-warning', 'bg-secondary');

        if (newStatus === 'done') {
            badge.classList.add('bg-success');
        } else if (newStatus === 'in_progress') {
            badge.classList.add('bg-warning');
        } else {
            badge.classList.add('bg-secondary');
        }
        
        console.log('Status updated successfully to:', newStatus);
    } catch (err) {
        console.error('Toggle status error:', err);
        alert('Error talking to the server: ' + err.message);
    }
}

async function loadTemplate(templateName) {
    try {
        const res = await fetch(`templates.php?ajax=1&template=${templateName}`);
        if (!res.ok) {
            throw new Error('Template not found');
        }
        const template = await res.json();
        
        document.querySelector('input[name="title"]').value = template.title;
        document.querySelector('input[name="subject"]').value = template.subject;
        document.querySelector('select[name="resource_type"]').value = template.resource_type;
        document.querySelector('textarea[name="notes"]').value = template.notes;
        
        // Scroll to form
        document.querySelector('input[name="title"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Focus on title for customization
        setTimeout(() => {
            document.querySelector('input[name="title"]').focus();
            document.querySelector('input[name="title"]').select();
        }, 300);
    } catch (err) {
        console.error('Failed to load template:', err);
        alert('Failed to load template. Please try again.');
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
        const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const subject = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const type = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(6) .badge').textContent.toLowerCase();
        
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
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            console.log('Toggle button clicked, ID:', id);
            if (id) {
                toggleStatus(id);
            } else {
                console.error('No data-id found on toggle button');
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

    // Checkbox change listener for bulk actions
    document.body.addEventListener('change', (e) => {
        if (e.target.classList.contains('resource-checkbox') || e.target.id === 'selectAll') {
            updateBulkActions();
        }
    });

    // Keyboard shortcuts (non-conflicting only)
    document.addEventListener('keydown', (e) => {
        // Escape: Clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('search-input');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                filterResources();
                searchInput.blur();
            }
        }
        
        // Forward slash (/): Focus on search (like GitHub, Reddit)
        if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
            const activeElement = document.activeElement;
            // Only if not already in an input
            if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const searchInput = document.getElementById('search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        }
    });
});


// Bulk Actions - Consolidated
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
    const selectAll = document.getElementById('selectAll');
    
    if (count > 0) {
        if (bulkBar) bulkBar.classList.remove('d-none');
        if (countSpan) countSpan.textContent = count;
    } else {
        if (bulkBar) bulkBar.classList.add('d-none');
        if (selectAll) selectAll.checked = false;
    }
}

async function bulkUpdateStatus(newStatus) {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    if (ids.length === 0) {
        alert('Please select at least one resource');
        return;
    }
    
    if (!confirm(`Update ${ids.length} resource${ids.length > 1 ? 's' : ''} to "${newStatus}"?`)) return;
    
    try {
        const res = await fetch('../api/bulk_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'change_status', ids, status: newStatus })
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
    const ids = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    if (ids.length === 0) {
        alert('Please select at least one resource');
        return;
    }
    
    if (!confirm(`Delete ${ids.length} resource${ids.length > 1 ? 's' : ''}? This cannot be undone!`)) return;
    
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

function showBulkTagModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkTagModal'));
    modal.show();
}

async function bulkAddTags() {
    const checkboxes = document.querySelectorAll('.resource-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => parseInt(cb.value));
    const tagsInput = document.getElementById('bulk-tags-input');
    
    if (ids.length === 0) {
        alert('Please select at least one resource');
        return;
    }
    
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
                ids: ids,
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
