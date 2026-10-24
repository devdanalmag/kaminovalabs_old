<?php
// Team showcase section is directly integrated on the Contact page
header("Location: contact.php#team", true, 301);
exit;

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
    <title>Our Team - <?= htmlspecialchars($SITE_NAME) ?> | Leadership & Core Innovators</title>
    <meta name="description"
        content="Meet the visionary founders, cryptographers, AI architects, and cybersecurity specialists driving pioneering digital innovations at Kaminova Labs.">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Our Team - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="keywords"
        content="Kaminova Labs team, blockchain researchers, AI engineers, cybersecurity leaders, tech founders, cryptography specialists">
    <meta name="author" content="<?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kaminovaglobal.com/team.php">
    <meta property="og:title" content="Our Team - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="og:description"
        content="Meet the visionary leadership and researchers engineering tomorrow's decentralized paradigm.">
    <meta property="og:image" content="https://kaminovaglobal.com/img/logo3d.png">
    <meta property="og:site_name" content="<?= htmlspecialchars($SITE_NAME) ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://kaminovaglobal.com/team.php">
    <meta property="twitter:title" content="Our Team - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="twitter:description"
        content="Meet the visionary leadership and researchers engineering tomorrow's decentralized paradigm.">
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
    <link rel="canonical" href="https://kaminovaglobal.com/team.php">

    <style>
        /* Team Showcase Dedicated Styles */
        .team-hero {
            padding: 140px 5% 50px;
            text-align: center;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
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
            margin-bottom: 1.5rem;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        }

        /* Filter Tabs */
        .team-filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 auto 3rem;
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
            margin: 0 auto 3rem;
            padding: 0 1rem;
        }

        .team-card {
            background: linear-gradient(155deg, rgba(0, 26, 61, 0.75) 0%, rgba(0, 10, 24, 0.92) 100%);
            border: 1px solid rgba(0, 212, 255, 0.22);
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.4s ease;
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
    </style>
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
    $currentPage = 'team';
    include 'includes/header.php'; 
    ?>

    <!-- Team Page Hero -->
    <section class="team-hero">
        <div class="team-hero-badge">
            <i class="fas fa-brain"></i> World-Class Multidisciplinary Minds
        </div>
        <h1 class="hero-title" style="margin-bottom: 1.2rem;">Architecting Tomorrow's Digital Paradigm</h1>
        <p class="hero-desc" style="max-width: 840px; margin: 0 auto; font-size: 1.15rem; color: rgba(255, 255, 255, 0.85);">
            Meet the researchers, engineers, cryptographers, and leaders turning advanced computational theory into verifiable, secure, and decentralized real-world technology.
        </p>
    </section>

    <!-- Team Grid Showcase -->
    <section style="max-width: 1350px; margin: 0 auto 5rem; padding: 0 4%;">
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

        <?php if (empty($team_members)): ?>
            <div style="text-align: center; padding: 4rem 1rem; color: rgba(255, 255, 255, 0.7);">
                <i class="fas fa-users fa-3x" style="color: var(--electric-blue); opacity: 0.5; margin-bottom: 1rem;"></i>
                <p>Team roster updating. Please check back shortly.</p>
            </div>
        <?php else: ?>
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
        <?php endif; ?>

        <!-- Join Our Mission / Research Fellowships Banner -->
        <div style="background: linear-gradient(135deg, rgba(0, 26, 61, 0.7), rgba(0, 8, 20, 0.9)); border: 1px solid rgba(0, 212, 255, 0.25); border-radius: 22px; padding: 3rem 2rem; text-align: center; max-width: 1000px; margin: 3rem auto 0; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
            <span style="color: var(--electric-blue); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 0.6rem;">
                <i class="fas fa-handshake"></i> Open Research Fellowships & Careers
            </span>
            <h2 style="font-size: clamp(1.6rem, 3vw, 2.2rem); margin-bottom: 1rem; color: var(--white);">
                Want to Build High-Impact Technologies With Us?
            </h2>
            <p style="color: rgba(255,255,255,0.75); max-width: 650px; margin: 0 auto 2rem; line-height: 1.6;">
                We are continually expanding our research fellowship program for exceptional talent in zero-knowledge proofs, smart contract security, machine learning forensics, and tokenomics.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="contact.php?subject=Career%20%26%20Fellowship%20Opportunities" class="btn btn-primary" style="padding: 12px 28px;">
                    Apply For Fellowship <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
                <a href="projects.php" class="btn btn-secondary" style="padding: 12px 28px;">
                    Explore Our Active Projects
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

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
    </script>
</body>

</html>
