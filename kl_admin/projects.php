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
                $title = $_POST['title'] ?? '';
                $slug = $_POST['slug'] ?? '';
                $category = $_POST['category'] ?? '';
                $status = $_POST['status'] ?? '';
                $short_description = $_POST['short_description'] ?? '';
                $full_description = $_POST['full_description'] ?? '';
                $features = $_POST['features'] ?? '';
                $team_role = $_POST['team_role'] ?? '';
                $team_name = $_POST['team_name'] ?? '';
                $icon_class = $_POST['icon_class'] ?? '';
                $display_order = $_POST['display_order'] ?? 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Convert features to JSON
                $features_array = array_filter(array_map('trim', explode("\n", $features)));
                $features_json = json_encode($features_array);
                
                // Handle file upload
                $project_image = '';
                if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
                    $upload_dir = '../uploads/projects/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['project_image']['tmp_name'], $upload_path)) {
                        $project_image = 'uploads/projects/' . $filename;
                    }
                }
                
                try {
                    insertRecord('projects', [
                        'title' => $title,
                        'slug' => $slug,
                        'category' => $category,
                        'status' => $status,
                        'short_description' => $short_description,
                        'full_description' => $full_description,
                        'features' => $features_json,
                        'team_role' => $team_role,
                        'team_name' => $team_name,
                        'project_image' => $project_image,
                        'icon_class' => $icon_class,
                        'display_order' => $display_order,
                        'is_active' => $is_active
                    ]);
                    $message = 'Project created successfully!';
                } catch (Exception $e) {
                    $error = 'Error creating project: ' . $e->getMessage();
                }
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $slug = $_POST['slug'] ?? '';
                $category = $_POST['category'] ?? '';
                $status = $_POST['status'] ?? '';
                $short_description = $_POST['short_description'] ?? '';
                $full_description = $_POST['full_description'] ?? '';
                $features = $_POST['features'] ?? '';
                $team_role = $_POST['team_role'] ?? '';
                $team_name = $_POST['team_name'] ?? '';
                $icon_class = $_POST['icon_class'] ?? '';
                $display_order = $_POST['display_order'] ?? 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Convert features to JSON
                $features_array = array_filter(array_map('trim', explode("\n", $features)));
                $features_json = json_encode($features_array);
                
                $update_data = [
                    'title' => $title,
                    'slug' => $slug,
                    'category' => $category,
                    'status' => $status,
                    'short_description' => $short_description,
                    'full_description' => $full_description,
                    'features' => $features_json,
                    'team_role' => $team_role,
                    'team_name' => $team_name,
                    'icon_class' => $icon_class,
                    'display_order' => $display_order,
                    'is_active' => $is_active
                ];
                
                // Handle file upload
                if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
                    $upload_dir = '../uploads/projects/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['project_image']['tmp_name'], $upload_path)) {
                        $update_data['project_image'] = 'uploads/projects/' . $filename;
                    }
                }
                
                try {
                    $rows_affected = updateRecord('projects', $update_data, 'id = :id', ['id' => $id]);
                    if ($rows_affected > 0) {
                        $message = 'Project updated successfully!';
                    } else {
                        $error = 'No changes were made to the project.';
                    }
                } catch (Exception $e) {
                    $error = 'Error updating project: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                try {
                    deleteRecord('projects', 'id = :id', ['id' => $id]);
                    $message = 'Project deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting project: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get project for editing
$edit_project = null;
if (isset($_GET['edit'])) {
    $edit_project = fetchOne("SELECT * FROM projects WHERE id = :id", ['id' => $_GET['edit']]);
    if ($edit_project && $edit_project['features']) {
        $edit_project['features_text'] = implode("\n", json_decode($edit_project['features'], true) ?: []);
    }
}

// Get all projects
$projects = fetchAll("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");

// Categories for dropdown
$categories = [
    'BioFuture & Governance',
    'Forensics & Security',
    'Logistics & Utility',
    'Finance & Payments',
    'Ecosystem & Advisory Projects'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management - Kaminova Labs Admin</title>
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
                <li class="active">
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
                <li>
                    <a href="team.php">
                        <i class="fas fa-users"></i>
                        <span>Team Members</span>
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
                    <h1>Project Management</h1>
                    <p>Create and manage projects</p>
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

                <!-- Project Form -->
                <div class="form-container">
                    <h2><?php echo $edit_project ? 'Edit Project' : 'Create New Project'; ?></h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_project ? 'update' : 'create'; ?>">
                        <?php if ($edit_project): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" required 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug (URL-friendly)</label>
                                <input type="text" id="slug" name="slug" required 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['slug']) : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                                <?php echo ($edit_project && $edit_project['category'] == $cat) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <input type="text" id="status" name="status" 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['status']) : ''; ?>"
                                       placeholder="e.g., Under Development, Completed">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <textarea id="short_description" name="short_description" required><?php echo $edit_project ? htmlspecialchars($edit_project['short_description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="full_description">Full Description</label>
                            <textarea id="full_description" name="full_description" required style="min-height: 150px;"><?php echo $edit_project ? htmlspecialchars($edit_project['full_description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="features">Features (one per line)</label>
                            <textarea id="features" name="features" style="min-height: 120px;" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><?php echo $edit_project ? htmlspecialchars($edit_project['features_text']) : ''; ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="team_role">Team Role</label>
                                <input type="text" id="team_role" name="team_role" 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['team_role']) : ''; ?>"
                                       placeholder="e.g., Founder, CTO, Advisory">
                            </div>

                            <div class="form-group">
                                <label for="team_name">Team Name</label>
                                <input type="text" id="team_name" name="team_name" 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['team_name']) : ''; ?>"
                                       placeholder="e.g., Team Lead, Chief Technology Officer">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="icon_class">Icon Class</label>
                                <input type="text" id="icon_class" name="icon_class" 
                                       value="<?php echo $edit_project ? htmlspecialchars($edit_project['icon_class']) : ''; ?>"
                                       placeholder="e.g., fas fa-leaf">
                            </div>

                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" 
                                       value="<?php echo $edit_project ? $edit_project['display_order'] : 0; ?>">
                            </div>

                            <div class="form-group">
                                <label for="is_active">
                                    <input type="checkbox" id="is_active" name="is_active" 
                                           <?php echo ($edit_project && $edit_project['is_active']) ? 'checked' : ''; ?>>
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="project_image">Project Image</label>
                            <input type="file" id="project_image" name="project_image" accept="image/*">
                            <?php if ($edit_project && $edit_project['project_image']): ?>
                                <p>Current image: <?php echo htmlspecialchars($edit_project['project_image']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_project ? 'Update' : 'Create'; ?> Project
                            </button>
                            <?php if ($edit_project): ?>
                                <a href="projects.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Projects List -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th>Active</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                                    <td><?php echo htmlspecialchars($project['category']); ?></td>
                                    <td><?php echo htmlspecialchars($project['status']); ?></td>
                                    <td><?php echo $project['display_order']; ?></td>
                                    <td>
                                        <span class="status <?php echo $project['is_active'] ? 'active' : 'draft'; ?>">
                                            <?php echo $project['is_active'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this project?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
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