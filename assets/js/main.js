// assets/js/main.js

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
});
