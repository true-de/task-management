<!-- All Tasks Tab -->
<div class="tab-pane fade show active" id="all-tasks" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Tasks</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                    id="filterDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
                    <a class="dropdown-item" href="#" data-filter="all">All Tasks</a>
                    <a class="dropdown-item" href="#" data-filter="completed">Completed</a>
                    <a class="dropdown-item" href="#" data-filter="pending">Pending</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-filter="high">High Priority</a>
                    <a class="dropdown-item" href="#" data-filter="medium">Medium Priority</a>
                    <a class="dropdown-item" href="#" data-filter="low">Low Priority</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Deadline</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-tasks-table">
                        <?php if (!empty($allTasks)): ?>
                            <?php foreach ($allTasks as $task): ?>
                                <tr class="task-row priority-<?php echo $task['priority']; ?> <?php echo $task['completed'] ? 'task-completed' : ''; ?>"
                                    data-id="<?php echo $task['task_id']; ?>">
                                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                                    <td><span
                                            class="badge badge-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($task['deadline'])); ?></td>
                                    <td><?php echo htmlspecialchars($task['assignee_name'] ?? 'Unassigned'); ?></td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input task-status-toggle"
                                                id="status-<?php echo $task['task_id']; ?>" <?php echo $task['completed'] ? 'checked' : ''; ?>
                                                data-id="<?php echo $task['task_id']; ?>">
                                            <label class="custom-control-label"
                                                for="status-<?php echo $task['task_id']; ?>"><?php echo $task['completed'] ? 'Completed' : 'Pending'; ?></label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info view-task"
                                            data-id="<?php echo $task['task_id']; ?>"><i
                                                class="far fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary edit-task"
                                            data-id="<?php echo $task['task_id']; ?>"><i
                                                class="far fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-task"
                                            data-id="<?php echo $task['task_id']; ?>"><i
                                                class="far fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No tasks found. Add a new task to get
                                    started!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>