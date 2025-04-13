const TaskFilters = {
    init: function() {
        this.initTaskFilters();
    },

    // Initialize task filters
    initTaskFilters: function() {
        $('.dropdown-item[data-filter]').click(function(e) {
            e.preventDefault();
            const filter = $(this).data('filter');

            // Update button text
            const buttonText = $(this).text();
            $('#filterDropdown').html(`<i class="fas fa-filter mr-1"></i> ${buttonText}`);

            // Apply filter
            if (filter === 'all') {
                $('.task-row').show();
            } else if (filter === 'completed') {
                $('.task-row').hide();
                $('.task-row.task-completed').show();
            } else if (filter === 'pending') {
                $('.task-row').hide();
                $('.task-row:not(.task-completed)').show();
            } else {
                $('.task-row').hide();
                $(`.task-row.priority-${filter}`).show();
            }
        });
    }
};