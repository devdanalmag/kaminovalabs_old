<?php
require_once 'auth.php';
Auth::requireLogin();

$message = '';
$error = '';

// Auto-create table if not exists (defensive migration)
try {
    executeQuery("CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        title VARCHAR(255) DEFAULT NULL,
        category VARCHAR(100) DEFAULT 'Web3 & Blockchain',
        description TEXT DEFAULT NULL,
        logo VARCHAR(255) DEFAULT NULL,
        website_url VARCHAR(255) DEFAULT NULL,
        display_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    // Ignore if table already exists or permission issues
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = trim($_POST['name'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $category = trim($_POST['category'] ?? 'Web3 & Blockchain');
                $description = trim($_POST['description'] ?? '');
                $website_url = trim($_POST['website_url'] ?? '');
                $display_order = (int)($_POST['display_order'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($name)) {
                    $error = 'Partner Name is required.';
                    break;
                }

                // Handle file upload
                $logo = '';
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                    $upload_dir = '../uploads/partners/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_info = pathinfo($_FILES['logo']['name']);
                    $file_extension = strtolower($file_info['extension'] ?? '');
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $filename = 'partner_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                            $logo = 'uploads/partners/' . $filename;
                        } else {
                            $error = 'Failed to upload logo image.';
                            break;
                        }
                    } else {
                        $error = 'Invalid image file type. Allowed: jpg, png, svg, webp, gif.';
                        break;
                    }
                }
                
                try {
                    insertRecord('partners', [
                        'name' => $name,
                        'title' => $title,
                        'category' => $category,
                        'description' => $description,
                        'logo' => $logo,
                        'website_url' => $website_url,
                        'display_order' => $display_order,
                        'is_active' => $is_active
                    ]);
                    $message = 'Ecosystem Partner added successfully!';
                } catch (Exception $e) {
                    $error = 'Error adding partner: ' . $e->getMessage();
                }
                break;
                
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $category = trim($_POST['category'] ?? 'Web3 & Blockchain');
                $description = trim($_POST['description'] ?? '');
                $website_url = trim($_POST['website_url'] ?? '');
                $display_order = (int)($_POST['display_order'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($name)) {
                    $error = 'Partner Name is required.';
                    break;
                }

                $update_data = [
                    'name' => $name,
                    'title' => $title,
                    'category' => $category,
                    'description' => $description,
                    'website_url' => $website_url,
                    'display_order' => $display_order,
                    'is_active' => $is_active
                ];
                
                // Handle file upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                    $upload_dir = '../uploads/partners/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_info = pathinfo($_FILES['logo']['name']);
                    $file_extension = strtolower($file_info['extension'] ?? '');
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $filename = 'partner_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                            $update_data['logo'] = 'uploads/partners/' . $filename;
                        }
                    } else {
                        $error = 'Invalid image file type. Allowed: jpg, png, svg, webp, gif.';
                        break;
                    }
                }
                
                try {
                    $rows_affected = updateRecord('partners', $update_data, 'id = :id', ['id' => $id]);
                    $message = 'Ecosystem Partner updated successfully!';
                } catch (Exception $e) {
                    $error = 'Error updating partner: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                try {
                    deleteRecord('partners', 'id = :id', ['id' => $id]);
                    $message = 'Partner deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting partner: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get partner for editing
$edit_partner = null;
if (isset($_GET['edit'])) {
    $edit_partner = fetchOne("SELECT * FROM partners WHERE id = :id", ['id' => $_GET['edit']]);
}

// Search filter
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['cat'] ?? '');

$sql = "SELECT * FROM partners WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE :search OR title LIKE :search OR description LIKE :search)";
    $params['search'] = "%{$search}%";
}
if ($category_filter !== '') {
    $sql .= " AND category = :cat";
    $params['cat'] = $category_filter;
}

