$(document).ready(function () {
    // Delete user button click
    $('.delete-user-btn').on('click', function () {
        const userId = $(this).data('id');
        const userName = $(this).data('name');

        $('#delete-user-name').text(userName);
        $('#confirm-delete-user').attr('href', '?delete_user_id=' + userId);

        // Create and show the modal correctly
        const deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        deleteUserModal.show();
    });

    // Auto-dismiss success alerts after 5 seconds (but NOT modals)
    setTimeout(function () {
        // Bootstrap 5 compatible way to close alerts
        const alerts = document.querySelectorAll('.alert-info');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});