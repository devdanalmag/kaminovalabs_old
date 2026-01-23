<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../config/rate_limit.php';
require_once 'auth.php';
Auth::requireLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF and rate limit checks
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token. Please refresh and try again.';
    } elseif (!rate_limit('admin_events', 30, 60)) {
        $error = 'Too many requests. Please wait a moment and try again.';
    }
    if (!$error) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = $_POST['title'] ?? '';
                $slug = $_POST['slug'] ?? '';
                $description = $_POST['description'] ?? '';
                $event_date = $_POST['event_date'] ?? '';
                $event_time = $_POST['event_time'] ?? '';
                $location = $_POST['location'] ?? '';
                $event_type = $_POST['event_type'] ?? 'upcoming';
                $speakers = $_POST['speakers'] ?? '';
                $registration_link = $_POST['registration_link'] ?? '';
                $watch_link = $_POST['watch_link'] ?? '';
                $x_link = $_POST['x_link'] ?? '';
                // Validate URLs
                foreach ([&$registration_link, &$watch_link, &$x_link] as &$ref) {
                    if ($ref && !filter_var($ref, FILTER_VALIDATE_URL)) { $ref = ''; }
                }
                $icon_class = $_POST['icon_class'] ?? '';
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Handle file upload
                $featured_image = '';
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                    $upload_dir = '../uploads/events/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                        $featured_image = 'uploads/events/' . $filename;
                    }
                }
                
                try {
                    insertRecord('events', [
                        'title' => $title,
                        'slug' => $slug,
                        'description' => $description,
                        'event_date' => $event_date,
                        'event_time' => $event_time,
                        'location' => $location,
                        'event_type' => $event_type,
                        'speakers' => $speakers,
                        'registration_link' => $registration_link,
                        'watch_link' => $watch_link,
                        'x_link' => $x_link,
                        'featured_image' => $featured_image,
                        'icon_class' => $icon_class,
                        'is_active' => $is_active
                    ]);
                    $message = 'Event created successfully!';
                } catch (Exception $e) {
                    $error = 'Error creating event: ' . $e->getMessage();
                }
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $slug = $_POST['slug'] ?? '';
                $description = $_POST['description'] ?? '';
                $event_date = $_POST['event_date'] ?? '';
                $event_time = $_POST['event_time'] ?? '';
                $location = $_POST['location'] ?? '';
                $event_type = $_POST['event_type'] ?? 'upcoming';
                $speakers = $_POST['speakers'] ?? '';
                $registration_link = $_POST['registration_link'] ?? '';
                $watch_link = $_POST['watch_link'] ?? '';
                $x_link = $_POST['x_link'] ?? '';
                foreach ([&$registration_link, &$watch_link, &$x_link] as &$ref) {
                    if ($ref && !filter_var($ref, FILTER_VALIDATE_URL)) { $ref = ''; }
                }
                $icon_class = $_POST['icon_class'] ?? '';
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $update_data = [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'event_date' => $event_date,
                    'event_time' => $event_time,
                    'location' => $location,
                    'event_type' => $event_type,
                    'speakers' => $speakers,
                    'registration_link' => $registration_link,
                    'watch_link' => $watch_link,
                    'x_link' => $x_link,
                    'icon_class' => $icon_class,
                    'is_active' => $is_active
                ];
                
                // Handle file upload
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                    $upload_dir = '../uploads/events/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                        $update_data['featured_image'] = 'uploads/events/' . $filename;
                    }
                }
                
                try {
                    $rows_affected = updateRecord('events', $update_data, 'id = :id', ['id' => $id]);
                    if ($rows_affected > 0) {
                        $message = 'Event updated successfully!';
                    } else {
                        $error = 'No changes were made to the event.';
                    }
                } catch (Exception $e) {
                    $error = 'Error updating event: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                try {
                    deleteRecord('events', 'id = :id', ['id' => $id]);
                    $message = 'Event deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting event: ' . $e->getMessage();
                }
                break;
        }
    }
}
}

// Get event for editing
$edit_event = null;
if (isset($_GET['edit'])) {
    $edit_event = fetchOne("SELECT * FROM events WHERE id = :id", ['id' => $_GET['edit']]);
}

