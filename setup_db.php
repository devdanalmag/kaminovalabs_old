<?php
// Database setup script
echo "<h1>Kaminova Labs Database Setup</h1>";

try {
    // First, connect without specifying database to create it
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS kaminova_labs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database 'kaminova_labs' created/verified</p>";
    
    // Use the database
    $pdo->exec("USE kaminova_labs");
    echo "<p style='color: green;'>✅ Using database 'kaminova_labs'</p>";
    
    // Read and execute SQL file
    $sql_file = 'database/kaminova_labs.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql_content)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore duplicate entry errors for initial data
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "<p style='color: orange;'>⚠️ Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p style='color: green;'>✅ SQL file executed successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ SQL file not found: {$sql_file}</p>";
    }
    
    // Verify tables
    echo "<h2>Verifying Tables:</h2>";
    $tables = ['admin_users', 'blog_posts', 'projects', 'team_members', 'site_settings', 'events'];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $count = $result->fetch()['count'];
            echo "<p style='color: green;'>✅ Table '{$table}': {$count} records</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Table '{$table}': " . $e->getMessage() . "</p>";
        }
    }

    // Ensure new event columns exist (watch_link, x_link)
    echo "<h2>Checking Event Columns:</h2>";
    try {
        $colCheck = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'events' AND COLUMN_NAME = 'watch_link'");
        $colCheck->execute(['kaminova_labs']);
        $hasWatch = (int)$colCheck->fetch()['cnt'] > 0;

        if (!$hasWatch) {
            $pdo->exec("ALTER TABLE events ADD COLUMN watch_link VARCHAR(255) AFTER registration_link");
            echo "<p style='color: green;'>✅ Added 'watch_link' column to events</p>";
        } else {
            echo "<p style='color: green;'>✅ 'watch_link' column already exists</p>";
        }

        $colCheck2 = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'events' AND COLUMN_NAME = 'x_link'");
        $colCheck2->execute(['kaminova_labs']);
        $hasX = (int)$colCheck2->fetch()['cnt'] > 0;

        if (!$hasX) {
            $pdo->exec("ALTER TABLE events ADD COLUMN x_link VARCHAR(255) AFTER watch_link");
            echo "<p style='color: green;'>✅ Added 'x_link' column to events</p>";
        } else {
            echo "<p style='color: green;'>✅ 'x_link' column already exists</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠️ Could not verify/add event columns: " . $e->getMessage() . "</p>";
    }
    
    // Test admin user
    echo "<h2>Admin User Status:</h2>";
    try {
        $admin = $pdo->query("SELECT username, email FROM admin_users WHERE username = 'admin'")->fetch();
        if ($admin) {
            echo "<p style='color: green;'>✅ Admin user exists: {$admin['username']} ({$admin['email']})</p>";
            echo "<p><strong>Login credentials:</strong> admin / admin123</p>";
        } else {
            echo "<p style='color: red;'>❌ Admin user not found</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error checking admin user: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>🎉 Database setup completed successfully!</p>";
    echo "<p><a href='kl_admin/' style='color: blue;'>Go to Admin Panel</a></p>";
    echo "<p><a href='test_db.php' style='color: blue;'>Run Database Tests</a></p>";
    echo "<p><a href='index.html' style='color: blue;'>View Website</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please ensure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>MySQL is accessible on localhost:3306</li>";
    echo "<li>Root user has proper permissions</li>";
    echo "</ul>";
}
?>