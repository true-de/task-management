$(document).ready(function() {
    // Initialize all modules
    Notifications.init();
    TaskManager.init();
    UserManager.init();
    
    // Initialize the task filters
    TaskFilters.init();
    
    // Refresh button
    $('#refresh-btn').click(function() {
        TaskManager.refreshTasks();
        Notifications.show('Data refreshed');
    });
    
    // Initialize tab navigation properly with Bootstrap 5
    document.querySelectorAll('#taskTabs .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('href');
            const tab = new bootstrap.Tab(this);
            tab.show();
        });
    });
});