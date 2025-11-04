<?php
// Comprehensive update test script
session_start();
require_once 'config/database.php';

echo "<h1>Update Process Diagnostic Test</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $db = getDB();
    if ($db) {
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Test database selection
        $result = $db->query("SELECT DATABASE() as current_db");
        $current_db = $result->fetch();
        echo "<p class='info'>Current database: " . ($current_db['current_db'] ?? 'None') . "</p>";
        
        if ($current_db['current_db'] !== 'kaminova_labs') {
            echo "<p class='error'>❌ Wrong database selected!</p>";
        }
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check if tables exist
echo "<h2>2. Table Structure Test</h2>";
$required_tables = ['site_settings', 'blog_posts', 'projects', 'team_members', 'events', 'admin_users'];
foreach ($required_tables as $table) {
    try {
        $result = $db->query("DESCRIBE {$table}");
        $columns = $result->fetchAll();
        echo "<p class='success'>✅ Table '{$table}' exists with " . count($columns) . " columns</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Table '{$table}' missing or error: " . $e->getMessage() . "</p>";
    }
}

// Test 3: Site Settings Test
echo "<h2>3. Site Settings Update Test</h2>";
try {
    // Check current settings
    $settings = fetchAll("SELECT * FROM site_settings");
    echo "<p class='info'>Found " . count($settings) . " settings in database</p>";
    
    // Test insert/update cycle
    $test_key = 'test_update_' . time();
    $original_value = 'Original Test Value ' . date('H:i:s');
    $updated_value = 'Updated Test Value ' . date('H:i:s');
    
    echo "<h3>Testing INSERT operation:</h3>";
    $insert_id = insertRecord('site_settings', [
        'setting_key' => $test_key,
        'setting_value' => $original_value,
        'setting_type' => 'text',
        'description' => 'Test setting for diagnostics'
    ]);
    
    if ($insert_id) {
        echo "<p class='success'>✅ INSERT successful (ID: {$insert_id})</p>";
        
        // Verify insert
        $inserted = fetchOne("SELECT * FROM site_settings WHERE id = :id", ['id' => $insert_id]);
        if ($inserted && $inserted['setting_value'] === $original_value) {
            echo "<p class='success'>✅ INSERT verification successful</p>";
        } else {
            echo "<p class='error'>❌ INSERT verification failed</p>";
            echo "<pre>" . print_r($inserted, true) . "</pre>";
        }
        
        echo "<h3>Testing UPDATE operation:</h3>";
        $rows_affected = updateRecord('site_settings', 
            ['setting_value' => $updated_value], 
            'id = :id', 
            ['id' => $insert_id]
        );
        
        echo "<p class='info'>Update returned: {$rows_affected} rows affected</p>";
        
        if ($rows_affected > 0) {
            echo "<p class='success'>✅ UPDATE successful</p>";
            
            // Verify update
            $updated = fetchOne("SELECT * FROM site_settings WHERE id = :id", ['id' => $insert_id]);
            if ($updated && $updated['setting_value'] === $updated_value) {
                echo "<p class='success'>✅ UPDATE verification successful</p>";
                echo "<p class='info'>Value changed from: '{$original_value}' to: '{$updated['setting_value']}'</p>";
            } else {
                echo "<p class='error'>❌ UPDATE verification failed</p>";
                echo "<p class='error'>Expected: '{$updated_value}'</p>";
                echo "<p class='error'>Got: '" . ($updated['setting_value'] ?? 'NULL') . "'</p>";
                echo "<pre>" . print_r($updated, true) . "</pre>";
            }
        } else {
            echo "<p class='error'>❌ UPDATE failed - no rows affected</p>";
        }
        
        // Test update by setting_key (like in admin panel)
        echo "<h3>Testing UPDATE by setting_key (Admin Panel Method):</h3>";
        $final_value = 'Final Test Value ' . date('H:i:s');
        $rows_affected2 = updateRecord('site_settings', 
            ['setting_value' => $final_value], 
            'setting_key = :key', 
            ['key' => $test_key]
        );
        
        echo "<p class='info'>Update by key returned: {$rows_affected2} rows affected</p>";
        
        if ($rows_affected2 > 0) {
            echo "<p class='success'>✅ UPDATE by setting_key successful</p>";
            
            // Verify final update
            $final = fetchOne("SELECT * FROM site_settings WHERE setting_key = :key", ['key' => $test_key]);
            if ($final && $final['setting_value'] === $final_value) {
                echo "<p class='success'>✅ Final UPDATE verification successful</p>";
            } else {
                echo "<p class='error'>❌ Final UPDATE verification failed</p>";
                echo "<pre>" . print_r($final, true) . "</pre>";
            }
        } else {
            echo "<p class='error'>❌ UPDATE by setting_key failed</p>";
        }
        
        // Clean up
        deleteRecord('site_settings', 'id = :id', ['id' => $insert_id]);
        echo "<p class='info'>Test record cleaned up</p>";
        
    } else {
        echo "<p class='error'>❌ INSERT failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Site settings test error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 4: Simulate Admin Panel Update
echo "<h2>4. Admin Panel Simulation Test</h2>";
try {
    // Simulate the exact process from settings.php
    echo "<h3>Simulating settings form submission:</h3>";
    
    $test_data = [
        'action' => 'update_settings',
        'setting_contact_phone' => '+234 123 456 789 (TEST)',
        'setting_contact_email' => 'test@kaminovalabs.com',
        'setting_stats_clients' => '999',
        'setting_company_name' => 'Kaminova Labs (TEST)'
    ];
    
    $updated_count = 0;
    foreach ($test_data as $key => $value) {
        if ($key != 'action' && strpos($key, 'setting_') === 0) {
            $setting_key = substr($key, 8); // Remove 'setting_' prefix
            echo "<p class='info'>Processing: {$setting_key} = {$value}</p>";
            
            // Check if setting exists
            $existing = fetchOne("SELECT id FROM site_settings WHERE setting_key = :key", ['key' => $setting_key]);
            
            if ($existing) {
                echo "<p class='info'>Setting exists, updating...</p>";
                $rows_affected = updateRecord('site_settings', 
                    ['setting_value' => $value], 
                    'setting_key = :key', 
                    ['key' => $setting_key]
                );
                echo "<p class='info'>Rows affected: {$rows_affected}</p>";
                $updated_count += $rows_affected;
            } else {
                echo "<p class='warning'>Setting doesn't exist, creating...</p>";
                insertRecord('site_settings', [
                    'setting_key' => $setting_key,
                    'setting_value' => $value,
                    'setting_type' => 'text',
                    'description' => ucfirst(str_replace('_', ' ', $setting_key))
                ]);
                $updated_count++;
            }
        }
    }
    
    echo "<p class='success'>✅ Admin panel simulation completed. {$updated_count} settings processed.</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Admin panel simulation error: " . $e->getMessage() . "</p>";
}

// Test 5: Check current settings values
echo "<h2>5. Current Settings Values</h2>";
try {
    $current_settings = fetchAll("SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key");
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Setting Key</th><th>Current Value</th></tr>";
    foreach ($current_settings as $setting) {
        echo "<tr><td>{$setting['setting_key']}</td><td>{$setting['setting_value']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error fetching current settings: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Diagnostic Summary</h2>";
echo "<p><a href='admin/settings.php'>Go to Admin Settings</a> | ";
echo "<a href='admin/debug.php'>Go to Debug Page</a> | ";
echo "<a href='test_db.php'>Run Basic DB Test</a></p>";
?>