$sql .= " ORDER BY display_order ASC, created_at DESC";
$partners = fetchAll($sql, $params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecosystem Partners - Kaminova Labs Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="/img/logo-tp.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .partner-logo-preview {
            width: 70px;
            height: 45px;
            object-fit: contain;
            background: rgba(0, 26, 61, 0.4);
            border-radius: 6px;
            padding: 4px;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }
        .partner-card-preview {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-bar input, .filter-bar select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ced4da;
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
                <li>
                    <a href="team.php">
                        <i class="fas fa-users"></i>
                        <span>Team Members</span>
                    </a>
                </li>
                <li class="active">
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
                    <a href="debug.php">
                        <i class="fas fa-bug"></i>
                        <span>Debug Info</span>
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
                    <h1>Ecosystem Partners</h1>
                    <p>Manage partnerships, ecosystem collaborators & sponsor logos</p>
                </div>
                <div class="header-right">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="../partners.php" target="_blank" class="btn btn-secondary" style="margin-right: 10px;">
                        <i class="fas fa-external-link-alt"></i> View Public Page
                    </a>
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

                <!-- Partner Form -->
                <div class="form-container">
                    <h2><?php echo $edit_partner ? 'Edit Ecosystem Partner' : 'Add New Partner'; ?></h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_partner ? 'update' : 'create'; ?>">
                        <?php if ($edit_partner): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_partner['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="name">Partner Name *</label>
                                <input type="text" id="name" name="name" required placeholder="e.g. Chainlink, Polygon, CertiK"
                                       value="<?php echo $edit_partner ? htmlspecialchars($edit_partner['name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="title">Title / Role / Tagline</label>
                                <input type="text" id="title" name="title" placeholder="e.g. Decentralized Oracle & Data Network"
                                       value="<?php echo $edit_partner ? htmlspecialchars($edit_partner['title'] ?? '') : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category">
                                    <?php 
                                    $categories = ['Web3 & Blockchain', 'AI & Forensics', 'Security & Forensics', 'Strategic & Advisory', 'Academic & Research', 'Technology & Cloud'];
                                    $current_cat = $edit_partner['category'] ?? 'Web3 & Blockchain';
                                    foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($current_cat === $cat) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="website_url">Website URL</label>
                                <input type="url" id="website_url" name="website_url" placeholder="https://example.com"
                                       value="<?php echo $edit_partner ? htmlspecialchars($edit_partner['website_url'] ?? '') : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Partner Description</label>
                            <textarea id="description" name="description" placeholder="Brief summary of the partnership, joint initiatives, and technological collaboration..." style="min-height: 100px;"><?php echo $edit_partner ? htmlspecialchars($edit_partner['description'] ?? '') : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="logo">Partner Logo (Image / SVG)</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <?php if ($edit_partner && !empty($edit_partner['logo'])): ?>
                                <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                                    <span>Current logo:</span>
                                    <img src="../<?php echo htmlspecialchars($edit_partner['logo']); ?>" alt="Partner Logo" class="partner-logo-preview">
                                    <small style="color: #666;"><?php echo htmlspecialchars($edit_partner['logo']); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" 
                                       value="<?php echo $edit_partner ? (int)$edit_partner['display_order'] : 0; ?>">
                            </div>

                            <div class="form-group" style="display: flex; align-items: center; margin-top: 25px;">
                                <label for="is_active" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           <?php echo (!$edit_partner || $edit_partner['is_active']) ? 'checked' : ''; ?>>
                                    Active (Display on website)
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_partner ? 'Update Partner' : 'Add Partner'; ?>
                            </button>
                            <?php if ($edit_partner): ?>
                                <a href="partners.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Partners List Filter & Table -->
                <div class="table-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
                        <h2>All Ecosystem Partners (<?php echo count($partners); ?>)</h2>
                        <form method="GET" class="filter-bar" style="margin-bottom: 0;">
                            <input type="text" name="search" placeholder="Search partners..." value="<?php echo htmlspecialchars($search); ?>">
                            <select name="cat" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter === $cat) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" style="padding: 8px 15px;"><i class="fas fa-search"></i></button>
                            <?php if ($search !== '' || $category_filter !== ''): ?>
                                <a href="partners.php" class="btn btn-secondary" style="padding: 8px 15px;">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name & Title</th>
                                <th>Category</th>
                                <th>Website</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($partners)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #888;">
                                        No ecosystem partners found. Add your first partner above!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($partners as $partner): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($partner['logo'])): ?>
                                                <img src="../<?php echo htmlspecialchars($partner['logo']); ?>" 
                                                     alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                                     class="partner-logo-preview"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                                <i class="fas fa-handshake" style="display: none; font-size: 1.5rem; color: var(--primary-blue);"></i>
                                            <?php else: ?>
                                                <div style="width: 70px; height: 45px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 6px;">
                                                    <i class="fas fa-handshake" style="font-size: 1.3rem; color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($partner['name']); ?></strong>
                                            <?php if (!empty($partner['title'])): ?>
                                                <div style="font-size: 0.85rem; color: #666; margin-top: 2px;">
                                                    <?php echo htmlspecialchars($partner['title']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="display: inline-block; padding: 3px 8px; font-size: 0.8rem; border-radius: 12px; background: rgba(0, 102, 255, 0.1); color: var(--primary-blue);">
                                                <?php echo htmlspecialchars($partner['category'] ?? 'General'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($partner['website_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($partner['website_url']); ?>" target="_blank" style="color: var(--primary-blue); text-decoration: none; font-size: 0.9rem;">
                                                    <i class="fas fa-external-link-alt"></i> Visit
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #999; font-size: 0.85rem;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo (int)$partner['display_order']; ?></td>
                                        <td>
                                            <span class="status <?php echo $partner['is_active'] ? 'active' : 'draft'; ?>">
                                                <?php echo $partner['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a href="?edit=<?php echo $partner['id']; ?>" class="btn btn-primary" title="Edit Partner">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete partner <?php echo htmlspecialchars(addslashes($partner['name'])); ?>?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $partner['id']; ?>">
                                                <button type="submit" class="btn btn-danger" title="Delete Partner">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/admin.js"></script>
</body>
</html>
