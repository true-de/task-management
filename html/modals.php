<!-- Task Detail Modal -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="task-detail-content">
                <!-- Task details will be dynamically added here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary edit-task-modal" id="edit-task-btn">Edit
                    Task</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-task-form">
                    <input type="hidden" id="edit-task-id">
                    <div class="form-group">
                        <label for="edit-task-title">Task Title</label>
                        <input type="text" class="form-control" id="edit-task-title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-task-description">Description</label>
                        <textarea class="form-control" id="edit-task-description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-task-deadline">Deadline</label>
                        <input type="datetime-local" class="form-control" id="edit-task-deadline">
                    </div>
                    <div class="form-group">
                        <label for="edit-task-priority">Priority</label>
                        <select class="form-control" id="edit-task-priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-task-assignee">Assign To</label>
                        <select class="form-control" id="edit-task-assignee">
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
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit-task-completed">
                            <label class="custom-control-label" for="edit-task-completed">Mark as
                                Completed</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-task-btn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user: <strong id="delete-user-name"></strong>?</p>
                <p class="text-danger">This action cannot be undone. All tasks assigned to this user will become
                    unassigned.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirm-delete-user" class="btn btn-danger">Delete User</a>
            </div>
        </div>
    </div>
</div>