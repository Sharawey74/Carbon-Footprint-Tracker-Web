/**
 * Main JavaScript for Carbon Footprint Tracker
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Enhanced tab switching with animations
    const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener('shown.bs.tab', function(event) {
            const targetPane = document.querySelector(event.target.getAttribute('data-bs-target'));
            if (targetPane) {
                // Add entrance animation
                targetPane.style.opacity = '0';
                targetPane.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    targetPane.style.opacity = '1';
                    targetPane.style.transform = 'translateY(0)';
                }, 50);
            }
        });
        
        tabLink.addEventListener('hide.bs.tab', function(event) {
            const targetPane = document.querySelector(event.target.getAttribute('data-bs-target'));
            if (targetPane) {
                // Add exit animation
                targetPane.style.opacity = '0';
                targetPane.style.transform = 'translateY(20px)';
            }
        });
    });

    // Enhanced form validation with visual feedback
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Add validation styles on blur
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                    
                    // Create or update validation message
                    let feedback = this.nextElementSibling;
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        this.parentNode.insertBefore(feedback, this.nextSibling);
                    }
                    feedback.textContent = 'This field is required';
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    
                    // Remove validation message if exists
                    const feedback = this.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.remove();
                    }
                }
            });
            
            // Clear validation styles on focus
            input.addEventListener('focus', function() {
                this.classList.remove('is-invalid', 'is-valid');
            });
        });
        
        // Form submission validation
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                    
                    // Create validation message if not exists
                    let feedback = input.nextElementSibling;
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        input.parentNode.insertBefore(feedback, input.nextSibling);
                        feedback.textContent = 'This field is required';
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            } else {
                // Add loading state to submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                    submitBtn.disabled = true;
                }
            }
        });
    });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Handle notification clicks
    document.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var notificationId = this.dataset.notificationId;
            markNotificationAsRead(notificationId);
        });
    });

    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('table.table-hover tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
            this.style.backgroundColor = 'rgba(52, 152, 219, 0.05)';
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.05)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    // Add pulse animation to new data elements
    const newElements = document.querySelectorAll('.new-data');
    
    newElements.forEach(element => {
        element.classList.add('pulse');
        
        // Remove animation after 3 cycles
        setTimeout(() => {
            element.classList.remove('pulse');
        }, 6000);
    });
    
    // Make card headers draggable to rearrange dashboard
    const cards = document.querySelectorAll('.card');
    let draggedCard = null;
    
    cards.forEach(card => {
        const header = card.querySelector('.card-header');
        
        if (header) {
            header.setAttribute('draggable', 'true');
            
            header.addEventListener('dragstart', function(e) {
                draggedCard = card;
                setTimeout(() => {
                    card.style.opacity = '0.4';
                }, 0);
            });
            
            header.addEventListener('dragend', function() {
                draggedCard = null;
                card.style.opacity = '1';
            });
            
            card.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            
            card.addEventListener('dragenter', function(e) {
                e.preventDefault();
                if (draggedCard !== null && draggedCard !== card) {
                    this.style.borderTop = '2px solid #3498db';
                }
            });
            
            card.addEventListener('dragleave', function() {
                this.style.borderTop = '';
            });
            
            card.addEventListener('drop', function(e) {
                e.preventDefault();
                if (draggedCard !== null && draggedCard !== card) {
                    const parent = card.parentNode;
                    const cardRect = card.getBoundingClientRect();
                    const mouseY = e.clientY;
                    
                    if (mouseY < cardRect.top + cardRect.height / 2) {
                        // Insert before
                        parent.insertBefore(draggedCard, card);
                    } else {
                        // Insert after
                        parent.insertBefore(draggedCard, card.nextSibling);
                    }
                    
                    this.style.borderTop = '';
                }
            });
        }
    });
});

/**
 * Mark a notification as read via AJAX
 */
function markNotificationAsRead(notificationId) {
    fetch('?controller=notification&action=markAsRead&id=' + notificationId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('.notification-item[data-notification-id="' + notificationId + '"]')
                .classList.remove('bg-light');
            updateNotificationCounter();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

/**
 * Update the notification counter
 */
function updateNotificationCounter() {
    const counter = document.querySelector('.notification-counter');
    if (counter) {
        const currentCount = parseInt(counter.textContent);
        if (currentCount > 0) {
            counter.textContent = currentCount - 1;
            if (currentCount - 1 === 0) {
                counter.style.display = 'none';
            }
        }
    }
}

// Function to show notification toast
function showNotification(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    
    // Create container if it doesn't exist
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    document.getElementById('toast-container').appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, {
        animation: true,
        autohide: true,
        delay: 5000
    });
    
    bsToast.show();
    
    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Add chart color helpers
const chartColors = {
    primary: '#3498db',
    secondary: '#2ecc71',
    tertiary: '#9b59b6',
    warning: '#f1c40f',
    danger: '#e74c3c',
    dark: '#2c3e50',
    light: '#ecf0f1',
    gray: '#95a5a6',
    
    getPrimaryGradient: function(ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(52, 152, 219, 0.8)');
        gradient.addColorStop(1, 'rgba(52, 152, 219, 0.2)');
        return gradient;
    },
    
    getSecondaryGradient: function(ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(46, 204, 113, 0.8)');
        gradient.addColorStop(1, 'rgba(46, 204, 113, 0.2)');
        return gradient;
    },
    
    getTertiaryGradient: function(ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(155, 89, 182, 0.8)');
        gradient.addColorStop(1, 'rgba(155, 89, 182, 0.2)');
        return gradient;
    }
}; 