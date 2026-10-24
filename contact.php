<?php
require_once __DIR__ . '/config/database.php';

$pdo = null;
try {
    $pdo = getDB();
} catch (Throwable $e) {
    $pdo = null;
}

if (!function_exists('getSetting')) {
    function getSetting($pdo, $key, $default = '')
    {
        if (!$pdo) return $default;
        try {
            $stmt = $pdo->prepare("SELECT setting_value AS v FROM site_settings WHERE setting_key = :k LIMIT 1");
            $stmt->execute([':k' => $key]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['v']) && $row['v'] !== null && $row['v'] !== '') {
                return $row['v'];
            }
        } catch (Throwable $e) {
        }
        return $default;
    }
}

$SITE_NAME = getSetting($pdo, 'company_name', 'KAMINOVA LABS');
$PHONE = getSetting($pdo, 'contact_phone', '+234 xxxxxxxxxx');
$EMAIL = getSetting($pdo, 'contact_email', 'info@kaminovaglobal.com');
$ADDRESS = getSetting($pdo, 'contact_address', '123 Innovation Drive, Nigeria');

// Fetch active team members
$team_members = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
        $stmt->execute();
        $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $team_members = [];
    }
}

if (!function_exists('resolveTeamDept')) {
    function resolveTeamDept($member) {
        if (!empty($member['department'])) return trim($member['department']);
        $pos = strtolower($member['position'] ?? '');
        if (strpos($pos, 'ceo') !== false || strpos($pos, 'chief executive') !== false || strpos($pos, 'founder') !== false || strpos($pos, 'co-founder') !== false || strpos($pos, 'director') !== false || strpos($pos, 'head') !== false) {
            return 'Leadership';
        }
        if (strpos($pos, 'ai') !== false || strpos($pos, 'bio') !== false || strpos($pos, 'research') !== false || strpos($pos, 'scientist') !== false) {
            return 'AI & Research';
        }
        if (strpos($pos, 'security') !== false || strpos($pos, 'cyber') !== false || strpos($pos, 'forensic') !== false || strpos($pos, 'audit') !== false || strpos($pos, 'crypto') !== false) {
            return 'Cybersecurity';
        }
        return 'Engineering';
    }
}

