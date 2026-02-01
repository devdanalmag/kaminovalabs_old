<?php
require_once 'auth.php';
Auth::requireLogin();

$user = Auth::getCurrentUser();
$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? 0;
    try {
        if ($_GET['action'] === 'read') {
            updateRecord('contact_messages', ['status' => 'read'], 'id = :id', ['id' => $id]);
            $message = 'Message marked as read.';
        } elseif ($_GET['action'] === 'unread') {
            updateRecord('contact_messages', ['status' => 'unread'], 'id = :id', ['id' => $id]);
            $message = 'Message marked as unread.';
        } elseif ($_GET['action'] === 'delete') {
            deleteRecord('contact_messages', 'id = :id', ['id' => $id]);
            $message = 'Message deleted successfully.';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get all messages
$messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Kaminova Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="/img/logo-tp.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .message-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .message-card {
            background: #1a1f3c;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            transition: all 0.3s ease;
        }

        .message-card.unread {
            border-left: 4px solid var(--electric-blue);
            background: #1e2548;
        }

        .message-card h4 {
            color: var(--electric-blue);
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-meta {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 15px;
        }

        .message-content {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
            max-width: 100%;
            color: white;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .badge-unread {
            background: var(--electric-blue);
            color: #000;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>Kaminova Labs</h3>
                <p>Admin Panel</p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="blog.php"><i class="fas fa-blog"></i><span>Blog Posts</span></a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i><span>Projects</span></a></li>
                <li><a href="events.php"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
                <li><a href="team.php"><i class="fas fa-users"></i><span>Team Members</span></a></li>
                <li class="active"><a href="messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i><span>Site Settings</span></a></li>
                <li><a href="?logout=1" onclick="return confirm('Are you sure you want to logout?')"><i
                            class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>

        <div class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <h1>Contact Messages</h1>
                    <p>Manage inquiries from your website</p>
                </div>
                <div class="header-right">
                    <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
                    <span class="user-info"><i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </span>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($messages)): ?>
                    <div class="no-data"><i class="fas fa-inbox fa-3x"></i>
                        <p>No messages yet.</p>
                    </div>
                <?php else: ?>
                    <div class="message-grid">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-card <?php echo $msg['status'] === 'unread' ? 'unread' : ''; ?>">
                                <h4>
                                    <?php echo htmlspecialchars($msg['subject']); ?>
                                    <?php if ($msg['status'] === 'unread'): ?><span class="badge-unread">New</span>
                                    <?php endif; ?>
                                </h4>
                                <div class="message-meta">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($msg['name']); ?> &bull;
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($msg['email']); ?> &bull;
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M j, Y H:i', strtotime($msg['created_at'])); ?>
                                </div>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </div>
                                <div class="message-actions">
                                    <?php if ($msg['status'] === 'unread'): ?>
                                        <a href="?action=read&id=<?php echo $msg['id']; ?>" class="btn btn-primary btn-sm">Mark
                                            Read</a>
                                    <?php else: ?>
                                        <a href="?action=unread&id=<?php echo $msg['id']; ?>" class="btn btn-secondary btn-sm">Mark
                                            Unread</a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $msg['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this message?')">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script src="assets/admin.js"></script>
    </div>
</body>

</html>
