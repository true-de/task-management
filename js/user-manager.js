const UserManager = {
    init: function() {
        this.initHandlers();
    },

    initHandlers: function() {
        // Handle user registration
        $('#register-user-form').submit(function(e) {
            e.preventDefault();

            const userData = {
                action: 'register_user',
                full_name: $('#reg-full-name').val(),
                username: $('#reg-username').val(),
                password: $('#reg-password').val()
            };

            $.ajax({
                url: 'process.php',
                type: 'POST',
                dataType: 'json',
                data: userData,
                success: function(response) {
                    if (response.success) {
                        Notifications.show(response.message || 'User registered successfully');
                        $('#register-user-form')[0].reset();
                    } else {
                        Notifications.show(response.message || 'Registration failed', 'danger');
                    }
                },
                error: function() {
                    Notifications.show('Error connecting to server', 'danger');
                }
            });
        });

        // User deletion handler
        $(document).on('click', '.delete-user-btn', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');

            $('#delete-user-name').text(userName);
            $('#confirm-delete-user').attr('href', '?delete_user_id=' + userId);

            // Create and show the modal correctly
            const deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            deleteUserModal.show();
        });

        // Confirm delete user button
        $('#confirm-delete-user').click(function(e) {
            e.preventDefault();
            const userId = this.href.split('=').pop();

            if (userId) {
                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete_user',
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Notifications.show(response.message);
                            // Close modal
                            var deleteUserModal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
                            if (deleteUserModal) {
                                deleteUserModal.hide();
                            }
                            // Reload the page to refresh user list
                            window.location.reload();
                        } else {
                            Notifications.show(response.message, 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        Notifications.show('Error connecting to server: ' + error, 'danger');
                    }
                });
            } else {
                Notifications.show('Invalid user ID', 'warning');
            }
        });
    }
};