$team_departments = [];
foreach ($team_members as $m) {
    $d = resolveTeamDept($m);
    if (!in_array($d, $team_departments)) {
        $team_departments[] = $d;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?= htmlspecialchars($SITE_NAME) ?> | Direct Engineering & Inquiries Terminal</title>
    <meta name="description"
        content="Connect with Kaminova Labs. Engage our cryptography, artificial intelligence, cybersecurity, and digital forensics teams for enterprise advisory, partnership, and protocol integration.">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Contact Us - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="keywords"
        content="Contact Kaminova Labs, blockchain consultation, AI research collaboration, cybersecurity audit, Web3 partnership, digital forensics inquiry">
    <meta name="author" content="<?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kaminovaglobal.com/contact.php">
    <meta property="og:title" content="Contact Us - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="og:description"
        content="Engage our multidisciplinary engineering and research teams for enterprise blockchain, AI, and cybersecurity collaborations.">
    <meta property="og:image" content="https://kaminovaglobal.com/img/logo3d.png">
    <meta property="og:site_name" content="<?= htmlspecialchars($SITE_NAME) ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://kaminovaglobal.com/contact.php">
    <meta property="twitter:title" content="Contact Us - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="twitter:description"
        content="Engage our multidisciplinary engineering and research teams for enterprise blockchain, AI, and cybersecurity collaborations.">
    <meta property="twitter:image" content="https://kaminovaglobal.com/img/logo3d.png">

    <!-- Preload Critical Resources -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" as="script">
    <link rel="preload" href="styles.css" as="style">

    <!-- External Resources -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : '2.2' ?>">
    <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">
    <link rel="canonical" href="https://kaminovaglobal.com/contact.php">

    <style>
        /* Dedicated Contact Page Styles */
        .contact-page-hero {
            padding: 140px 5% 50px;
            text-align: center;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 50px;
            color: var(--electric-blue);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        }

        .contact-page-wrapper {
            max-width: 1300px;
            margin: 0 auto 5rem;
            padding: 0 5%;
        }

        .contact-layout-grid {
            display: grid;
            grid-template-columns: 1.25fr 0.95fr;
            gap: 40px;
            margin-bottom: 4rem;
        }

        .contact-terminal-card {
            background: linear-gradient(145deg, rgba(0, 26, 61, 0.7) 0%, rgba(0, 8, 20, 0.9) 100%);
            border: 1px solid rgba(0, 212, 255, 0.25);
            border-radius: 24px;
            padding: 40px;
            backdrop-filter: blur(14px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .contact-form-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 212, 255, 0.15);
        }

        .contact-form-header h2 {
            font-size: 1.5rem;
            color: var(--white);
            margin: 0;
            font-weight: 700;
        }

        .contact-form-header p {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.72);
            margin: 3px 0 0;
        }

        .contact-channels-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .contact-channel-card {
            background: linear-gradient(135deg, rgba(0, 26, 61, 0.5) 0%, rgba(0, 8, 20, 0.7) 100%);
            border: 1px solid rgba(0, 212, 255, 0.18);
            border-radius: 18px;
            padding: 24px;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: flex-start;
            gap: 20px;
            transition: all 0.35s ease;
            text-decoration: none;
            color: inherit;
        }

        .contact-channel-card:hover {
            border-color: rgba(0, 212, 255, 0.45);
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 212, 255, 0.15);
        }

        .channel-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            box-shadow: 0 6px 15px rgba(0, 102, 255, 0.3);
        }

        .channel-details h4 {
            font-size: 1.1rem;
            color: var(--white);
            margin-bottom: 0.3rem;
        }

        .channel-details p {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 0.3rem;
        }

        .channel-action {
            font-size: 0.85rem;
            color: var(--electric-blue);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sla-badge-box {
            background: rgba(0, 102, 255, 0.08);
            border: 1px dashed rgba(0, 212, 255, 0.3);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sla-badge-box i {
            font-size: 2rem;
            color: var(--electric-blue);
        }

        .sla-badge-box h5 {
            color: var(--white);
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .sla-badge-box p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Team Showcase Section */
        .team-showcase-section {
            margin: 5rem auto 3.5rem;
            max-width: 1350px;
            padding: 0 1.5rem;
            scroll-margin-top: 100px;
        }

        .team-section-header {
            text-align: center;
            margin-bottom: 2.2rem;
        }

        .team-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 22px;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 50px;
            color: var(--electric-blue);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.2rem;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.18);
        }

        .team-showcase-section .section-title {
            display: table !important;
            margin: 0 auto 1.2rem !important;
            left: 0 !important;
            transform: none !important;
            font-size: clamp(2rem, 3.5vw, 2.6rem);
            text-align: center;
        }

        .team-section-desc {
            text-align: center;
            color: rgba(255, 255, 255, 0.82);
            max-width: 740px;
            margin: 0 auto;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        /* Filter Tabs */
        .team-filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 2.2rem auto 2.8rem;
            max-width: 950px;
        }

        .team-filter-btn {
            background: rgba(0, 26, 61, 0.55);
            border: 1px solid rgba(0, 212, 255, 0.22);
            border-radius: 50px;
            padding: 9px 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .team-filter-btn:hover {
            color: #ffffff;
            border-color: rgba(0, 212, 255, 0.55);
            background: rgba(0, 212, 255, 0.12);
            transform: translateY(-2px);
        }

        .team-filter-btn.active {
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.35), rgba(0, 212, 255, 0.25));
            border-color: var(--electric-blue);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }

        .team-filter-btn .filter-count {
            background: rgba(0, 0, 0, 0.4);
            color: var(--electric-blue);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .team-filter-btn.active .filter-count {
            background: var(--electric-blue);
            color: #000814;
        }

        /* Organized Responsive Grid: Fixed Card Proportions */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 320px));
            justify-content: center;
            gap: 32px;
            max-width: 1350px;
            margin: 0 auto;
        }

        .team-card {
            background: linear-gradient(155deg, rgba(0, 26, 61, 0.75) 0%, rgba(0, 10, 24, 0.92) 100%);
            border: 1px solid rgba(0, 212, 255, 0.22);
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.4s ease, opacity 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(14px);
            width: 100%;
            max-width: 330px;
            margin: 0 auto;
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--electric-blue), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 2;
        }

        .team-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0, 212, 255, 0.6);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.65), 0 0 30px rgba(0, 212, 255, 0.25);
        }

        .team-card:hover::before {
            opacity: 1;
        }

        /* Photo Wrapper - Exact 290px Height to ensure uniform headshots */
        .team-card-image-wrap {
            position: relative;
            width: 100%;
            height: 290px;
            overflow: hidden;
            background: radial-gradient(circle at 50% 35%, #0c2752 0%, #020814 100%);
        }

        .team-card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: block;
        }

        .team-card:hover .team-card-image {
            transform: scale(1.05);
        }

        .team-card-dept-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(0, 12, 30, 0.85);
            border: 1px solid rgba(0, 212, 255, 0.35);
            backdrop-filter: blur(8px);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--electric-blue);
            letter-spacing: 0.5px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .team-card-fallback-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 40%, #0d3066 0%, #020914 100%);
        }

        .fallback-avatar-inner {
            text-align: center;
            color: var(--electric-blue);
        }

        .fallback-avatar-inner i {
            font-size: 3.5rem;
            margin-bottom: 8px;
            display: block;
            opacity: 0.9;
        }

        .fallback-avatar-inner span {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            background: rgba(0, 212, 255, 0.15);
            padding: 3px 12px;
            border-radius: 30px;
            border: 1px solid rgba(0, 212, 255, 0.3);
        }

        .team-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 10, 24, 0.98) 0%, rgba(0, 10, 24, 0.3) 35%, transparent 70%);
            pointer-events: none;
        }

        .team-card-body {
            padding: 1.4rem 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .team-member-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .team-member-role {
            font-size: 0.85rem;
            color: var(--electric-blue);
            font-weight: 600;
            margin-bottom: 0.85rem;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        .team-member-bio {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.55;
            margin-bottom: 1.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.9em;
        }

        .team-card-socials {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: auto;
        }

        .team-social-link {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(0, 212, 255, 0.25);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .team-social-link:hover {
            background: var(--electric-blue);
            color: #000814;
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        }

        /* Expanding Team CTA */
        .team-join-card {
            background: linear-gradient(135deg, rgba(0, 26, 61, 0.65) 0%, rgba(0, 10, 24, 0.85) 100%);
            border: 1px dashed rgba(0, 212, 255, 0.35);
            border-radius: 20px;
            padding: 2.2rem 2rem;
            max-width: 1000px;
            margin: 3.5rem auto 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .team-join-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--electric-blue);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.4rem;
        }

        .team-join-content h3 {
            font-size: 1.35rem;
            color: var(--white);
            margin-bottom: 0.35rem;
        }

        .team-join-content p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.75);
            margin: 0;
            max-width: 620px;
            line-height: 1.5;
        }

        .team-join-btn {
            flex-shrink: 0;
            padding: 12px 24px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 820px) {
            .team-join-card {
                flex-direction: column;
                text-align: center;
                padding: 1.8rem;
            }
            .team-join-content p {
                margin-bottom: 1rem;
            }
        }

        .contact-faq-section {
            margin: 4rem auto;
            max-width: 900px;
            padding: 0 1rem;
        }

        .faq-item {
            background: rgba(0, 26, 61, 0.4);
            border: 1px solid rgba(0, 212, 255, 0.15);
            border-radius: 14px;
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item.active {
            border-color: rgba(0, 212, 255, 0.4);
        }

        .faq-question {
            padding: 20px 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--white);
            user-select: none;
        }

        .faq-question:hover {
            color: var(--electric-blue);
        }

        .faq-question i {
            transition: transform 0.3s ease;
            color: var(--electric-blue);
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 24px 20px;
            display: none;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        @media (max-width: 992px) {
            .contact-layout-grid {
                grid-template-columns: 1fr;
            }
            .contact-terminal-card {
                padding: 28px;
            }
        }

        @media (max-width: 576px) {
            .contact-terminal-card {
                padding: 20px;
            }
            .channel-icon-box {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }
        }
    </style>

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Contact Kaminova Labs",
        "url": "https://kaminovaglobal.com/contact.php",
        "description": "Direct communications terminal for Kaminova Labs engineering, research, and enterprise advisory.",
        "publisher": {
            "@type": "Organization",
            "name": "<?= htmlspecialchars($SITE_NAME) ?>",
            "logo": "https://kaminovaglobal.com/img/logo3d.png",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "<?= htmlspecialchars($PHONE) ?>",
                "email": "<?= htmlspecialchars($EMAIL) ?>",
                "contactType": "Customer Support & Partnerships"
            }
        }
    }
    </script>
