<?php
require_once 'auth.php';
Auth::requireLogin();

$user = Auth::getCurrentUser();
$message = '';
$error = '';

// Defensive migration: ensure table and columns exist
try {
    executeQuery("CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        position VARCHAR(100) NOT NULL,
        bio TEXT,
        image VARCHAR(255),
        email VARCHAR(100),
        linkedin VARCHAR(255),
        twitter VARCHAR(255),
        github VARCHAR(255),
        display_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $cols = fetchAll("SHOW COLUMNS FROM team_members LIKE 'github'");
    if (empty($cols)) {
        executeQuery("ALTER TABLE team_members ADD COLUMN github VARCHAR(255) NULL AFTER twitter");
    }

    $deptCol = fetchAll("SHOW COLUMNS FROM team_members LIKE 'department'");
    if (empty($deptCol)) {
        executeQuery("ALTER TABLE team_members ADD COLUMN department VARCHAR(100) NULL AFTER position");
    }
} catch (Exception $e) {
    // Ignore migration failure if permission/already exists
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = trim($_POST['name'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $department = trim($_POST['department'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $linkedin = trim($_POST['linkedin'] ?? '');
                $twitter = trim($_POST['twitter'] ?? '');
                $github = trim($_POST['github'] ?? '');
                $display_order = (int)($_POST['display_order'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($name) || empty($position)) {
                    $error = 'Name and Position are required fields.';
                    break;
                }

                // Handle file upload
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/team/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $filename = 'team_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $image = 'uploads/team/' . $filename;
                        } else {
                            $error = 'Failed to upload profile image.';
                            break;
                        }
                    } else {
                        $error = 'Invalid image format. Allowed: JPG, PNG, WEBP, GIF.';
                        break;
                    }
                }
                
                try {
                    insertRecord('team_members', [
                        'name' => $name,
                        'position' => $position,
                        'department' => $department,
                        'bio' => $bio,
                        'image' => $image,
                        'email' => $email,
                        'linkedin' => $linkedin,
                        'twitter' => $twitter,
                        'github' => $github,
                        'display_order' => $display_order,
                        'is_active' => $is_active
                    ]);
                    $message = 'Team member added successfully!';
                } catch (Exception $e) {
                    $error = 'Error adding team member: ' . $e->getMessage();
                }
                break;
                
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $department = trim($_POST['department'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $linkedin = trim($_POST['linkedin'] ?? '');
                $twitter = trim($_POST['twitter'] ?? '');
                $github = trim($_POST['github'] ?? '');
                $display_order = (int)($_POST['display_order'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($name) || empty($position)) {
                    $error = 'Name and Position are required fields.';
                    break;
                }

                $update_data = [
                    'name' => $name,
                    'position' => $position,
                    'department' => $department,
                    'bio' => $bio,
                    'email' => $email,
                    'linkedin' => $linkedin,
                    'twitter' => $twitter,
                    'github' => $github,
                    'display_order' => $display_order,
                    'is_active' => $is_active
                ];
                
                // Handle file upload if new file provided
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/team/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $filename = 'team_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $update_data['image'] = 'uploads/team/' . $filename;
                        } else {
                            $error = 'Failed to upload profile image.';
                            break;
                        }
                    } else {
                        $error = 'Invalid image format. Allowed: JPG, PNG, WEBP, GIF.';
                        break;
                    }
                } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                    $update_data['image'] = '';
                }
                
                try {
                    updateRecord('team_members', $update_data, 'id = :id', ['id' => $id]);
                    $message = 'Team member updated successfully!';
                } catch (Exception $e) {
                    $error = 'Error updating team member: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
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
    $edit_member = fetchOne("SELECT * FROM team_members WHERE id = :id", ['id' => (int)$_GET['edit']]);
}

// Get all team members
$team_members = fetchAll("SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC");

// Calculate stats
$total_members = count($team_members);
$active_members = count(array_filter($team_members, fn($m) => (int)$m['is_active'] === 1));
$photo_members = count(array_filter($team_members, fn($m) => !empty($m['image'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Kaminova Labs Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="/img/logo-tp.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .team-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .team-stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .team-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.25rem;
        }
        .team-stat-info h4 {
            font-size: 1.6rem;
            color: #1a1f3c;
            margin-bottom: 0.2rem;
        }
        .team-stat-info p {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .avatar-cell {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--electric-blue);
            background: #0b1528;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .avatar-placeholder {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(0, 102, 255, 0.15);
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border: 2px solid rgba(0, 102, 255, 0.3);
        }
        .member-info-cell {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }
        .member-meta strong {
            display: block;
            color: #1a1f3c;
            font-size: 0.95rem;
        }
        .member-meta span {
            font-size: 0.82rem;
            color: #0066ff;
            font-weight: 500;
        }
        .social-link-chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .social-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #eef4ff;
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .social-chip:hover {
            background: var(--primary-blue);
            color: #ffffff;
            transform: translateY(-2px);
        }
        .image-preview-box {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
            padding: 10px;
            background: rgba(0, 102, 255, 0.05);
            border-radius: 8px;
            border: 1px dashed rgba(0, 102, 255, 0.3);
        }
        .image-preview-box img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--electric-blue);
        }
        .badge-active {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
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
                    <a href="partners.php">
                        <i class="fas fa-handshake"></i>
                        <span>Ecosystem Partners</span>
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
                    <p>Add, edit, and organize leadership & team members</p>
                </div>
                <div class="header-right">
                    <a href="../contact.php#team" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; margin-right: 12px; text-decoration: none; padding: 6px 14px; font-size: 0.85rem;">
                        <i class="fas fa-external-link-alt"></i> View on Contact Page
                    </a>
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <?php if (isset($user['username'])): ?>
                        <span class="user-info"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['username']) ?></span>
                    <?php endif; ?>
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

                <!-- Team Stats Row -->
                <div class="team-stats-grid">
                    <div class="team-stat-card">
                        <div class="team-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="team-stat-info">
                            <h4><?= $total_members ?></h4>
                            <p>Total Members</p>
                        </div>
                    </div>
                    <div class="team-stat-card">
                        <div class="team-stat-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="team-stat-info">
                            <h4><?= $active_members ?></h4>
                            <p>Active Members</p>
                        </div>
                    </div>
                    <div class="team-stat-card">
                        <div class="team-stat-icon" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="team-stat-info">
                            <h4><?= $photo_members ?></h4>
                            <p>With Profile Photo</p>
                        </div>
                    </div>
                </div>

                <!-- Team Member Form -->
                <div class="form-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2><?php echo $edit_member ? '<i class="fas fa-edit"></i> Edit Team Member' : '<i class="fas fa-user-plus"></i> Add New Team Member'; ?></h2>
                        <?php if ($edit_member): ?>
                            <a href="team.php" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Switch to Add New</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_member ? 'update' : 'create'; ?>">
                        <?php if ($edit_member): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_member['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div class="form-group">
                                <label for="name">Full Name <span style="color: #ff4d4f;">*</span></label>
                                <input type="text" id="name" name="name" required placeholder="e.g. Dr. Elena Kaminova"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="position">Position / Role <span style="color: #ff4d4f;">*</span></label>
                                <input type="text" id="position" name="position" required placeholder="e.g. Chief Executive Officer"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['position']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="department">Department / Category</label>
                                <input type="text" id="department" name="department" list="dept-suggestions" placeholder="e.g. Leadership, Engineering"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['department'] ?? '') : ''; ?>">
                                <datalist id="dept-suggestions">
                                    <option value="Leadership">
                                    <option value="Engineering">
                                    <option value="AI & Research">
                                    <option value="Cybersecurity">
                                    <option value="Advisory">
                                    <option value="Operations">
                                </datalist>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio / Summary</label>
                            <textarea id="bio" name="bio" style="min-height: 90px;" placeholder="Brief professional overview, research focus, or background..."><?php echo $edit_member ? htmlspecialchars($edit_member['bio']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Profile Image</label>
                            <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp, image/gif">
                            <small style="color: #6c757d; display: block; margin-top: 4px;">Recommended: Square ratio (e.g. 800x800). Formats: JPG, PNG, WEBP.</small>
                            <?php if ($edit_member && !empty($edit_member['image'])): ?>
                                <div class="image-preview-box">
                                    <img src="../<?php echo htmlspecialchars($edit_member['image']); ?>" alt="Current Avatar" onerror="this.src='/img/logo-tp.png'">
                                    <div>
                                        <strong>Current Photo:</strong> <?= htmlspecialchars($edit_member['image']) ?>
                                        <div style="margin-top: 4px;">
                                            <label style="font-size: 0.85rem; font-weight: normal; cursor: pointer;">
                                                <input type="checkbox" name="remove_image" value="1"> Remove current photo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" id="email" name="email" placeholder="name@kaminovaglobal.com"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['email']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                                <input type="url" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/username"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['linkedin']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="twitter"><i class="fab fa-twitter"></i> X / Twitter URL</label>
                                <input type="url" id="twitter" name="twitter" placeholder="https://x.com/username"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['twitter']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="github"><i class="fab fa-github"></i> GitHub URL</label>
                                <input type="url" id="github" name="github" placeholder="https://github.com/username"
                                       value="<?php echo $edit_member ? htmlspecialchars($edit_member['github'] ?? '') : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center; margin-top: 0.5rem;">
                            <div class="form-group">
                                <label for="display_order">Display Order (Lower numbers appear first)</label>
                                <input type="number" id="display_order" name="display_order" 
                                       value="<?php echo $edit_member ? (int)$edit_member['display_order'] : 0; ?>">
                            </div>

                            <div class="form-group" style="padding-top: 1.5rem;">
                                <label for="is_active" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           <?php echo (!$edit_member || $edit_member['is_active']) ? 'checked' : ''; ?>
                                           style="width: 18px; height: 18px;">
                                    <strong>Active on Website</strong> (Visible on Team section/page)
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_member ? 'Update Team Member' : 'Add Team Member'; ?>
                            </button>
                            <?php if ($edit_member): ?>
                                <a href="team.php" class="btn btn-secondary">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Team Members List -->
                <div class="table-container" style="margin-top: 2rem;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 1.15rem; color: #1a1f3c;"><i class="fas fa-users-cog"></i> Existing Team Members (<?= count($team_members) ?>)</h3>
                    </div>

                    <?php if (empty($team_members)): ?>
                        <div style="padding: 3rem; text-align: center; color: #6c757d;">
                            <i class="fas fa-users fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                            <p>No team members found. Use the form above to add your first member.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Bio Excerpt</th>
                                    <th>Media Links</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($team_members as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="member-info-cell">
                                                <?php if (!empty($member['image'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($member['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                                         class="avatar-cell"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="avatar-placeholder" style="display: none;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="avatar-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="member-meta">
                                                    <strong><?php echo htmlspecialchars($member['name']); ?></strong>
                                                    <span><?php echo htmlspecialchars($member['position']); ?></span>
                                                    <?php if (!empty($member['department'])): ?>
                                                        <span style="display: inline-block; margin-top: 3px; font-size: 0.72rem; background: rgba(0, 102, 255, 0.1); color: var(--primary-blue); padding: 1px 7px; border-radius: 4px; font-weight: 600;"><?php echo htmlspecialchars($member['department']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="max-width: 250px; font-size: 0.85rem; color: #555;">
                                            <?php 
                                            $bio_text = $member['bio'] ?? '';
                                            echo htmlspecialchars(mb_strimwidth($bio_text, 0, 75, '...')); 
                                            ?>
                                        </td>
                                        <td>
                                            <div class="social-link-chips">
                                                <?php if (!empty($member['email'])): ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="social-chip" title="Email: <?php echo htmlspecialchars($member['email']); ?>">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($member['linkedin'])): ?>
                                                    <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="social-chip" title="LinkedIn">
                                                        <i class="fab fa-linkedin-in"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($member['twitter'])): ?>
                                                    <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank" rel="noopener noreferrer" class="social-chip" title="X / Twitter">
                                                        <i class="fab fa-twitter"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($member['github'])): ?>
                                                    <a href="<?php echo htmlspecialchars($member['github']); ?>" target="_blank" rel="noopener noreferrer" class="social-chip" title="GitHub">
                                                        <i class="fab fa-github"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (empty($member['email']) && empty($member['linkedin']) && empty($member['twitter']) && empty($member['github'])): ?>
                                                    <span style="font-size: 0.8rem; color: #aaa;">None</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><strong><?php echo (int)$member['display_order']; ?></strong></td>
                                        <td>
                                            <span class="<?php echo $member['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                                <?php echo $member['is_active'] ? '<i class="fas fa-check"></i> Active' : '<i class="fas fa-eye-slash"></i> Hidden'; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a href="?edit=<?php echo $member['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($member['name'])); ?>?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/admin.js"></script>
</body>
</html>