<?php
// Admin sync script: Import projects and blog posts from projects.php and blog.php
// Reads original file contents and inserts/updates DB rows so UI uses DB as source of truth.

require_once __DIR__ . '/../config/database.php';

function db() {
    return getDB();
}

function upsert_blog_posts($pdo, $posts) {
    $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, author, category, status, created_at)
            VALUES (:title, :slug, :excerpt, :content, :author, :category, :status, :created_at)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                excerpt = VALUES(excerpt),
                content = VALUES(content),
                author = VALUES(author),
                category = VALUES(category),
                status = VALUES(status),
                created_at = VALUES(created_at)";
    $stmt = $pdo->prepare($sql);
    $count = 0;
    foreach ($posts as $p) {
        $stmt->execute([
            ':title' => $p['title'],
            ':slug' => $p['slug'],
            ':excerpt' => $p['excerpt'],
            ':content' => $p['content'],
            ':author' => $p['author'],
            ':category' => $p['category'],
            ':status' => 'published',
            ':created_at' => $p['created_at'],
        ]);
        $count++;
    }
    return $count;
}

function upsert_projects($pdo, $projects) {
    $sql = "INSERT INTO projects (title, slug, category, status, short_description, full_description, features, team_role, team_name, icon_class, is_active, display_order)
            VALUES (:title, :slug, :category, :status, :short_description, :full_description, :features, :team_role, :team_name, :icon_class, 1, :display_order)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                category = VALUES(category),
                status = VALUES(status),
                short_description = VALUES(short_description),
                full_description = VALUES(full_description),
                features = VALUES(features),
                team_role = VALUES(team_role),
                team_name = VALUES(team_name),
                icon_class = VALUES(icon_class),
                is_active = 1,
                display_order = VALUES(display_order)";
    $stmt = $pdo->prepare($sql);
    $count = 0;
    foreach ($projects as $p) {
        $stmt->execute([
            ':title' => $p['title'],
            ':slug' => $p['slug'],
            ':category' => $p['category'],
            ':status' => $p['status'],
            ':short_description' => $p['short_description'],
            ':full_description' => $p['full_description'],
            ':features' => json_encode($p['features'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':team_role' => $p['team_role'],
            ':team_name' => $p['team_name'],
            ':icon_class' => $p['icon_class'] ?? null,
            ':display_order' => $p['display_order'] ?? 0,
        ]);
        $count++;
    }
    return $count;
}

function parse_human_date($s) {
    // Convert strings like "January 15, 2025" to "2025-01-15 00:00:00"
    $dt = DateTime::createFromFormat('F j, Y', trim($s));
    if (!$dt) {
        // Try alternative formats just in case
        $ts = strtotime($s);
        if ($ts) return date('Y-m-d 00:00:00', $ts);
        return date('Y-m-d 00:00:00');
    }
    return $dt->format('Y-m-d 00:00:00');
}

function extract_html_section($content) {
    // Attempt to load only from <!DOCTYPE html> onwards to avoid PHP header
    $pos = strpos($content, '<!DOCTYPE html');
    return $pos !== false ? substr($content, $pos) : $content;
}

function extract_blog_posts_from_file($file_path) {
    $raw = file_get_contents($file_path);
    $html = extract_html_section($raw);

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $cards = $xpath->query("//section[@id='blog-posts']//div[contains(@class,'projects-grid')]//div[contains(@class,'project-card') and contains(@class,'blog-card')]");

    $posts = [];
    foreach ($cards as $card) {
        // Title
        $titleNode = (new DOMXPath($dom))->query(".//h3[contains(@class,'project-title')]", $card)->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : '';
        // Excerpt
        $descNode = (new DOMXPath($dom))->query(".//p[contains(@class,'project-desc')]", $card)->item(0);
        $excerpt = $descNode ? trim($descNode->textContent) : '';
        // Category
        $catNode = (new DOMXPath($dom))->query(".//span[contains(@class,'blog-category-tag')]", $card)->item(0);
        $category = $catNode ? trim($catNode->textContent) : '';
        // Date
        $dateNode = (new DOMXPath($dom))->query(".//span[contains(@class,'blog-date')]", $card)->item(0);
        $dateStr = $dateNode ? trim($dateNode->textContent) : '';
        $created_at = $dateStr ? parse_human_date($dateStr) : date('Y-m-d 00:00:00');
        // Author
        $authorNode = (new DOMXPath($dom))->query(".//div[contains(@class,'blog-author')]//p", $card)->item(0);
        $authorText = $authorNode ? trim($authorNode->textContent) : '';
        $author = preg_replace('/^Author:\s*/i', '', $authorText);
        // Slug from button data-blog
        $btnNode = (new DOMXPath($dom))->query(".//button[contains(@class,'read-more-btn')]", $card)->item(0);
        $slug = '';
        if ($btnNode) {
            foreach ($btnNode->attributes as $attr) {
                if ($attr->name === 'data-blog') { $slug = trim($attr->value); break; }
            }
        }
        if (!$slug) continue;

        $posts[] = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $excerpt, // Preserve available content exactly; use excerpt as content
            'author' => $author,
            'category' => $category,
            'created_at' => $created_at,
        ];
    }
    return $posts;
}

function extract_projects_from_file($file_path) {
    $raw = file_get_contents($file_path);
    $html = extract_html_section($raw);

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $categories = [];
    // Collect category titles mapped by section id
    foreach ($xpath->query("//section[contains(@class,'section')]//div[contains(@class,'project-category')]//h3[contains(@class,'category-title')]") as $h3) {
        $categoryTitle = trim($h3->textContent);
        // Find the ancestor section id
        $section = $h3->parentNode; // .project-category
        while ($section && !( $section->nodeName === 'section' && $section->hasAttribute('id') )) {
            $section = $section->parentNode;
        }
        $sectionId = $section && $section->hasAttribute('id') ? $section->getAttribute('id') : '';
        $categories[$sectionId] = $categoryTitle;
    }

    $projects = [];
    $displayOrder = 1;

    // Iterate each project card
    foreach ($xpath->query("//div[contains(@class,'project-card') and not(contains(@class,'blog-card'))]") as $card) {
        // Determine section/category
        $section = $card->parentNode; // .projects-grid
        while ($section && !( $section->nodeName === 'section' && $section->hasAttribute('id') )) {
            $section = $section->parentNode;
        }
        $sectionId = $section && $section->hasAttribute('id') ? $section->getAttribute('id') : '';
        $category = $categories[$sectionId] ?? '';

        // Title
        $titleNode = (new DOMXPath($dom))->query(".//h3[contains(@class,'project-title')]", $card)->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : '';
        // Status (preserve as-is, including any parentheses)
        $statusNode = (new DOMXPath($dom))->query(".//p[contains(@class,'project-status')]", $card)->item(0);
        $status = $statusNode ? trim($statusNode->textContent) : '';
        $status = trim($status);
        if (substr($status, 0, 1) === '(' && substr($status, -1) === ')') {
            // store without wrapping parentheses; UI adds parentheses dynamically
            $status = trim($status, '()');
        }
        // Short description
        $descNode = (new DOMXPath($dom))->query(".//p[contains(@class,'project-desc')]", $card)->item(0);
        $shortDesc = $descNode ? trim($descNode->textContent) : '';
        // Team role/name
        $teamP = (new DOMXPath($dom))->query(".//div[contains(@class,'project-team')]//p", $card)->item(0);
        $teamRole = '';
        $teamName = '';
        if ($teamP) {
            // Expect format: <strong>ROLE:</strong> NAME
            $inner = $teamP->C14N(); // canonical XML of the node to retain strong text
            // Extract role inside <strong>...
            if (preg_match('/<strong>([^<]+):<\/strong>\s*(.+)$/i', $inner, $m)) {
                $teamRole = trim(html_entity_decode($m[1]));
                $teamName = trim(strip_tags(html_entity_decode($m[2])));
            } else {
                // Fallback to plain text
                $text = trim($teamP->textContent);
                $parts = preg_split('/:\s*/', $text, 2);
                $teamRole = trim($parts[0] ?? '');
                $teamName = trim($parts[1] ?? '');
            }
        }
        // Icon class
        $iconNode = (new DOMXPath($dom))->query(".//div[contains(@class,'project-img')]//i", $card)->item(0);
        $iconClass = $iconNode && $iconNode->hasAttribute('class') ? trim($iconNode->getAttribute('class')) : null;
        // Slug from button data-project
        $btnNode = (new DOMXPath($dom))->query(".//button[contains(@class,'read-more-btn')]", $card)->item(0);
        $slug = '';
        if ($btnNode) {
            foreach ($btnNode->attributes as $attr) {
                if ($attr->name === 'data-project') { $slug = trim($attr->value); break; }
            }
        }
        if (!$slug) continue;

        $projects[$slug] = [
            'slug' => $slug,
            'title' => $title,
            'category' => $category,
            'status' => $status ?: 'Under Development',
            'short_description' => $shortDesc,
            'full_description' => '',
            'features' => [],
            'team_role' => $teamRole,
            'team_name' => $teamName,
            'icon_class' => $iconClass,
            'display_order' => $displayOrder++,
        ];
    }

    // Augment with fullDescription/features from JS const projectDetails
    // Extract JS block
    if (preg_match('/const\s+projectDetails\s*=\s*\{([\s\S]*?)\};/m', $html, $m)) {
        $block = $m[1];
        // Split by entries keyed like: key: { ... }
        // Find each key
        preg_match_all('/\n\s*([a-z0-9_]+)\s*:\s*\{([\s\S]*?)\}\s*,?/i', $block, $mm, PREG_SET_ORDER);
        foreach ($mm as $entry) {
            $key = trim($entry[1]);
            $obj = $entry[2];
            // fullDescription
            $fullDesc = '';
            if (preg_match('/fullDescription\s*:\s*"([\s\S]*?)"\s*,/i', $obj, $md)) {
                $fullDesc = html_entity_decode($md[1]);
            }
            // features array of strings
            $features = [];
            if (preg_match('/features\s*:\s*\[([\s\S]*?)\]/i', $obj, $mf)) {
                $arr = $mf[1];
                preg_match_all('/"([^"]+)"\s*,?/i', $arr, $fa);
                foreach ($fa[1] as $s) { $features[] = html_entity_decode($s); }
            }
            if (isset($projects[$key])) {
                $projects[$key]['full_description'] = $fullDesc;
                $projects[$key]['features'] = $features;
                // Prefer status from object if available
                if (preg_match('/status\s*:\s*"([^"]+)"/i', $obj, $ms)) {
                    $projects[$key]['status'] = html_entity_decode($ms[1]);
                }
                // Prefer category from object if available
                if (preg_match('/category\s*:\s*"([^"]+)"/i', $obj, $mc)) {
                    $projects[$key]['category'] = html_entity_decode($mc[1]);
                }
                // Prefer description from object if needed (keep short_description from card)
            }
        }
    }

    return array_values($projects);
}

function main() {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $blogFile = __DIR__ . '/../blog.php';
        $projFile = __DIR__ . '/../projects.php';

        $blogPosts = extract_blog_posts_from_file($blogFile);
        $projects = extract_projects_from_file($projFile);

        $blogCount = upsert_blog_posts($pdo, $blogPosts);
        $projCount = upsert_projects($pdo, $projects);

        $pdo->commit();

        header('Content-Type: text/html; charset=utf-8');
        echo '<h2>Synchronization Complete</h2>';
        echo '<p>Blog posts upserted: ' . intval($blogCount) . '</p>';
        echo '<p>Projects upserted: ' . intval($projCount) . '</p>';
        echo '<p><a href="/blog.php" target="_blank">View Blog</a> | <a href="/projects.php" target="_blank">View Projects</a></p>';
    } catch (Throwable $e) {
        $pdo->rollBack();
        header('Content-Type: text/plain; charset=utf-8');
        http_response_code(500);
        echo 'Error: ' . $e->getMessage();
    }
}

main();