</head>

<body>
    <!-- 3D Canvas -->
    <canvas id="three-canvas"></canvas>

    <!-- Particle Background -->
    <canvas id="particles"></canvas>

    <!-- Geometric Background -->
    <div class="geometric-background"></div>

    <!-- Navigation Header -->
    <?php 
    $currentPage = 'contact';
    include 'includes/header.php'; 
    ?>

    <!-- Contact Page Hero -->
    <section class="contact-page-hero">
        <div class="contact-hero-badge">
            <i class="fas fa-envelope-open-text"></i> Get In Touch
        </div>
        <h1 class="hero-title" style="margin-bottom: 1.2rem;">Let's Connect & Collaborate</h1>
        <p class="hero-desc" style="max-width: 820px; margin: 0 auto; font-size: 1.15rem; color: rgba(255, 255, 255, 0.85);">
            Whether you have questions, partnership inquiries, or need technical expertise in blockchain, AI, or cybersecurity, our team is here to help.
        </p>
    </section>

    <!-- Main Contact Layout Section -->
    <section class="contact-page-wrapper">
        <div class="contact-layout-grid">
            
            <!-- Left Column: Contact Form -->
            <div class="contact-terminal-card">
                <div class="contact-form-header">
                    <div class="channel-icon-box" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h2>Contact Form</h2>
                        <p>Fill out the form below and our team will get back to you promptly.</p>
                    </div>
                </div>

                <form id="contactForm" class="contact-form" autocomplete="on">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name <span style="color: var(--electric-blue);">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address <span style="color: var(--electric-blue);">*</span></label>
                        <input type="email" id="email" name="email" placeholder="you@domain.com" required>
                    </div>

                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Subject <span style="color: var(--electric-blue);">*</span></label>
                        <input type="text" id="subject" name="subject" list="subject-suggestions" placeholder="Select or type subject" required>
                        <datalist id="subject-suggestions">
                            <option value="Enterprise Blockchain Architecture">
                            <option value="AI & BioFuture Systems Consultation">
                            <option value="Cybersecurity Audit & Threat Intelligence">
                            <option value="Ecosystem & Partnership Collaboration">
                            <option value="Research Sponsorship & Academic Alliance">
                            <option value="Career & Fellowship Opportunities">
                            <option value="General & Media Inquiry">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment-alt"></i> Message <span style="color: var(--electric-blue);">*</span></label>
                        <textarea id="message" name="message" placeholder="Type your message here..." required style="min-height: 160px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="padding: 14px 28px; font-size: 1.05rem;">
                        Send Message <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
                    </button>

                    <div id="formStatus" class="form-status" style="margin-top: 15px;"></div>
                </form>
            </div>

            <!-- Right Column: Direct Channels & Operational Details -->
            <div class="contact-channels-column">
                
                <!-- Headquarters Card -->
                <div class="contact-channel-card">
                    <div class="channel-icon-box">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="channel-details">
                        <h4>Global Headquarters</h4>
                        <p><?= htmlspecialchars($ADDRESS) ?></p>
                        <span class="channel-action"><i class="fas fa-globe-africa"></i> Physical & Virtual Operations</span>
                    </div>
                </div>

                <!-- Phone Channel Card -->
                <a href="tel:<?= htmlspecialchars($PHONE) ?>" class="contact-channel-card">
                    <div class="channel-icon-box">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="channel-details">
                        <h4>Direct Telephone</h4>
                        <p><?= htmlspecialchars($PHONE) ?></p>
                        <span class="channel-action">Mon - Fri, 08:00 - 18:00 (GMT+1) <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- Email Channel Card -->
                <a href="mailto:<?= htmlspecialchars($EMAIL) ?>" class="contact-channel-card">
                    <div class="channel-icon-box">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="channel-details">
                        <h4>Official Correspondence</h4>
                        <p><?= htmlspecialchars($EMAIL) ?></p>
                        <span class="channel-action">Direct Engineering Dispatch <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- SLA Guarantee Box -->
                <div class="sla-badge-box">
                    <i class="fas fa-shield-halved"></i>
                    <div>
                        <h5>24-Hour Response Protocol</h5>
                        <p>All institutional inquiries, audit requests, and research proposals are reviewed by designated technical leads within 24 business hours.</p>
                    </div>
                </div>

                <!-- Connect Socials Card -->
                <div class="contact-channel-card" style="display: block;">
                    <h4 style="margin-bottom: 0.5rem;"><i class="fas fa-network-wired" style="color: var(--electric-blue); margin-right: 8px;"></i> Follow & Connect</h4>
                    <p style="font-size: 0.88rem; color: rgba(255, 255, 255, 0.75); margin-bottom: 1.2rem;">
                        Track our open-source developments, security advisories, and ecosystem updates.
                    </p>
                    <div class="social-icons" style="gap: 12px;">
                        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://x.com" target="_blank" rel="noopener noreferrer" class="social-icon" title="X / Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="social-icon" title="GitHub"><i class="fab fa-github"></i></a>
                        <a href="https://telegram.org" target="_blank" rel="noopener noreferrer" class="social-icon" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
                    </div>
                </div>

            </div>

        </div>

        <!-- Leadership & Core Team Section -->
        <div id="team" class="team-showcase-section">
            <div class="team-section-header">
                <span class="team-hero-badge"><i class="fas fa-brain"></i> Multidisciplinary Team</span>
                <h2 class="section-title">Meet Our Team</h2>
                <p class="team-section-desc">
                    The visionary founders, cryptographers, AI architects, and cybersecurity specialists driving pioneering digital innovations at Kaminova Labs.
                </p>
            </div>

            <?php if (!empty($team_departments) && count($team_members) > 1): ?>
                <div class="team-filter-bar">
                    <button type="button" class="team-filter-btn active" data-filter="all">
                        <i class="fas fa-th-large"></i> All Members <span class="filter-count"><?= count($team_members) ?></span>
                    </button>
                    <?php foreach ($team_departments as $dept): 
                        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $dept));
                        $countDept = count(array_filter($team_members, fn($m) => resolveTeamDept($m) === $dept));
                    ?>
                        <button type="button" class="team-filter-btn" data-filter="<?= htmlspecialchars($slug) ?>">
                            <?= htmlspecialchars($dept) ?> <span class="filter-count"><?= $countDept ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($team_members)): ?>
                <div class="team-grid">
                    <?php foreach ($team_members as $member): 
                        $memberDept = resolveTeamDept($member);
                        $deptSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $memberDept));
                    ?>
                        <div class="team-card" data-department="<?= htmlspecialchars($deptSlug) ?>">
                            <div class="team-card-image-wrap">
                                <?php if (!empty($member['image'])): ?>
                                    <img src="<?= htmlspecialchars($member['image']) ?>" 
                                         alt="<?= htmlspecialchars($member['name']) ?>" 
                                         class="team-card-image"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="team-card-fallback-avatar" style="display: none;">
                                        <div class="fallback-avatar-inner">
                                            <i class="fas fa-user-astronaut"></i>
                                            <span><?= htmlspecialchars(mb_strtoupper(mb_substr($member['name'], 0, 2))) ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="team-card-fallback-avatar" style="display: flex;">
                                        <div class="fallback-avatar-inner">
                                            <i class="fas fa-user-astronaut"></i>
                                            <span><?= htmlspecialchars(mb_strtoupper(mb_substr($member['name'], 0, 2))) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="team-card-dept-tag">
                                    <i class="fas fa-layer-group"></i> <?= htmlspecialchars($memberDept) ?>
                                </span>
                                <div class="team-image-overlay"></div>
                            </div>

                            <div class="team-card-body">
                                <h3 class="team-member-name"><?= htmlspecialchars($member['name']) ?></h3>
                                <div class="team-member-role"><?= htmlspecialchars($member['position']) ?></div>
                                
                                <?php if (!empty($member['bio'])): ?>
                                    <p class="team-member-bio" title="<?= htmlspecialchars($member['bio']) ?>">
                                        <?= htmlspecialchars($member['bio']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="team-card-socials">
                                    <?php if (!empty($member['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="team-social-link" title="Email <?= htmlspecialchars($member['name']) ?>">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($member['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="team-social-link" title="LinkedIn Profile">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['twitter'])): ?>
                                        <a href="<?= htmlspecialchars($member['twitter']) ?>" target="_blank" rel="noopener noreferrer" class="team-social-link" title="X / Twitter Profile">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['github'])): ?>
                                        <a href="<?= htmlspecialchars($member['github']) ?>" target="_blank" rel="noopener noreferrer" class="team-social-link" title="GitHub Profile">
                                            <i class="fab fa-github"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Expanding Team CTA Card -->
                <div class="team-join-card">
                    <div class="team-join-content">
                        <span class="team-join-badge"><i class="fas fa-sparkles"></i> Expanding Team</span>
                        <h3>Want to Build High-Impact Technology With Us?</h3>
                        <p>We are continuously onboarding gifted minds across cryptography, autonomous security, AI bio-sustainability, and protocol engineering.</p>
                    </div>
                    <a href="contact.php?subject=Career%20%26%20Fellowship%20Opportunities" class="btn btn-primary team-join-btn">
                        Join Our Team <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 1rem; color: rgba(255, 255, 255, 0.65);">
                    <i class="fas fa-users fa-3x" style="color: var(--electric-blue); opacity: 0.5; margin-bottom: 1rem;"></i>
                    <p>Team roster updating. Please check back shortly.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Interactive FAQ Section -->
        <div class="contact-faq-section">
            <h2 class="section-title" style="font-size: 2rem;">Frequently Asked Questions</h2>
            
            <div class="faq-item active">
                <div class="faq-question">
                    <span>How do we request a smart contract or cybersecurity audit?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    You can submit an inquiry via our contact form above selecting "Cybersecurity Audit & Threat Intelligence". Please include repository links, architectural documentation, and desired timelines. Our forensics team will provide an initial vulnerability scope and quote within 24 hours.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>How does Kaminova Labs engage in ecosystem partnerships?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    We collaborate closely with leading blockchain protocols, academic institutes, and enterprise cloud providers. Explore our <a href="partners.php" style="color: var(--electric-blue); text-decoration: underline;">Ecosystem Partners</a> page to see active collaborations or submit a proposal here for technical integration.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Are your research projects available for pilot testing?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Yes! Projects like GreenTrust Nexus and NodeWhisper frequently offer pilot sandboxes and developer testnets for verified enterprise partners and academic researchers. Indicate your organization's testing requirements in your message.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Can we invite Kaminova Labs leadership to speak at technical summits?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Our founders and researchers regularly keynote global Web3 summits, AI symposiums, and cybersecurity conferences. Check our past and upcoming events on the <a href="event.php" style="color: var(--electric-blue); text-decoration: underline;">Events</a> page and select "General & Media Inquiry" to submit speaking invitations.
                </div>
            </div>
        </div>

    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Main scripts -->
    <script src="js/script.js"></script>
    <script>
        // Interactive Team Department Filter Logic
        const filterBtns = document.querySelectorAll('.team-filter-btn');
        const teamCards = document.querySelectorAll('.team-card[data-department]');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const targetFilter = btn.getAttribute('data-filter');

                teamCards.forEach(card => {
                    const cardDept = card.getAttribute('data-department');
                    if (targetFilter === 'all' || cardDept === targetFilter) {
                        card.style.display = 'flex';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(15px)';
                        setTimeout(() => {
                            card.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 20);
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Interactive FAQ accordion logic
        document.querySelectorAll('.faq-question').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                const wasActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!wasActive) {
                    item.classList.add('active');
                }
            });
        });

        // Pre-populate subject from URL param if available (e.g. ?subject=Career)
        const urlParams = new URLSearchParams(window.location.search);
        const subjectParam = urlParams.get('subject');
        if (subjectParam) {
            const subjectInput = document.getElementById('subject');
            if (subjectInput) {
                subjectInput.value = subjectParam;
            }
        }
    </script>
</body>

</html>
