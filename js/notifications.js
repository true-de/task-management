const Notifications = {
    init: function() {
        // Set up any initial configuration for notifications
        console.log('Notifications module initialized');
    },
    
    // Show notification
    show: function(message, type = 'success') {
        const notification = $('#notification-area');
        notification.html(`<div class="alert alert-${type} alert-dismissible fade show">
                                ${message}
                                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>`);
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            notification.find('.alert').alert('close');
        }, 5000);
    }
};