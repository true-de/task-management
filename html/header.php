<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <!-- add these favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Task Management System</h1>

        <!-- Notification Area -->
        <div id="notification-area" class="notification"></div>
        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Task Management Header -->
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-tasks mr-2"></i> <strong>Task Manager</strong>
                </div>
                <div>
                    <button class="btn btn-outline-primary btn-sm" id="refresh-btn">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>