<?php
// Force database setup and initialization
echo "<h1>Force Database Setup & Initialization</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
</style>";

try {
    // Connect to MySQL without database
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p class='success'>✅ Connected to MySQL server</p>";
    
    // Drop and recreate database to ensure clean state
    $pdo->exec("DROP DATABASE IF EXISTS kaminova_labs");
    echo "<p class='warning'>⚠️ Dropped existing database (if any)</p>";
    
    $pdo->exec("CREATE DATABASE kaminova_labs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✅ Created fresh database 'kaminova_labs'</p>";
    
    $pdo->exec("USE kaminova_labs");
    
    // Create tables manually to ensure they exist
    echo "<h2>Creating Tables:</h2>";
    
    // Admin users table
    $pdo->exec("
        CREATE TABLE admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created admin_users table</p>";
    
    // Site settings table
    $pdo->exec("
        CREATE TABLE site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value LONGTEXT,
            setting_type ENUM('text', 'textarea', 'email', 'phone', 'url', 'json') DEFAULT 'text',
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created site_settings table</p>";
    
    // Blog posts table
    $pdo->exec("
        CREATE TABLE blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            excerpt TEXT,
            content LONGTEXT NOT NULL,
            featured_image VARCHAR(255),
            category VARCHAR(100),
            author VARCHAR(100),
            status ENUM('draft', 'published') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created blog_posts table</p>";
    
    // Projects table
    $pdo->exec("
        CREATE TABLE projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            category VARCHAR(100) NOT NULL,
            status VARCHAR(100),
            short_description TEXT,
            full_description LONGTEXT,
            features JSON,
            team_role VARCHAR(100),
            team_name VARCHAR(100),
            project_image VARCHAR(255),
            icon_class VARCHAR(100),
            display_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created projects table</p>";
    
    // Team members table
    $pdo->exec("
        CREATE TABLE team_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(100) NOT NULL,
            bio TEXT,
            image VARCHAR(255),
            email VARCHAR(100),
            linkedin VARCHAR(255),
            twitter VARCHAR(255),
            display_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created team_members table</p>";
    
    // Events table
    $pdo->exec("
        CREATE TABLE events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            event_date DATE,
            event_time TIME,
            location VARCHAR(255),
            event_type ENUM('upcoming', 'past') DEFAULT 'upcoming',
            speakers TEXT,
            registration_link VARCHAR(255),
            watch_link VARCHAR(255),
            x_link VARCHAR(255),
            featured_image VARCHAR(255),
            icon_class VARCHAR(100),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p class='success'>✅ Created events table</p>";
    
    // Insert admin user with correct hashed password for 'admin123'
    echo "<h2>Inserting Default Data:</h2>";
    $adminPasswordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@kaminovalabs.com', $adminPasswordHash]);
    echo "<p class='success'>✅ Created admin user (kl_admin/kl_admin123)</p>";
    
    // Insert default site settings
    $default_settings = [
        ['company_name', 'Kaminova Labs', 'text', 'Company Name'],
        ['company_tagline', 'Pioneering Digital Innovation', 'text', 'Company Tagline'],
        ['contact_email', 'info@kaminovalabs.com', 'email', 'Contact Email'],
        ['contact_phone', '+234 xxxxxxxxxx', 'phone', 'Contact Phone'],
        ['contact_address', '123 Innovation Drive, Nigeria', 'text', 'Contact Address'],
        ['about_description', 'We are a multidisciplinary technology innovation company at the forefront of digital transformation.', 'textarea', 'About Description'],
        ['hero_title', 'Pioneering The Future of Digital Innovation', 'text', 'Hero Section Title'],
        ['hero_description', 'Transforming Industries Through Blockchain, AI, Cybersecurity & Digital Forensics. We deliver cutting-edge solutions to drive your business forward in the digital age.', 'textarea', 'Hero Section Description'],
        ['stats_clients', '150', 'text', 'Number of Clients'],
        ['stats_projects', '240', 'text', 'Number of Projects'],
        ['stats_team', '45', 'text', 'Team Members'],
        ['stats_years', '8', 'text', 'Years of Experience']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)");
    foreach ($default_settings as $setting) {
        $stmt->execute($setting);
    }
    echo "<p class='success'>✅ Inserted " . count($default_settings) . " default settings</p>";
    
    // Insert sample blog posts
    $pdo->exec("
        INSERT INTO blog_posts (title, slug, excerpt, content, category, author, status) VALUES
        ('The Future of AI in Blockchain', 'ai-blockchain-future', 'Exploring how AI transforms blockchain technology', 'Exploring how artificial intelligence is transforming blockchain technology, from smart contract optimization to predictive analytics in decentralized systems.', 'AI & Innovation', 'Dr. Sarah Chen', 'published'),
        ('Building Sustainable Web3', 'green-web3-sustainability', 'How blockchain combats climate change', 'How blockchain technology is being leveraged to combat climate change through carbon tokenization, renewable energy tracking, and sustainable supply chain management.', 'Sustainability', 'Michael Rodriguez', 'published')
    ");
    echo "<p class='success'>✅ Inserted sample blog posts</p>";
    
    // Insert sample projects
    $pdo->exec("
        INSERT INTO projects (title, slug, category, status, short_description, full_description, features, team_role, team_name, icon_class, display_order) VALUES
        ('GreenTrust Nexus', 'greentrust-nexus', 'BioFuture & Governance', 'Under Development', 'Climate, Trust & Tokenized Sustainability - A Web3 ecosystem for climate tokenization, trust networks, and incentive-driven governance.', 'A comprehensive Web3 ecosystem designed for climate tokenization, trust networks, and incentive-driven governance.', '[\"Climate tokenization and carbon credit management\", \"Trust network establishment for environmental projects\", \"Incentive-driven governance for sustainability initiatives\"]', 'Founder', 'Team Lead', 'fas fa-leaf', 1),
        ('NodeWhisper', 'nodewhisper', 'Forensics & Security', 'Under Development', 'Blockchain Forensics & Threat Intelligence Platform - Next-gen forensic tool with threat heatmaps, behavioral fingerprinting, and collaborative case rooms.', 'Next-generation forensic tool equipped with advanced threat heatmaps, behavioral fingerprinting, and collaborative case rooms.', '[\"Advanced threat heatmaps and visualization\", \"Behavioral fingerprinting for suspicious activities\", \"Collaborative case rooms for team investigations\"]', 'Founder', 'Team Lead', 'fas fa-search', 2)
    ");
    echo "<p class='success'>✅ Inserted sample projects</p>";
    
    // Test database operations
    echo "<h2>Testing Database Operations:</h2>";
    
    // Test insert
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)");
    $stmt->execute(['test_setting', 'test_value', 'text', 'Test setting']);
    $test_id = $pdo->lastInsertId();
    echo "<p class='success'>✅ Test insert successful (ID: {$test_id})</p>";
    
    // Test update
    $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE id = ?");
    $stmt->execute(['updated_test_value', $test_id]);
    $rows_affected = $stmt->rowCount();
    echo "<p class='success'>✅ Test update successful ({$rows_affected} rows affected)</p>";
    
    // Test select
    $stmt = $pdo->prepare("SELECT * FROM site_settings WHERE id = ?");
    $stmt->execute([$test_id]);
    $result = $stmt->fetch();
    if ($result && $result['setting_value'] === 'updated_test_value') {
        echo "<p class='success'>✅ Test select and verification successful</p>";
    } else {
        echo "<p class='error'>❌ Test verification failed</p>";
    }
    
    // Clean up test record
    $stmt = $pdo->prepare("DELETE FROM site_settings WHERE id = ?");
    $stmt->execute([$test_id]);
    echo "<p class='success'>✅ Test cleanup completed</p>";
    
    echo "<hr>";
    echo "<h2 class='success'>🎉 Database setup completed successfully!</h2>";
    echo "<p><strong>Admin Login:</strong> admin / admin123</p>";
    echo "<p><a href='kl_admin/'>Go to Admin Panel</a> | ";
    echo "<a href='check_updates.php'>Test Updates</a> | ";
    echo "<a href='index.html'>View Website</a></p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ General error: " . $e->getMessage() . "</p>";
}
?>