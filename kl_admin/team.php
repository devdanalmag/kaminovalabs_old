<?php
require_once 'auth.php';
Auth::requireLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = $_POST['name'] ?? '';
                $position = $_POST['position'] ?? '';
                $bio = $_POST['bio'] ?? '';
                $email = $_POST['email'] ?? '';
                $linkedin = $_POST['linkedin'] ?? '';
                $twitter = $_POST['twitter'] ?? '';
                $display_order = $_POST['display_order'] ?? 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Handle file upload
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $upload_dir = '../uploads/team/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $image = 'uploads/team/' . $filename;
                    }
                }
                
                try {
                    insertRecord('team_members', [
                        'name' => $name,
                        'position' => $position,
                        'bio' => $bio,
                        'image' => $image,
                        'email' => $email,
                        'linkedin' => $linkedin,
                        'twitter' => $twitter,
                        'display_order' => $display_order,
                        'is_active' => $is_active
                    ]);
                    $message = 'Team member added successfully!';
                } catch (Exception $e) {
                    $error = 'Error adding team member: ' . $e->getMessage();
                }
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $name = $_POST['name'] ?? '';
                $position = $_POST['position'] ?? '';
                $bio = $_POST['bio'] ?? '';
                $email = $_POST['email'] ?? '';
                $linkedin = $_POST['linkedin'] ?? '';
                $twitter = $_POST['twitter'] ?? '';
                $display_order = $_POST['display_order'] ?? 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $update_data = [
                    'name' => $name,
                    'position' => $position,
                    'bio' => $bio,
                    'email' => $email,
                    'linkedin' => $linkedin,
                    'twitter' => $twitter,
                    'display_order' => $display_order,
                    'is_active' => $is_active
                ];
                
                // Handle file upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $upload_dir = '../uploads/team/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $update_data['image'] = 'uploads/team/' . $filename;
                    }
                }
                
                try {
                    $rows_affected = updateRecord('team_members', $update_data, 'id = :id', ['id' => $id]);
                    if ($rows_affected > 0) {
                        $message = 'Team member updated successfully!';
                    } else {
                        $error = 'No changes were made to the team member.';
                    }
                } catch (Exception $e) {
                    $error = 'Error updating team member: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                try {
                    deleteRecord('team_members', 'id = :id', ['id' => $id]);
                    $message = 'Team member deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting team member: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get team member for editing
$edit_member = null;
if (isset($_GET['edit'])) {
    $edit_member = fetchOne("SELECT * FROM team_members WHERE id = :id", ['id' => $_GET['edit']]);
}

// Get all team members
$team_members = fetchAll("SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Kaminova Labs Admin</title>
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
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="blog.php">
                        <i class="fas fa-blog"></i>
                        <span>Blog Posts</span>
                    </a>
                </li>
                <li>
                    <a href="projects.php">
                        <i class="fas fa-project-diagram"></i>
                        <span>Projects</span>
                    </a>
                </li>
                <li>
                    <a href="events.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                <li class="active">
                    <a href="team.php">
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
                    <a href="settings.php">
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
                    <h1>Team Management</h1>
                    <p>Manage team members</p>
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

                <!-- Team Member Form -->
                <div class="form-container">
                    <h2><?php echo $edit_member ? 'Edit Team Member' : 'Add New Team Member'; ?></h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_member ? 'update' : 'create'; ?>">
                        <?php if ($edit_member): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_member['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" id="position" name="position" required 
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['position']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" style="min-height: 100px;"><?php echo $edit_member ? htmlspecialchars($edit_member['bio']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Profile Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            <?php if ($edit_member && $edit_member['image']): ?>
                                <p>Current image: <?php echo htmlspecialchars($edit_member['image']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['email']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="linkedin">LinkedIn URL</label>
                                <input type="url" id="linkedin" name="linkedin" 
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['linkedin']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="twitter">Twitter URL</label>
                                <input type="url" id="twitter" name="twitter" 
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['twitter']) : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" 
                                       value="<?php echo $edit_member ? $edit_member['display_order'] : 0; ?>">
                            </div>

                            <div class="form-group">
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" 
                                           <?php echo ($edit_member && $edit_member['is_active']) ? 'checked' : ''; ?>>
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_member ? 'Update' : 'Add'; ?> Team Member
                            </button>
                            <?php if ($edit_member): ?>
                                <a href="team.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Team Members List -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Order</th>
                                <th>Active</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($team_members as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['position']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo $member['display_order']; ?></td>
                                    <td>
                                        <span class="status <?php echo $member['is_active'] ? 'active' : 'draft'; ?>">
                                            <?php echo $member['is_active'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($member['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $member['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this team member?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
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
    <script src="assets/admin.js"></script>
</body>
</html>