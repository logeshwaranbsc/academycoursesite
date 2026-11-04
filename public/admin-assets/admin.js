/**
 * Meridian Academy — Admin Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Modal Helper ──
    window.openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.show').forEach(m => {
                m.classList.remove('show');
                document.body.style.overflow = '';
            });
        }
    });

    // ── 2. Image Input Preview ──
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', (e) => {
            const previewId = input.getAttribute('data-preview');
            const previewEl = document.getElementById(previewId);
            if (previewEl && input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (re) => {
                    previewEl.src = re.target.result;
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });

    // ── 3. Toggle Status (AJAX) ──
    document.querySelectorAll('.btn-toggle-status').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = btn.getAttribute('data-url');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok && data.status) {
                    const badge = btn.querySelector('.status-badge');
                    if (badge) {
                        if (data.status === 'active') {
                            badge.className = 'status-badge badge-green';
                            badge.textContent = 'Active';
                        } else {
                            badge.className = 'status-badge badge-red';
                            badge.textContent = 'Inactive';
                        }
                    }
                    showToast(`Status changed to ${data.status}`, 'success');
                } else {
                    showToast(data.error || 'Failed to toggle status.', 'error');
                }
            } catch (err) {
                showToast('Network error while toggling status.', 'error');
            }
        });
    });

    // ── 4. Delete Confirmation Modal Helper ──
    window.confirmDelete = (formAction, itemName = 'this item') => {
        const modal = document.getElementById('delete-confirm-modal');
        const form = document.getElementById('delete-confirm-form');
        const text = document.getElementById('delete-item-name');

        if (form) form.action = formAction;
        if (text) text.textContent = itemName;
        if (modal) openModal('delete-confirm-modal');
    };
});
