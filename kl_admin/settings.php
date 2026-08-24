<?php
require_once 'auth.php';
Auth::requireLogin();

$message = '';
$error = '';
$debug_info = [];

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $debug_info[] = "POST request received";
    $debug_info[] = "POST data: " . print_r($_POST, true);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_settings':
                try {
                    $debug_info[] = "Starting settings update process";
                    $updated_count = 0;
                    $processed_settings = [];

                    foreach ($_POST as $key => $value) {
                        if ($key != 'action' && strpos($key, 'setting_') === 0) {
                            $setting_key = substr($key, 8); // Remove 'setting_' prefix
                            $debug_info[] = "Processing setting: {$setting_key} = {$value}";

                            // Check if setting exists, if not create it
                            $existing = fetchOne("SELECT id FROM site_settings WHERE setting_key = :key", ['key' => $setting_key]);
                            $debug_info[] = "Existing record check: " . ($existing ? "Found ID " . $existing['id'] : "Not found");

                            if ($existing) {
                                $rows_affected = updateRecord(
                                    'site_settings',
                                    ['setting_value' => $value],
                                    'setting_key = :key',
                                    ['key' => $setting_key]
                                );
                                $debug_info[] = "Update result: {$rows_affected} rows affected";
                                $updated_count += $rows_affected;
                                $processed_settings[] = "{$setting_key}: updated ({$rows_affected} rows)";
                            } else {
                                // Insert new setting if it doesn't exist
                                $insert_id = insertRecord('site_settings', [
                                    'setting_key' => $setting_key,
                                    'setting_value' => $value,
                                    'setting_type' => 'text',
                                    'description' => ucfirst(str_replace('_', ' ', $setting_key))
                                ]);
                                $debug_info[] = "Insert result: ID {$insert_id}";
                                $updated_count++;
                                $processed_settings[] = "{$setting_key}: created (ID {$insert_id})";
                            }
                        }
                    }

                    $debug_info[] = "Total settings processed: {$updated_count}";
                    $message = "Settings updated successfully! ({$updated_count} settings modified)";
                    if (!empty($processed_settings)) {
                        $message .= "<br>Details: " . implode(", ", $processed_settings);
                    }
                } catch (Exception $e) {
                    $error = 'Error updating settings: ' . $e->getMessage();
                    $debug_info[] = "Exception: " . $e->getMessage();
                    $debug_info[] = "Stack trace: " . $e->getTraceAsString();
                }
                break;
        }
    } else {
        $debug_info[] = "No action specified in POST";
    }
} else {
    $debug_info[] = "GET request - displaying form";
}

// Get all settings
try {
    $settings = fetchAll("SELECT * FROM site_settings ORDER BY setting_key");
    $settings_array = [];
    foreach ($settings as $setting) {
        $settings_array[$setting['setting_key']] = $setting['setting_value'];
    }
    $debug_info[] = "Loaded " . count($settings) . " settings from database";
} catch (Exception $e) {
    $error = "Error loading settings: " . $e->getMessage();
    $debug_info[] = "Settings load error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Kaminova Labs Admin</title>
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
                <li class="active">
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
                    <h1>Site Settings</h1>
                    <p>Manage site configuration and content</p>
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
                        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Debug Information (remove in production) -->
                <?php if (!empty($debug_info)): ?>
                    <div class="form-container" style="background: #f0f8ff; border-left: 4px solid #007cba;">
                        <h3>Debug Information</h3>
                        <div style="font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                            <?php foreach ($debug_info as $info): ?>
                                <div style="margin-bottom: 5px; padding: 2px;"><?php echo htmlspecialchars($info); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Settings Form -->
                <form method="POST">
                    <input type="hidden" name="action" value="update_settings">

                    <!-- Company Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-building"></i> Company Information</h2>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="company_name">Company Name</label>
                                <input type="text" id="company_name" name="setting_company_name"
                                    value="<?php echo htmlspecialchars($settings_array['company_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="company_tagline">Company Tagline</label>
                                <input type="text" id="company_tagline" name="setting_company_tagline"
                                    value="<?php echo htmlspecialchars($settings_array['company_tagline'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="about_description">About Description</label>
                            <textarea id="about_description" name="setting_about_description"
                                style="min-height: 100px;"><?php echo htmlspecialchars($settings_array['about_description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-address-book"></i> Contact Information</h2>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="contact_email">Contact Email</label>
                                <input type="email" id="contact_email" name="setting_contact_email"
                                    value="<?php echo htmlspecialchars($settings_array['contact_email'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="contact_phone">Contact Phone</label>
                                <input type="text" id="contact_phone" name="setting_contact_phone"
                                    value="<?php echo htmlspecialchars($settings_array['contact_phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_address">Contact Address</label>
                            <input type="text" id="contact_address" name="setting_contact_address"
                                value="<?php echo htmlspecialchars($settings_array['contact_address'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Hero Section -->
                    <div class="form-container">
                        <h2><i class="fas fa-star"></i> Hero Section</h2>

                        <div class="form-group">
                            <label for="hero_title">Hero Title</label>
                            <input type="text" id="hero_title" name="setting_hero_title"
                                value="<?php echo htmlspecialchars($settings_array['hero_title'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="hero_description">Hero Description</label>
                            <textarea id="hero_description" name="setting_hero_description"
                                style="min-height: 100px;"><?php echo htmlspecialchars($settings_array['hero_description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="form-container">
                        <h2><i class="fas fa-chart-bar"></i> Statistics</h2>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="stats_clients">Number of Clients</label>
                                <input type="number" id="stats_clients" name="setting_stats_clients"
                                    value="<?php echo htmlspecialchars($settings_array['stats_clients'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="stats_projects">Number of Projects</label>
                                <input type="number" id="stats_projects" name="setting_stats_projects"
                                    value="<?php echo htmlspecialchars($settings_array['stats_projects'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="stats_team">Team Members</label>
                                <input type="number" id="stats_team" name="setting_stats_team"
                                    value="<?php echo htmlspecialchars($settings_array['stats_team'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="stats_years">Years of Experience</label>
                                <input type="number" id="stats_years" name="setting_stats_years"
                                    value="<?php echo htmlspecialchars($settings_array['stats_years'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="form-container">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/admin.js"></script>
</body>

</html>