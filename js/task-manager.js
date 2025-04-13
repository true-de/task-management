const TaskManager = {
    init: function() {
        // Initialize all task-related event handlers
        this.attachEventHandlers();
        this.initFormHandlers();
    },

    // Attach event handlers to dynamic elements
    attachEventHandlers: function() {
        // View task details
        $(document).on('click', '.view-task', function() {
            const taskId = $(this).data('id');
            TaskManager.loadTaskDetails(taskId);
        });

        // Edit task
        $(document).on('click', '.edit-task, .edit-task-modal', function() {
            const taskId = $(this).data('id');
            TaskManager.loadTaskForEdit(taskId);
        });

        // Delete task
        $(document).on('click', '.delete-task', function() {
            const taskId = $(this).data('id');
            if (confirm('Are you sure you want to delete this task?')) {
                TaskManager.deleteTask(taskId);
            }
        });

        // Toggle task status
        $(document).on('change', '.task-status-toggle', function() {
            const taskId = $(this).data('id');
            TaskManager.toggleTaskStatus(taskId);
        });
    },

    // Initialize form handlers
    initFormHandlers: function() {
        // Handle task form submission
        $('#task-form').submit(function(e) {
            e.preventDefault();

            const taskData = {
                action: 'add_task',
                title: $('#task-title').val(),
                description: $('#task-description').val(),
                deadline: $('#task-deadline').val(),
                priority: $('#task-priority').val(),
                assignee_id: $('#task-assignee').val()
            };

            $.ajax({
                url: 'process.php',
                type: 'POST',
                dataType: 'json',
                data: taskData,
                success: function(response) {
                    if (response.success) {
                        Notifications.show(response.message);

                        // Clear form
                        $('#task-form')[0].reset();

                        // Switch to All Tasks tab
                        const allTasksTab = document.querySelector('#taskTabs a[href="#all-tasks"]');
                        const tab = new bootstrap.Tab(allTasksTab);
                        tab.show();

                        // Refresh task list
                        TaskManager.refreshTasks();
                    } else {
                        Notifications.show(response.message, 'danger');
                    }
                },
                error: function() {
                    Notifications.show('Error connecting to server', 'danger');
                }
            });
        });

        // Handle edit task form submission
        $('#save-task-btn').click(function() {
            const taskId = $('#edit-task-id').val();
            const taskData = {
                action: 'update_task',
                task_id: taskId,
                title: $('#edit-task-title').val(),
                description: $('#edit-task-description').val(),
                deadline: $('#edit-task-deadline').val(),
                priority: $('#edit-task-priority').val(),
                assignee_id: $('#edit-task-assignee').val(),
                completed: $('#edit-task-completed').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: 'process.php',
                type: 'POST',
                dataType: 'json',
                data: taskData,
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        var editTaskModal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                        if (editTaskModal) {
                            editTaskModal.hide();
                        }

                        Notifications.show(response.message);
                        TaskManager.refreshTasks();
                    } else {
                        Notifications.show(response.message, 'danger');
                    }
                },
                error: function() {
                    Notifications.show('Error connecting to server', 'danger');
                }
            });
        });
    },

    // Load task details
    loadTaskDetails: function(taskId) {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_task',
                task_id: taskId
            },
            success: function(response) {
                if (response.success) {
                    const task = response.task;
                    const priorityClass = Utils.getPriorityClass(task.priority);
                    const deadlineDate = task.deadline ? new Date(task.deadline) : null;
                    const formattedDeadline = deadlineDate ? 
                        deadlineDate.toLocaleDateString() + ' ' + deadlineDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 
                        'No deadline';

                    let detailContent = `
                        <div class="task-detail">
                            <h4 class="mb-3">${task.title}</h4>
                            <div class="badge ${priorityClass} mb-3">${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)} Priority</div>
                            <div class="task-status mb-3">
                                Status: <span class="badge ${task.completed ? 'badge-success' : 'badge-warning'}">
                                    ${task.completed ? 'Completed' : 'Pending'}
                                </span>
                            </div>
                            <div class="task-assignee mb-3">
                                <i class="fas fa-user mr-1"></i> Assigned to: ${task.assignee_name || 'Unassigned'}
                            </div>
                            <div class="task-deadline mb-3">
                                <i class="far fa-calendar-alt mr-1"></i> Deadline: ${formattedDeadline}
                            </div>
                            <div class="task-description mb-3">
                                <h5>Description:</h5>
                                <p>${task.description || 'No description provided'}</p>
                            </div>
                            <div class="task-dates text-muted mt-4">
                                <small>Created: ${new Date(task.created_at).toLocaleString()}</small>
                                ${task.updated_at ? `<br><small>Last Updated: ${new Date(task.updated_at).toLocaleString()}</small>` : ''}
                            </div>
                        </div>
                    `;

                    $('#task-detail-content').html(detailContent);
                    var taskDetailModal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
                    taskDetailModal.show();

                    // Store task ID for edit button
                    $('#edit-task-btn').attr('data-id', task.task_id);
                } else {
                    Notifications.show('Failed to load task details', 'danger');
                }
            },
            error: function() {
                Notifications.show('Error connecting to server', 'danger');
            }
        });
    },

    // Load task data for editing
    loadTaskForEdit: function(taskId) {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_task',
                task_id: taskId
            },
            success: function(response) {
                if (response.success) {
                    const task = response.task;

                    $('#edit-task-id').val(task.task_id);
                    $('#edit-task-title').val(task.title);
                    $('#edit-task-description').val(task.description);
                    $('#edit-task-priority').val(task.priority);
                    $('#edit-task-deadline').val(Utils.formatDateForInput(task.deadline));
                    $('#edit-task-assignee').val(task.assignee_id || '');
                    $('#edit-task-completed').prop('checked', task.completed == 1);

                    var editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                    editTaskModal.show();
                } else {
                    Notifications.show('Failed to load task for editing', 'danger');
                }
            },
            error: function() {
                Notifications.show('Error connecting to server', 'danger');
            }
        });
    },

    // Delete task
    deleteTask: function(taskId) {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'delete_task',
                task_id: taskId
            },
            success: function(response) {
                if (response.success) {
                    Notifications.show(response.message);
                    TaskManager.refreshTasks();
                } else {
                    Notifications.show(response.message, 'danger');
                }
            },
            error: function() {
                Notifications.show('Error connecting to server', 'danger');
            }
        });
    },

    // Toggle task status
    toggleTaskStatus: function(taskId) {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'toggle_complete',
                task_id: taskId
            },
            success: function(response) {
                if (response.success) {
                    const labelText = response.new_status ? 'Completed' : 'Pending';
                    $(`label[for="status-${taskId}"]`).text(labelText);

                    // Update row class
                    const row = $(`.task-row[data-id="${taskId}"]`);
                    if (response.new_status) {
                        row.addClass('task-completed');
                    } else {
                        row.removeClass('task-completed');
                    }

                    Notifications.show('Task status updated');
                } else {
                    Notifications.show(response.message, 'danger');
                }
            },
            error: function() {
                Notifications.show('Error connecting to server', 'danger');
            }
        });
    },

    // Refresh task data
    refreshTasks: function() {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_tasks'
            },
            success: function(response) {
                if (response.success) {
                    // Update the table
                    let tableHTML = '';
                    response.tasks.forEach(function(task) {
                        const priorityClass = Utils.getPriorityClass(task.priority);
                        const completedClass = task.completed == 1 ? 'task-completed' : '';
                        const deadline = task.deadline ? new Date(task.deadline).toLocaleString() : 'No deadline';

                        tableHTML += `
                            <tr class="task-row priority-${task.priority} ${completedClass}" data-id="${task.task_id}">
                                <td>${task.title}</td>
                                <td><span class="badge ${priorityClass}">${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}</span></td>
                                <td>${deadline}</td>
                                <td>${task.assignee_name || 'Unassigned'}</td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input task-status-toggle" 
                                            id="status-${task.task_id}" ${task.completed == 1 ? 'checked' : ''} data-id="${task.task_id}">
                                        <label class="custom-control-label" for="status-${task.task_id}">
                                            ${task.completed == 1 ? 'Completed' : 'Pending'}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info view-task" data-id="${task.task_id}">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary edit-task" data-id="${task.task_id}">
                                        <i class="far fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.task_id}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    $('#all-tasks-table').html(tableHTML);

                    // Re-attach event handlers
                    TaskManager.attachEventHandlers();
                }
            }
        });
    }
};