import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Polling-based notification system
document.addEventListener('DOMContentLoaded', function() {
    let lastNotificationId = 0;
    
    // Check for new notifications every 3 seconds
    setInterval(() => {
        checkNewNotifications();
    }, 3000);
    
    function checkNewNotifications() {
        fetch(`/api/notifications/check?last_id=${lastNotificationId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': `Bearer ${getApiToken()}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.new_notifications && data.new_notifications.length > 0) {
                data.new_notifications.forEach(notification => {
                    showToastNotification(notification.data);
                    lastNotificationId = Math.max(lastNotificationId, notification.id);
                });
                
                // Update notification bell
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
    }
    
    function getApiToken() {
        // Get API token from meta tag or localStorage
        const token = document.head.querySelector('meta[name="api-token"]');
        return token ? token.getAttribute('content') : '';
    }
    
    function showToastNotification(data) {
        if (window.Swal) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'info',
                title: data.title || 'Notification',
                text: data.message
            });
        }
    }
    
    // Initial load
    loadNotifications();
});

// Keep existing loadNotifications function
function loadNotifications() {
    fetch('/admin/notifications/bell-data', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        updateNotificationBell(data);
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
    });
}

function updateNotificationBell(data) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (data.unread_count > 0) {
            badge.textContent = data.unread_count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }
    
    const dropdownMenu = document.querySelector('#notificationDropdown');
    if (dropdownMenu) {
        let content = '';
        
        if (data.recent_notifications && data.recent_notifications.length > 0) {
            content += '<h6 class="dropdown-header">Notifikasi Terbaru</h6>';
            
            data.recent_notifications.forEach(notification => {
                let message = formatNotificationMessage(notification);
                let readClass = notification.read_at ? '' : 'fw-bold';
                
                content += `
                    <a class="dropdown-item ${readClass}" href="${notification.data.url || '#'}">
                        <div class="small">${message}</div>
                        <div class="text-muted small">${notification.created_at}</div>
                    </a>
                `;
            });
            
            content += '<div class="dropdown-divider"></div>';
            content += '<a class="dropdown-item text-center" href="/admin/notifications">Lihat Semua Notifikasi</a>';
        } else {
            content = '<div class="dropdown-item-text">Tidak ada notifikasi</div>';
        }
        
        dropdownMenu.innerHTML = content;
    }
}

function formatNotificationMessage(notification) {
    if (!notification.data) {
        return 'Notifikasi baru';
    }
    
    switch (notification.type) {
        case 'App\\Notifications\\NewUserRegistered':
            return `Pengguna baru terdaftar: ${notification.data.name || 'Unknown User'}`;
            
        case 'App\\Notifications\\NewOrder':
            return `Pesanan baru: #${notification.data.order_id || 'Unknown'}`;
            
        case 'App\\Notifications\\OrderStatusChanged':
            return `Pesanan #${notification.data.order_id || 'Unknown'} status berubah menjadi ${notification.data.status || 'Unknown Status'}`;
            
        case 'App\\Notifications\\NewProduct':
            return `Produk baru ditambahkan: ${notification.data.product_name || 'Unknown Product'}`;
            
        default:
            return notification.data.message || 'Notifikasi baru';
    }
}