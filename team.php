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

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1300px;
            margin: 0 auto 3rem;
            padding: 0 1rem;
        }

        .team-card {
            background: linear-gradient(145deg, rgba(0, 26, 61, 0.65) 0%, rgba(0, 8, 20, 0.85) 100%);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
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
        }

        .team-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0, 212, 255, 0.55);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(0, 212, 255, 0.25);
        }

        .team-card:hover::before {
            opacity: 1;
        }

        .team-card-image-wrap {
            position: relative;
            width: 100%;
            padding-top: 100%;
            overflow: hidden;
            background: #020b18;
        }

        .team-card-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .team-card:hover .team-card-image {
            transform: scale(1.06);
        }

        .team-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 10, 25, 0.95) 0%, rgba(0, 10, 25, 0.2) 50%, transparent 100%);
            pointer-events: none;
        }

        .team-card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .team-member-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.35rem;
        }

        .team-member-role {
            font-size: 0.9rem;
            color: var(--electric-blue);
            font-weight: 600;
            margin-bottom: 0.9rem;
            letter-spacing: 0.5px;
        }

        .team-member-bio {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
            margin-bottom: 1.4rem;
            flex-grow: 1;
        }

        .team-card-socials {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
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
        <?php if (empty($team_members)): ?>
            <div style="text-align: center; padding: 4rem 1rem; color: rgba(255, 255, 255, 0.7);">
                <i class="fas fa-users fa-3x" style="color: var(--electric-blue); opacity: 0.5; margin-bottom: 1rem;"></i>
                <p>Team roster updating. Please check back shortly.</p>
            </div>
        <?php else: ?>
            <div class="team-grid">
                <?php foreach ($team_members as $member): ?>
                    <div class="team-card">
                        <div class="team-card-image-wrap">
                            <?php if (!empty($member['image'])): ?>
                                <img src="<?= htmlspecialchars($member['image']) ?>" 
                                     alt="<?= htmlspecialchars($member['name']) ?>" 
                                     class="team-card-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="team-card-image" style="display: none; background: #061124; align-items: center; justify-content: center; font-size: 3rem; color: var(--electric-blue);">
                                    <i class="fas fa-user-astronaut"></i>
                                </div>
                            <?php else: ?>
                                <div class="team-card-image" style="display: flex; background: #061124; align-items: center; justify-content: center; font-size: 3rem; color: var(--electric-blue);">
                                    <i class="fas fa-user-astronaut"></i>
                                </div>
                            <?php endif; ?>
                            <div class="team-image-overlay"></div>
                        </div>

                        <div class="team-card-body">
                            <h3 class="team-member-name"><?= htmlspecialchars($member['name']) ?></h3>
                            <div class="team-member-role"><?= htmlspecialchars($member['position']) ?></div>
                            
                            <?php if (!empty($member['bio'])): ?>
                                <p class="team-member-bio"><?= htmlspecialchars($member['bio']) ?></p>
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
                                        <i class="fab fa-twitter"></i>
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
</body>

</html>
