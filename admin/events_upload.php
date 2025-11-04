<?php
// Admin handler to upload event images and save event data
// Expects a form POST with enctype="multipart/form-data"

require_once __DIR__ . '/../config/database.php';

function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    $db = getDB();

    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $event_type = trim($_POST['event_type'] ?? ''); // 'upcoming' or 'past'
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $speakers = trim($_POST['speakers'] ?? '');
    $registration_link = trim($_POST['registration_link'] ?? '');
    $watch_link = trim($_POST['watch_link'] ?? '');
    $x_link = trim($_POST['x_link'] ?? '');
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

    if ($title === '' || $slug === '' || ($event_type !== 'upcoming' && $event_type !== 'past')) {
        jsonResponse(false, 'Missing required fields: title, slug, event_type');
    }

    // Handle image upload if provided
    $image_url = null;
    if (!empty($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if (!isset($allowed[$mime])) {
            jsonResponse(false, 'Invalid image type. Allowed: JPG, PNG, WEBP');
        }
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            jsonResponse(false, 'Image too large. Max 5MB');
        }
        $ext = $allowed[$mime];
        $safeSlug = preg_replace('/[^a-z0-9-_]/i', '-', strtolower($slug));
        $uploadDir = __DIR__ . '/../uploads/events';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = $safeSlug . '-' . time() . '.' . $ext;
        $destPath = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
            jsonResponse(false, 'Failed to move uploaded file');
        }
        // Public path relative to web root
        $image_url = '/uploads/events/' . $filename;
    }

    // Check if event exists by slug
    $stmt = $db->prepare('SELECT id, image_url FROM events WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update
        $sql = 'UPDATE events SET 
            title = :title,
            event_type = :event_type,
            event_date = :event_date,
            event_time = :event_time,
            location = :location,
            description = :description,
            speakers = :speakers,
            registration_link = :registration_link,
            watch_link = :watch_link,
            x_link = :x_link,
            is_active = :is_active' . ($image_url ? ', image_url = :image_url' : '') . '
            WHERE slug = :slug';
        $params = [
            'title' => $title,
            'event_type' => $event_type,
            'event_date' => $event_date ?: null,
            'event_time' => $event_time ?: null,
            'location' => $location ?: null,
            'description' => $description ?: null,
            'speakers' => $speakers ?: null,
            'registration_link' => $registration_link ?: null,
            'watch_link' => $watch_link ?: null,
            'x_link' => $x_link ?: null,
            'is_active' => $is_active,
            'slug' => $slug,
        ];
        if ($image_url) { $params['image_url'] = $image_url; }
        $upd = $db->prepare($sql);
        $upd->execute($params);
        jsonResponse(true, 'Event updated successfully', ['slug' => $slug, 'image_url' => $image_url ?: $existing['image_url']]);
    } else {
        // Insert
        $sql = 'INSERT INTO events (title, slug, event_type, event_date, event_time, location, description, speakers, registration_link, watch_link, x_link, image_url, is_active, created_at) 
                VALUES (:title, :slug, :event_type, :event_date, :event_time, :location, :description, :speakers, :registration_link, :watch_link, :x_link, :image_url, :is_active, NOW())';
        $params = [
            'title' => $title,
            'slug' => $slug,
            'event_type' => $event_type,
            'event_date' => $event_date ?: null,
            'event_time' => $event_time ?: null,
            'location' => $location ?: null,
            'description' => $description ?: null,
            'speakers' => $speakers ?: null,
            'registration_link' => $registration_link ?: null,
            'watch_link' => $watch_link ?: null,
            'x_link' => $x_link ?: null,
            'image_url' => $image_url,
            'is_active' => $is_active,
        ];
        $ins = $db->prepare($sql);
        $ins->execute($params);
        jsonResponse(true, 'Event created successfully', ['slug' => $slug, 'image_url' => $image_url]);
    }

} catch (Exception $e) {
    jsonResponse(false, $e->getMessage());
}
?>