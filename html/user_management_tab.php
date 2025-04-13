<!-- User Management Tab -->
<div class="tab-pane fade" id="register-user" role="tabpanel" aria-labelledby="register-user-tab">
    <!-- Registration Form Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Register New User</h5>
        </div>
        <div class="card-body">
            <form id="register-user-form" method="POST">
                <input type="hidden" name="register_user" value="1">
                <div class="form-group">
                    <label for="reg-full-name">Full Name</label>
                    <input type="text" class="form-control" name="full_name" id="reg-full-name" required>
                </div>
                <div class="form-group">
                    <label for="reg-username">Username</label>
                    <input type="text" class="form-control" name="username" id="reg-username" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" class="form-control" name="password" id="reg-password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register User</button>
            </form>
        </div>
    </div>

    <!-- User List Card -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Current Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-user-btn"
                                            data-id="<?php echo $user['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($user['full_name']); ?>">
                                            <i class="far fa-trash-alt"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>