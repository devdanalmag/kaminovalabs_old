<?php
require_once 'auth.php';
Auth::requireLogin();

$user = Auth::getCurrentUser();

// Get statistics
$stats = [
    'blog_posts' => fetchOne("SELECT COUNT(*) as count FROM blog_posts")['count'],
    'projects' => fetchOne("SELECT COUNT(*) as count FROM projects")['count'],
    'team_members' => fetchOne("SELECT COUNT(*) as count FROM team_members")['count'],
    'events' => fetchOne("SELECT COUNT(*) as count FROM events")['count']
];

// Get recent blog posts
$recent_blogs = fetchAll("SELECT id, title, created_at, status FROM blog_posts ORDER BY created_at DESC LIMIT 5");

// Get recent projects
$recent_projects = fetchAll("SELECT id, title, created_at, status FROM projects ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kaminova Labs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="shortcut icon" href="/img/logo-tp.png" type="image/x-icon">
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
                <li class="active">
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
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</p>
                </div>
                <div class="header-right">
                    <span class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </span>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blog">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['blog_posts']; ?></h3>
                            <p>Blog Posts</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon projects">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['projects']; ?></h3>
                            <p>Projects</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon team">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['team_members']; ?></h3>
                            <p>Team Members</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon events">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['events']; ?></h3>
                            <p>Events</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Content -->
                <div class="recent-content">
                    <div class="recent-section">
                        <div class="section-header">
                            <h2>Recent Blog Posts</h2>
                            <a href="blog.php" class="btn btn-primary">View All</a>
                        </div>
                        <div class="recent-list">
                            <?php if (empty($recent_blogs)): ?>
                                <p class="no-data">No blog posts found. <a href="blog.php">Create your first post</a></p>
                            <?php else: ?>
                                <?php foreach ($recent_blogs as $blog): ?>
                                    <div class="recent-item">
                                        <div class="item-info">
                                            <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                                            <p><?php echo date('M j, Y', strtotime($blog['created_at'])); ?></p>
                                        </div>
                                        <span class="status <?php echo $blog['status']; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="recent-section">
                        <div class="section-header">
                            <h2>Recent Projects</h2>
                            <a href="projects.php" class="btn btn-primary">View All</a>
                        </div>
                        <div class="recent-list">
                            <?php if (empty($recent_projects)): ?>
                                <p class="no-data">No projects found. <a href="projects.php">Create your first project</a></p>
                            <?php else: ?>
                                <?php foreach ($recent_projects as $project): ?>
                                    <div class="recent-item">
                                        <div class="item-info">
                                            <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                                            <p><?php echo date('M j, Y', strtotime($project['created_at'])); ?></p>
                                        </div>
                                        <span class="status active">
                                            <?php echo htmlspecialchars($project['status']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>