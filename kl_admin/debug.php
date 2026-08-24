<?php
require_once 'auth.php';
Auth::requireLogin();
require_once '../config/database.php';

$debug_info = [];
$test_results = [];

// Test database connection
try {
    $db = getDB();
    if ($db) {
        $debug_info[] = "✅ Database connection successful";

        // Test if tables exist
        $tables = ['admin_users', 'blog_posts', 'projects', 'team_members', 'site_settings', 'events', 'contact_messages'];
        foreach ($tables as $table) {
            try {
                $result = fetchOne("SELECT COUNT(*) as count FROM {$table}");
                $debug_info[] = "✅ Table '{$table}' exists with {$result['count']} records";
            } catch (Exception $e) {
                $debug_info[] = "❌ Table '{$table}' error: " . $e->getMessage();
            }
        }

        // Test site settings
        try {
            $settings = fetchAll("SELECT * FROM site_settings");
            $debug_info[] = "✅ Site settings loaded: " . count($settings) . " settings found";

            // Test update operation
            $test_key = 'debug_test_' . time();
            $test_value = 'Test value ' . date('Y-m-d H:i:s');

            // Insert test setting
            $insert_id = insertRecord('site_settings', [
                'setting_key' => $test_key,
                'setting_value' => $test_value,
                'setting_type' => 'text',
                'description' => 'Debug test setting'
            ]);

            if ($insert_id) {
                $debug_info[] = "✅ Insert test successful (ID: {$insert_id})";

                // Test update
                $updated_value = 'Updated value ' . date('Y-m-d H:i:s');
                $rows_affected = updateRecord(
                    'site_settings',
                    ['setting_value' => $updated_value],
                    'setting_key = :key',
                    ['key' => $test_key]
                );

                if ($rows_affected > 0) {
                    $debug_info[] = "✅ Update test successful ({$rows_affected} rows affected)";
                } else {
                    $debug_info[] = "❌ Update test failed (0 rows affected)";
                }

                // Verify update
                $updated_record = fetchOne("SELECT * FROM site_settings WHERE setting_key = :key", ['key' => $test_key]);
                if ($updated_record && $updated_record['setting_value'] === $updated_value) {
                    $debug_info[] = "✅ Update verification successful";
                } else {
                    $debug_info[] = "❌ Update verification failed";
                }

                // Clean up test record
                deleteRecord('site_settings', 'setting_key = :key', ['key' => $test_key]);
                $debug_info[] = "✅ Test cleanup completed";
            } else {
                $debug_info[] = "❌ Insert test failed";
            }

        } catch (Exception $e) {
            $debug_info[] = "❌ Site settings test error: " . $e->getMessage();
        }

    } else {
        $debug_info[] = "❌ Database connection failed";
    }
} catch (Exception $e) {
    $debug_info[] = "❌ Database error: " . $e->getMessage();
}

// Test PHP configuration
$debug_info[] = "📋 PHP Version: " . phpversion();
$debug_info[] = "📋 PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No');
$debug_info[] = "📋 PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No');
$debug_info[] = "📋 Upload Max Filesize: " . ini_get('upload_max_filesize');
$debug_info[] = "📋 Post Max Size: " . ini_get('post_max_size');
$debug_info[] = "📋 Memory Limit: " . ini_get('memory_limit');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Information - Kaminova Labs Admin</title>
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
                <li>
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
                <li class="active">
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
                    <h1>Debug Information</h1>
                    <p>System diagnostics and database connectivity tests</p>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
                <div class="form-container">
                    <h2><i class="fas fa-database"></i> Database & System Tests</h2>

                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace;">
                        <?php foreach ($debug_info as $info): ?>
                            <div style="margin-bottom: 0.5rem; padding: 0.25rem;">
                                <?php echo htmlspecialchars($info); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 2rem;">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <button onclick="location.reload()" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Refresh Tests
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>