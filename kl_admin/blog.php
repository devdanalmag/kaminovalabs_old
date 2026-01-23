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
                $excerpt = $_POST['excerpt'] ?? '';
                $content = $_POST['content'] ?? '';
                $category = $_POST['category'] ?? '';
                $author = $_POST['author'] ?? '';
                $status = $_POST['status'] ?? 'draft';

                // Handle file upload
                $featured_image = '';
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                    $upload_dir = '../uploads/blog/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                        $featured_image = 'uploads/blog/' . $filename;
                    }
                }

                try {
                    insertRecord('blog_posts', [
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'featured_image' => $featured_image,
                        'category' => $category,
                        'author' => $author,
                        'status' => $status
                    ]);
                    $message = 'Blog post created successfully!';
                } catch (Exception $e) {
                    $error = 'Error creating blog post: ' . $e->getMessage();
                }
                break;

            case 'update':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $slug = $_POST['slug'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $content = $_POST['content'] ?? '';
                $category = $_POST['category'] ?? '';
                $author = $_POST['author'] ?? '';
                $status = $_POST['status'] ?? 'draft';

                $update_data = [
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => $excerpt,
                    'content' => $content,
                    'category' => $category,
                    'author' => $author,
                    'status' => $status
                ];

                // Handle file upload
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                    $upload_dir = '../uploads/blog/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                        $update_data['featured_image'] = 'uploads/blog/' . $filename;
                    }
                }

                try {
                    $rows_affected = updateRecord('blog_posts', $update_data, 'id = :id', ['id' => $id]);
                    if ($rows_affected > 0) {
                        $message = 'Blog post updated successfully!';
                    } else {
                        $error = 'No changes were made to the blog post.';
                    }
                } catch (Exception $e) {
                    $error = 'Error updating blog post: ' . $e->getMessage();
                }
                break;

            case 'delete':
                $id = $_POST['id'] ?? 0;
                try {
                    deleteRecord('blog_posts', 'id = :id', ['id' => $id]);
                    $message = 'Blog post deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting blog post: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get blog post for editing
$edit_post = null;
if (isset($_GET['edit'])) {
    $edit_post = fetchOne("SELECT * FROM blog_posts WHERE id = :id", ['id' => $_GET['edit']]);
}

// Get all blog posts
$blog_posts = fetchAll("SELECT * FROM blog_posts ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Kaminova Labs Admin</title>
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
                <li class="active">
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
                    <h1>Blog Management</h1>
                    <p>Create and manage blog posts</p>
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

                <!-- Blog Form -->
                <div class="form-container">
                    <h2><?php echo $edit_post ? 'Edit Blog Post' : 'Create New Blog Post'; ?></h2>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_post ? 'update' : 'create'; ?>">
                        <?php if ($edit_post): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_post['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo $edit_post ? htmlspecialchars($edit_post['title']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug (URL-friendly)</label>
                            <input type="text" id="slug" name="slug" required
                                value="<?php echo $edit_post ? htmlspecialchars($edit_post['slug']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt"
                                name="excerpt"><?php echo $edit_post ? htmlspecialchars($edit_post['excerpt']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea id="content" name="content" required
                                style="min-height: 200px;"><?php echo $edit_post ? htmlspecialchars($edit_post['content']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category"
                                value="<?php echo $edit_post ? htmlspecialchars($edit_post['category']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" id="author" name="author"
                                value="<?php echo $edit_post ? htmlspecialchars($edit_post['author']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="featured_image">Featured Image</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*">
                            <?php if ($edit_post && $edit_post['featured_image']): ?>
                                <p>Current image: <?php echo htmlspecialchars($edit_post['featured_image']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft" <?php echo ($edit_post && $edit_post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($edit_post && $edit_post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_post ? 'Update' : 'Create'; ?> Blog Post
                            </button>
                            <?php if ($edit_post): ?>
                                <a href="blog.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Blog Posts List -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blog_posts as $post): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['category']); ?></td>
                                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                                    <td>
                                        <span class="status <?php echo $post['status']; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                    <td class="table-actions">
                                        <a href="?edit=<?php echo $post['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
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
        document.getElementById('title').addEventListener('input', function () {
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