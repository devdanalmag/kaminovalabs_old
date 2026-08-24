<?php
require_once __DIR__ . '/config/database.php';
$pdo = null;
try {
    $pdo = getDB();
} catch (Throwable $e) {
    $pdo = null;
}
function getSetting($pdo, $key, $default = '')
{
    if (!$pdo)
        return $default;
    try {
        $stmt = $pdo->prepare("SELECT setting_value AS v FROM site_settings WHERE setting_key = :k LIMIT 1");
        $stmt->execute([':k' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['v']) && $row['v'] !== null && $row['v'] !== '')
            return $row['v'];
    } catch (Throwable $e) {
    }
    return $default;
}
$SITE_NAME = getSetting($pdo, 'company_name', 'KAMINOVA LABS');
$PHONE = getSetting($pdo, 'contact_phone', '+234 xxxxxxxxxx');
$EMAIL = getSetting($pdo, 'contact_email', 'info@kaminovalabs.com');
$ADDRESS = getSetting($pdo, 'contact_address', '123 Innovation Drive, Nigeria');

// Fetch active partners
$partners = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM partners WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
        $stmt->execute();
        $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $partners = [];
    }
}

// Get unique categories for filter
$categories = [];
foreach ($partners as $p) {
    if (!empty($p['category']) && !in_array($p['category'], $categories)) {
        $categories[] = $p['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecosystem Partners - <?= htmlspecialchars($SITE_NAME) ?> | Collaborative Innovation</title>
    <meta name="description"
        content="Discover Kaminova Labs' ecosystem partners and global alliance network across Web3, blockchain, artificial intelligence, cybersecurity, and digital forensics.">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Ecosystem Partners - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="keywords"
        content="Kaminova Labs partners, Web3 partners, blockchain ecosystem, AI innovation partners, cybersecurity alliance, tech collaborations">
    <meta name="author" content="<?= htmlspecialchars($SITE_NAME) ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kaminovaglobal.com/partners.php">
    <meta property="og:title" content="Ecosystem Partners - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="og:description"
        content="Discover our ecosystem partners driving innovation in Web3, AI, Cybersecurity, and Digital Forensics.">
    <meta property="og:image" content="https://kaminovaglobal.com/img/logo3d.png">
    <meta property="og:site_name" content="<?= htmlspecialchars($SITE_NAME) ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://kaminovaglobal.com/partners.php">
    <meta property="twitter:title" content="Ecosystem Partners - <?= htmlspecialchars($SITE_NAME) ?>">
    <meta property="twitter:description"
        content="Discover our ecosystem partners driving innovation in Web3, AI, Cybersecurity, and Digital Forensics.">
    <meta property="twitter:image" content="https://kaminovaglobal.com/img/logo3d.png">

    <!-- External Resources -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">

    <style>
        .partners-hero {
            padding: 140px 5% 60px;
            text-align: center;
            position: relative;
        }

        .partners-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 50px;
            color: var(--electric-blue);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        }

        .partners-hero h1 {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            font-weight: 800;
            margin-bottom: 1.2rem;
            background: linear-gradient(135deg, #ffffff 0%, var(--electric-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .partners-hero p {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 780px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .partners-stats-strip {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .partners-stat-box {
            background: rgba(0, 26, 61, 0.5);
            border: 1px solid rgba(0, 212, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 1.2rem 2rem;
            border-radius: 14px;
            text-align: center;
            min-width: 160px;
            transition: all 0.3s ease;
        }

        .partners-stat-box:hover {
            border-color: var(--electric-blue);
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 212, 255, 0.2);
        }

        .partners-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--electric-blue);
            font-family: 'Segoe UI', sans-serif;
        }

        .partners-stat-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* Controls: Filter & Search */
        .partners-controls {
            max-width: 1200px;
            margin: 0 auto 3rem;
            padding: 0 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .partner-filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .partner-tab-btn {
            padding: 10px 20px;
            background: rgba(0, 26, 61, 0.6);
            border: 1px solid rgba(0, 212, 255, 0.25);
            border-radius: 30px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .partner-tab-btn:hover,
        .partner-tab-btn.active {
            background: var(--primary-blue);
            color: #ffffff;
            border-color: var(--electric-blue);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
            transform: translateY(-2px);
        }

        .partner-search-box {
            position: relative;
            min-width: 280px;
        }

        .partner-search-box input {
            width: 100%;
            padding: 12px 18px 12px 42px;
            background: rgba(0, 26, 61, 0.6);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 30px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .partner-search-box input:focus {
            border-color: var(--electric-blue);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
            background: rgba(0, 26, 61, 0.85);
        }

        .partner-search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--electric-blue);
            font-size: 0.95rem;
        }

        /* Partners Grid */
        .partners-grid-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 5% 5rem;
        }

        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .partner-card {
            background: rgba(0, 26, 61, 0.45);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 18px;
            padding: 2.2rem;
            backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }

        .partner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--electric-blue), var(--glow));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .partner-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0, 212, 255, 0.6);
            box-shadow: 0 15px 35px rgba(0, 10, 30, 0.6), 0 0 25px rgba(0, 212, 255, 0.25);
        }

        .partner-card:hover::before {
            opacity: 1;
        }

        .partner-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 15px;
        }

        .partner-logo-box {
            width: 120px;
            height: 65px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .partner-card:hover .partner-logo-box {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(0, 212, 255, 0.4);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
        }

        .partner-logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
        }

        .partner-category-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            background: rgba(0, 212, 255, 0.12);
            border: 1px solid rgba(0, 212, 255, 0.35);
            color: var(--electric-blue);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .partner-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        .partner-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--electric-blue);
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .partner-desc {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.8rem;
            flex-grow: 1;
        }

        .partner-footer {
            padding-top: 1.2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .partner-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--electric-blue);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .partner-link-btn:hover {
            color: #ffffff;
            transform: translateX(4px);
            text-shadow: 0 0 10px var(--glow);
        }

        /* Become a Partner CTA Section */
        .partner-cta-section {
            max-width: 1200px;
            margin: 3rem auto 6rem;
            padding: 0 5%;
        }

        .partner-cta-card {
            background: radial-gradient(circle at 10% 20%, rgba(0, 102, 255, 0.25) 0%, rgba(0, 8, 20, 0.9) 80%);
            border: 1px solid rgba(0, 212, 255, 0.35);
            border-radius: 24px;
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(0, 212, 255, 0.15);
        }

        .partner-cta-card h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff, var(--electric-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .partner-cta-card p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 680px;
            margin: 0 auto 2.2rem;
            line-height: 1.6;
        }

        .partner-cta-card .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .no-partners-found {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(0, 26, 61, 0.3);
            border-radius: 18px;
            border: 1px dashed rgba(0, 212, 255, 0.3);
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="loader-circle"></div>
    </div>

    <!-- 3D Canvas -->
    <canvas id="three-canvas"></canvas>

    <!-- Particle Background -->
    <canvas id="particles"></canvas>

    <!-- Geometric Background -->
    <div class="geometric-background"></div>

    <!-- Navigation Header -->
    <?php 
    $currentPage = 'partners';
    include 'includes/header.php'; 
    ?>

    <!-- Hero Section -->
    <section class="partners-hero">
        <div class="partners-hero-badge">
            <i class="fas fa-handshake"></i> Global Collaborative Network
        </div>
        <h1>Ecosystem Partners</h1>
        <p>
            We collaborate with world-class blockchain protocols, artificial intelligence pioneers, cybersecurity authorities, and research institutions to build transformative digital infrastructure.
        </p>

        <div class="partners-stats-strip">
            <div class="partners-stat-box">
                <div class="partners-stat-number"><?= count($partners) ?>+</div>
                <div class="partners-stat-label">Active Partners</div>
            </div>
            <div class="partners-stat-box">
                <div class="partners-stat-number">4+</div>
                <div class="partners-stat-label">Tech Sectors</div>
            </div>
            <div class="partners-stat-box">
                <div class="partners-stat-number">100%</div>
                <div class="partners-stat-label">Verifiable Trust</div>
            </div>
        </div>
    </section>

    <!-- Controls: Filter & Search -->
    <section class="partners-controls">
        <div class="partner-filter-tabs">
            <button class="partner-tab-btn active" data-category="all">All Collaborators</button>
            <?php foreach ($categories as $cat): ?>
                <button class="partner-tab-btn" data-category="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="partner-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="partnerSearchInput" placeholder="Search partners by name, tech or role...">
        </div>
    </section>

    <!-- Partners Grid Showcase -->
    <main class="partners-grid-container">
        <div class="partners-grid" id="partnersGrid">
            <?php if (empty($partners)): ?>
                <div class="no-partners-found">
                    <i class="fas fa-handshake fa-3x" style="color: var(--electric-blue); margin-bottom: 1rem;"></i>
                    <h3>No Ecosystem Partners Found</h3>
                    <p>Partners are being updated. Please check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($partners as $partner): ?>
                    <article class="partner-card" 
                             data-category="<?= htmlspecialchars($partner['category'] ?? 'General') ?>"
                             data-name="<?= htmlspecialchars(strtolower($partner['name'])) ?>"
                             data-title="<?= htmlspecialchars(strtolower($partner['title'] ?? '')) ?>"
                             data-desc="<?= htmlspecialchars(strtolower($partner['description'] ?? '')) ?>">
                        
                        <div>
                            <div class="partner-card-header">
                                <div class="partner-logo-box">
                                    <?php if (!empty($partner['logo'])): ?>
                                        <img src="<?= htmlspecialchars($partner['logo']) ?>" 
                                             alt="<?= htmlspecialchars($partner['name']) ?> Logo" 
                                             class="partner-logo-img"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                        <i class="fas fa-handshake" style="display: none; font-size: 1.8rem; color: var(--electric-blue);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-handshake" style="font-size: 1.8rem; color: var(--electric-blue);"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="partner-category-badge">
                                    <?= htmlspecialchars($partner['category'] ?? 'Partner') ?>
                                </span>
                            </div>

                            <h2 class="partner-name"><?= htmlspecialchars($partner['name']) ?></h2>
                            <?php if (!empty($partner['title'])): ?>
                                <h3 class="partner-title"><?= htmlspecialchars($partner['title']) ?></h3>
                            <?php endif; ?>

                            <p class="partner-desc">
                                <?= nl2br(htmlspecialchars($partner['description'] ?? 'Collaborating with Kaminova Labs on strategic research, blockchain infrastructure, and decentralized systems innovation.')) ?>
                            </p>
                        </div>

                        <div class="partner-footer">
                            <?php if (!empty($partner['website_url'])): ?>
                                <a href="<?= htmlspecialchars($partner['website_url']) ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="partner-link-btn">
                                    <span>Visit Website</span>
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            <?php else: ?>
                                <span class="partner-link-btn" style="color: rgba(255,255,255,0.4); cursor: default;">
                                    <i class="fas fa-shield-alt"></i> Verified Partner
                                </span>
                            <?php endif; ?>
                            <i class="fas fa-circle-check" style="color: var(--electric-blue);" title="Official Ecosystem Alliance"></i>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Become a Partner CTA -->
    <section class="partner-cta-section reveal">
        <div class="partner-cta-card">
            <h2>Join the Kaminova Labs Ecosystem</h2>
            <p>
                Are you building groundbreaking technology in Web3, Artificial Intelligence, Forensics, or Cyber Security? Collaborate with our engineering lab, access joint research grants, and expand your technological reach.
            </p>
            <div class="cta-buttons">
                <a href="index.php#contact" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Partner With Us
                </a>
                <a href="projects.php" class="btn btn-secondary">
                    <i class="fas fa-project-diagram"></i> Explore Projects
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        // Partner Filter and Search Script
        document.addEventListener('DOMContentLoaded', () => {
            const tabButtons = document.querySelectorAll('.partner-tab-btn');
            const searchInput = document.getElementById('partnerSearchInput');
            const partnerCards = document.querySelectorAll('.partner-card');

            let currentCategory = 'all';
            let searchQuery = '';

            function filterPartners() {
                let visibleCount = 0;
                partnerCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category') || '';
                    const cardName = card.getAttribute('data-name') || '';
                    const cardTitle = card.getAttribute('data-title') || '';
                    const cardDesc = card.getAttribute('data-desc') || '';

                    const matchesCategory = (currentCategory === 'all') || (cardCategory === currentCategory);
                    const matchesSearch = !searchQuery || 
                                          cardName.includes(searchQuery) || 
                                          cardTitle.includes(searchQuery) || 
                                          cardDesc.includes(searchQuery) ||
                                          cardCategory.toLowerCase().includes(searchQuery);

                    if (matchesCategory && matchesSearch) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentCategory = btn.getAttribute('data-category');
                    filterPartners();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    searchQuery = e.target.value.toLowerCase().trim();
                    filterPartners();
                });
            }
        });
    </script>
</body>
</html>
