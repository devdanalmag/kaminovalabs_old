<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$response = ['success' => false, 'data' => null, 'error' => null];

try {
    $type = $_GET['type'] ?? '';
    
    switch ($type) {
        case 'settings':
            $settings = fetchAll("SELECT setting_key, setting_value FROM site_settings");
            $settings_array = [];
            foreach ($settings as $setting) {
                $settings_array[$setting['setting_key']] = $setting['setting_value'];
            }
            $response['data'] = $settings_array;
            $response['success'] = true;
            break;
            
        case 'blog_posts':
            $limit = $_GET['limit'] ?? 10;
            $status = $_GET['status'] ?? 'published';
            
            $sql = "SELECT * FROM blog_posts WHERE status = :status ORDER BY created_at DESC LIMIT :limit";
            $params = ['status' => $status];
            
            $stmt = getDB()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $blog_posts = $stmt->fetchAll();
            $response['data'] = $blog_posts;
            $response['success'] = true;
            break;
            
        case 'projects':
            $category = $_GET['category'] ?? '';
            $active_only = $_GET['active_only'] ?? true;
            
            $sql = "SELECT * FROM projects";
            $params = [];
            $conditions = [];
            
            if ($active_only) {
                $conditions[] = "is_active = 1";
            }
            
            if ($category) {
                $conditions[] = "category = :category";
                $params['category'] = $category;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY display_order ASC, created_at DESC";
            
            $projects = fetchAll($sql, $params);
            
            // Decode JSON features for each project
            foreach ($projects as &$project) {
                if ($project['features']) {
                    $project['features'] = json_decode($project['features'], true) ?: [];
                }
            }
            
            $response['data'] = $projects;
            $response['success'] = true;
            break;
            
        case 'team_members':
            $active_only = $_GET['active_only'] ?? true;
            
            $sql = "SELECT * FROM team_members";
            if ($active_only) {
                $sql .= " WHERE is_active = 1";
            }
            $sql .= " ORDER BY display_order ASC, created_at DESC";
            
            $team_members = fetchAll($sql);
            $response['data'] = $team_members;
            $response['success'] = true;
            break;
            
        case 'events':
            $event_type = $_GET['event_type'] ?? '';
            $active_only = $_GET['active_only'] ?? true;
            $limit = $_GET['limit'] ?? 10;
            
            $sql = "SELECT * FROM events";
            $params = [];
            $conditions = [];
            
            if ($active_only) {
                $conditions[] = "is_active = 1";
            }
            
            if ($event_type) {
                $conditions[] = "event_type = :event_type";
                $params['event_type'] = $event_type;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY event_date DESC, created_at DESC LIMIT :limit";
            
            $stmt = getDB()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $events = $stmt->fetchAll();
            $response['data'] = $events;
            $response['success'] = true;
            break;
            
        case 'single_blog':
            $slug = $_GET['slug'] ?? '';
            if ($slug) {
                $blog_post = fetchOne("SELECT * FROM blog_posts WHERE slug = :slug AND status = 'published'", 
                                    ['slug' => $slug]);
                $response['data'] = $blog_post;
                $response['success'] = true;
            } else {
                $response['error'] = 'Slug parameter required';
            }
            break;
            
        case 'single_project':
            $slug = $_GET['slug'] ?? '';
            if ($slug) {
                $project = fetchOne("SELECT * FROM projects WHERE slug = :slug AND is_active = 1", 
                                  ['slug' => $slug]);
                if ($project && $project['features']) {
                    $project['features'] = json_decode($project['features'], true) ?: [];
                }
                $response['data'] = $project;
                $response['success'] = true;
            } else {
                $response['error'] = 'Slug parameter required';
            }
            break;
            
        default:
            $response['error'] = 'Invalid type parameter';
            break;
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>