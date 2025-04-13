<!-- Add Task Tab -->
<div class="tab-pane fade" id="add-task" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Add New Task</h5>
        </div>
        <div class="card-body">
            <form id="task-form">
                <div class="form-group">
                    <label for="task-title">Task Title</label>
                    <input type="text" class="form-control" id="task-title" required>
                </div>
                <div class="form-group">
                    <label for="task-description">Description</label>
                    <textarea class="form-control" id="task-description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="task-deadline">Deadline</label>
                    <input type="datetime-local" class="form-control" id="task-deadline">
                </div>
                <div class="form-group">
                    <label for="task-priority">Priority</label>
                    <select class="form-control" id="task-priority">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="task-assignee">Assign To</label>
                    <select class="form-control" id="task-assignee">
                        <option value="">-- Unassigned --</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Add Task</button>
            </form>
        </div>
    </div>
</div>