// Get all events
$events = fetchAll("SELECT * FROM events ORDER BY event_date DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - Kaminova Labs Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
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
                <li>
                    <a href="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="blog">
                        <i class="fas fa-blog"></i>
                        <span>Blog Posts</span>
                    </a>
                </li>
                <li>
                    <a href="projects">
                        <i class="fas fa-project-diagram"></i>
                        <span>Projects</span>
                    </a>
                </li>
                <li class="active">
                    <a href="events">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                <li>
                    <a href="team">
                        <i class="fas fa-users"></i>
                        <span>Team Members</span>
                    </a>
                </li>
                <li>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li>
                    <a href="settings">
                        <i class="fas fa-cog"></i>
                        <span>Site Settings</span>
                    </a>
                </li>
                <li>
                    <a href="?logout=1" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <h1>Event Management</h1>
                    <p>Create and manage events</p>
                </div>
                <div class="header-right">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Event Form -->
                <div class="form-container">
                    <h2><?php echo $edit_event ? 'Edit Event' : 'Create New Event'; ?></h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_event ? 'update' : 'create'; ?>">
                        <?php if ($edit_event): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_event['id']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="title">Event Title</label>
                                <input type="text" id="title" name="title" required 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['title']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug (URL-friendly)</label>
                                <input type="text" id="slug" name="slug" required 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['slug']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required style="min-height: 120px;"><?php echo $edit_event ? htmlspecialchars($edit_event['description']) : ''; ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="event_date">Event Date</label>
                                <input type="date" id="event_date" name="event_date" 
                                       value="<?php echo $edit_event ? $edit_event['event_date'] : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="event_time">Event Time</label>
                                <input type="time" id="event_time" name="event_time" 
                                       value="<?php echo $edit_event ? $edit_event['event_time'] : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="event_type">Event Type</label>
                                <select id="event_type" name="event_type">
                                    <option value="upcoming" <?php echo ($edit_event && $edit_event['event_type'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="past" <?php echo ($edit_event && $edit_event['event_type'] == 'past') ? 'selected' : ''; ?>>Past</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" 
                                   value="<?php echo $edit_event ? htmlspecialchars($edit_event['location']) : ''; ?>"
                                   placeholder="e.g., Virtual Event, Lagos Nigeria">
                        </div>

                        <div class="form-group">
                            <label for="speakers">Speakers</label>
                            <textarea id="speakers" name="speakers" style="min-height: 80px;" placeholder="List of speakers"><?php echo $edit_event ? htmlspecialchars($edit_event['speakers']) : ''; ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="registration_link">Registration Link</label>
                                <input type="url" id="registration_link" name="registration_link" 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['registration_link']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="watch_link">Watch Replay Link</label>
                                <input type="url" id="watch_link" name="watch_link" placeholder="https://..." 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['watch_link'] ?? '') : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="x_link">X (Twitter) Post URL</label>
                                <input type="url" id="x_link" name="x_link" placeholder="https://x.com/..." 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['x_link'] ?? '') : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="icon_class">Icon Class</label>
                                <input type="text" id="icon_class" name="icon_class" 
                                       value="<?php echo $edit_event ? htmlspecialchars($edit_event['icon_class']) : ''; ?>"
                                       placeholder="e.g., fas fa-calendar-alt">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="featured_image">Featured Image</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*">
                            <?php if ($edit_event && $edit_event['featured_image']): ?>
                                <p>Current image: <?php echo htmlspecialchars($edit_event['featured_image']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="is_active">
                                <input type="checkbox" id="is_active" name="is_active" 
                                       <?php echo ($edit_event && $edit_event['is_active']) ? 'checked' : ''; ?>>
                                Active
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_event ? 'Update' : 'Create'; ?> Event
                            </button>
                            <?php if ($edit_event): ?>
                                <a href="events.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Events List -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Active</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo $event['event_date'] ? date('M j, Y', strtotime($event['event_date'])) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td>
                                        <span class="status <?php echo $event['event_type'] == 'upcoming' ? 'active' : 'draft'; ?>">
                                            <?php echo ucfirst($event['event_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status <?php echo $event['is_active'] ? 'active' : 'draft'; ?>">
                                            <?php echo $event['is_active'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($event['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $event['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this event?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.getElementById('slug').value = slug;
        });
    </script>
    <script src="assets/admin.js"></script>
</body>
</html>