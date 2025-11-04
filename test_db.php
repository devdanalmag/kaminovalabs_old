<?php
// Standalone database test
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>";

try {
    $db = getDB();
    if ($db) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test basic query
        $result = $db->query("SELECT 1 as test");
        if ($result) {
            echo "<p style='color: green;'>✅ Basic query test successful!</p>";
        }
        
        // Check if database exists
        $db_check = $db->query("SELECT DATABASE() as current_db");
        $current_db = $db_check->fetch();
        echo "<p>Current database: " . ($current_db['current_db'] ?? 'None') . "</p>";
        
        // List tables
        echo "<h2>Available Tables:</h2>";
        $tables = $db->query("SHOW TABLES");
        if ($tables) {
            while ($table = $tables->fetch()) {
                $table_name = array_values($table)[0];
                echo "<p>- {$table_name}</p>";
                
                // Count records in each table
                try {
                    $count_result = $db->query("SELECT COUNT(*) as count FROM `{$table_name}`");
                    $count = $count_result->fetch();
                    echo "<p style='margin-left: 20px;'>Records: {$count['count']}</p>";
                } catch (Exception $e) {
                    echo "<p style='margin-left: 20px; color: red;'>Error counting records: " . $e->getMessage() . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Could not list tables</p>";
        }
        
        // Test site_settings specifically
        echo "<h2>Site Settings Test:</h2>";
        try {
            $settings = fetchAll("SELECT * FROM site_settings LIMIT 5");
            echo "<p style='color: green;'>✅ Site settings query successful! Found " . count($settings) . " settings.</p>";
            
            foreach ($settings as $setting) {
                echo "<p>- {$setting['setting_key']}: {$setting['setting_value']}</p>";
            }
            
            // Test update
            echo "<h3>Testing Update Operation:</h3>";
            $test_key = 'test_update_' . time();
            
            // Insert test record
            $insert_result = insertRecord('site_settings', [
                'setting_key' => $test_key,
                'setting_value' => 'Original Value',
                'setting_type' => 'text',
                'description' => 'Test setting'
            ]);
            
            if ($insert_result) {
                echo "<p style='color: green;'>✅ Insert successful (ID: {$insert_result})</p>";
                
                // Update test record
                $update_result = updateRecord('site_settings', 
                    ['setting_value' => 'Updated Value'], 
                    'setting_key = :key', 
                    ['key' => $test_key]
                );
                
                echo "<p style='color: green;'>✅ Update result: {$update_result} rows affected</p>";
                
                // Verify update
                $verify = fetchOne("SELECT * FROM site_settings WHERE setting_key = :key", ['key' => $test_key]);
                if ($verify && $verify['setting_value'] === 'Updated Value') {
                    echo "<p style='color: green;'>✅ Update verification successful!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Update verification failed!</p>";
                }
                
                // Clean up
                deleteRecord('site_settings', 'setting_key = :key', ['key' => $test_key]);
                echo "<p style='color: green;'>✅ Cleanup completed</p>";
            } else {
                echo "<p style='color: red;'>❌ Insert failed</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Site settings error: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/'>Go to Admin Panel</a></p>";
echo "<p><a href='admin/debug.php'>Go to Debug Page</a></p>";
?>