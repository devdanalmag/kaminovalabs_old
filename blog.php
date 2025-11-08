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
    if (!$pdo) return $default;
    try {
        $stmt = $pdo->prepare("SELECT setting_value AS v FROM site_settings WHERE setting_key = :k LIMIT 1");
        $stmt->execute([':k' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['v']) && $row['v'] !== null && $row['v'] !== '') return $row['v'];
    } catch (Throwable $e) {
    }
    return $default;
}
$SITE_NAME = getSetting($pdo, 'company_name', 'KAMINOVA LABS');
$PHONE     = getSetting($pdo, 'contact_phone', '+234 xxxxxxxxxx');
$EMAIL     = getSetting($pdo, 'contact_email', 'info@kaminovalabs.com');
$ADDRESS   = getSetting($pdo, 'contact_address', '123 Innovation Drive, Nigeria');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaminova Labs - Blog & Events</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">
    <style>
        /* Ensure event images fit nicely */
        .event-card .event-img {
            height: 220px;
            overflow: hidden;
            border-radius: 8px;
        }

        .event-card .event-img img.fit-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .event-card {
            cursor: pointer;
        }
    </style>
    <style>
        /* Event modal responsive layout */
        #eventModalOverlay .modal-content {
            width: 90%;
            max-width: 800px;
        }

        #eventModalOverlay .modal-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        #eventModalOverlay .modal-image {
            height: 300px;
            overflow: hidden;
        }

        #eventModalOverlay .modal-actions {
            display: flex;
            gap: 12px;
        }

        #closeEventModal {
            position: absolute;
            top: 10px;
            right: 12px;
            background: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0);
            color: #ffffff;
            width: 36px;
            height: 36px;
            line-height: 34px;
            text-align: center;
            font-size: 20px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            #eventModalOverlay .modal-content {
                width: 95%;
            }

            #eventModalOverlay .modal-body {
                grid-template-columns: 1fr !important;
            }

            #eventModalOverlay .modal-image {
                height: auto;
                max-height: 45vh;
            }

            #eventModalOverlay .modal-actions {
                flex-direction: column;
            }
        }

        /* Ensure event images fit nicely */
        .event-card .event-img {
            height: 220px;
            overflow: hidden;
            border-radius: 8px;
        }

        .event-card .event-img img.fit-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .event-card {
            cursor: pointer;
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

    <!-- Navigation -->
    <header id="header">
        <a href="#" class="logo">
            <img src="img/logo3d.png" alt="KAMINOVA LABS">
            <?= htmlspecialchars($SITE_NAME) ?>
        </a>

        <ul class="nav-links">
            <li><a href="./">Home</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#mission">Mission</a></li>
            <li class="dropdown">
                <a href="projects.php" class="dropdown-toggle">Projects <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="projects.php#biofuture-governance">BioFuture & Governance</a></li>
                    <li><a href="projects.php#forensics-security">Forensics & Security</a></li>
                    <li><a href="projects.php#logistics-utility">Logistics & Utility</a></li>
                    <li><a href="projects.php#finance-payments">Finance & Payments</a></li>
                    <li><a href="projects.php#ecosystem-advisory">Ecosystem & Advisory</a></li>
                </ul>
            </li>
            <!-- <li><a href="#team">Team</a></li> -->
            <li><a href="blog.php" class="current-page">Blog</a></li>
            <li><a href="#achievements">Achievements</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>

        <!-- <a href="#" class="header-cta">Get Started</a> -->

        <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </div>
    </header>
    <!-- Main Content -->
    <!-- <main class="hero"> -->
    <!-- Hero Section -->
    <!-- <section class="hero section reveal" >
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title">Our Projects</h1>
                <p class="hero-subtitle">Pioneering innovations across multiple domains</p>
            </div>
        </div>
    </section>

    <!- Events Section -->
    <section class="section reveal" id="events">
        <div class="container">
            <!-- <div class="event-category">
                <h3 class="category-title" style="padding-top: 35px;">Upcoming Events</h3>
                <div class="projects-grid">
                    <div class="project-card event-card">
                        <div class="project-img event-img">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">February 15, 2025</span>
                                <span class="event-time">2:00 PM - 5:00 PM EST</span>
                                <span class="event-location">Virtual Event</span>
                            </div>
                            <h3 class="project-title">Blockchain Innovation Summit 2025</h3>
                            <p class="project-desc">Join industry leaders and innovators for a comprehensive discussion
                                on the future of blockchain technology, featuring keynotes on AI integration,
                                sustainability, and real-world applications across various sectors.</p>
                            <div class="event-speakers">
                                <p><strong>Keynote Speakers:</strong> Dr. Sarah Chen, Michael Rodriguez, Alex Thompson
                                </p>
                            </div>
                            <button class="btn btn-primary register-btn" data-event="blockchain-summit">Register
                                Now</button>
                        </div>
                    </div>

                    <div class="project-card event-card">
                        <div class="project-img event-img">
                            <i class="fas fa-globe-africa"></i>
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">March 8, 2025</span>
                                <span class="event-time">10:00 AM - 4:00 PM WAT</span>
                                <span class="event-location">Lagos, Nigeria</span>
                            </div>
                            <h3 class="project-title">Africa Blockchain Week: Empowering Local Innovation</h3>
                            <p class="project-desc">A groundbreaking event focused on blockchain adoption in Africa,
                                featuring workshops on DeFi, logistics solutions, and mobile payments. Connect with
                                local entrepreneurs and global investors.</p>
                            <div class="event-speakers">
                                <p><strong>Featured Speakers:</strong> Amara Okafor, Jennifer Park, Local Industry
                                    Leaders</p>
                            </div>
                            <button class="btn btn-primary register-btn" data-event="africa-blockchain-week">Register
                                Now</button>
                        </div>
                    </div>

                    <div class="project-card event-card">
                        <div class="project-img event-img">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">March 22, 2025</span>
                                <span class="event-time">1:00 PM - 6:00 PM EST</span>
                                <span class="event-location">Hybrid: NYC & Virtual</span>
                            </div>
                            <h3 class="project-title">Web3 Security & Forensics Workshop</h3>
                            <p class="project-desc">Deep dive into advanced blockchain security practices, threat
                                detection, and forensic analysis. Hands-on workshops with NodeWhisper and BADIS
                                platforms, plus networking with security professionals.</p>
                            <div class="event-speakers">
                                <p><strong>Workshop Leaders:</strong> Alex Thompson, Security Team, Industry Experts</p>
                            </div>
                            <button class="btn btn-primary register-btn" data-event="security-workshop">Register
                                Now</button>
                        </div>
                    </div>
                </div>
            </div> -->

            <div class="event-category">
                <h3 class="category-title">Past Events</h3>
                <div class="projects-grid">
                    <div class="project-card event-card past-event" data-title="Green Web3: Sustainable Blockchain Solutions" data-date="December 10, 2023" data-time="3:00 PM - 7:00 PM EST" data-location="Virtual Event" data-description="Explored the intersection of blockchain technology and environmental sustainability, featuring presentations on carbon tokenization, renewable energy tracking, and the GreenTrust Nexus ecosystem." data-image="uploads/Jigawa1.jpg" data-x-link="https://x.com/">
                        <div class="project-img event-img">
                            <img src="uploads/Jigawa1.jpg" alt="Green Web3: Sustainable Blockchain Solutions" class="fit-img">
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">December 10, 2023</span>
                                <span class="event-time">3:00 PM - 7:00 PM EST</span>
                                <span class="event-location">Virtual Event</span>
                            </div>
                            <h3 class="project-title">Green Web3: Sustainable Blockchain Solutions</h3>
                            <p class="project-desc">Explored the intersection of blockchain technology and environmental
                                sustainability, featuring presentations on carbon tokenization, renewable energy
                                tracking, and the GreenTrust Nexus ecosystem.</p>
                            <div class="event-speakers">
                                <p><strong>Speakers:</strong> Michael Rodriguez, Environmental Tech Leaders</p>
                            </div>
                            <button class="btn btn-secondary watch-replay-btn" data-event="green-web3" data-watch-link="https://youtu.be/placeholder">Watch
                                Replay</button>
                        </div>
                    </div>

                    <div class="project-card event-card past-event" data-title="DeFi Innovation Showcase" data-date="November 18, 2023" data-time="2:00 PM - 5:00 PM EST" data-location="Virtual Event" data-description="Showcased the latest developments in decentralized finance, including live demos of HashSwap, discussions on user experience improvements, and the future of accessible DeFi platforms." data-image="uploads/defi.jpg" data-x-link="https://x.com/">
                        <div class="project-img event-img">
                            <img src="uploads/defi.jpg" alt="DeFi Innovation Showcase" class="fit-img">
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">November 18, 2023</span>
                                <span class="event-time">2:00 PM - 5:00 PM EST</span>
                                <span class="event-location">Virtual Event</span>
                            </div>
                            <h3 class="project-title">DeFi Innovation Showcase</h3>
                            <p class="project-desc">Showcased the latest developments in decentralized finance,
                                including live demos of HashSwap, discussions on user experience improvements, and the
                                future of accessible DeFi platforms.</p>
                            <div class="event-speakers">
                                <p><strong>Speakers:</strong> Jennifer Park, DeFi Development Team</p>
                            </div>
                            <button class="btn btn-secondary watch-replay-btn" data-event="defi-showcase" data-watch-link="https://youtu.be/placeholder">Watch
                                Replay</button>
                        </div>
                    </div>

                    <div class="project-card event-card past-event" data-title="Digital Philanthropy & Transparent Donations" data-date="October 25, 2023" data-time="11:00 AM - 3:00 PM EST" data-location="Virtual Event" data-description="Discussed the transformation of charitable giving through blockchain technology, featuring the Trustfound platform and insights into building trust in digital communities through transparent donation systems." data-image="uploads/trustfound.jpg" data-x-link="https://x.com/">
                        <div class="project-img event-img">
                            <img src="uploads/trustfound.jpg" alt="Digital Philanthropy & Transparent Donations" class="fit-img">
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">October 25, 2023</span>
                                <span class="event-time">11:00 AM - 3:00 PM EST</span>
                                <span class="event-location">Virtual Event</span>
                            </div>
                            <h3 class="project-title">Digital Philanthropy & Transparent Donations</h3>
                            <p class="project-desc">Discussed the transformation of charitable giving through blockchain
                                technology, featuring the Trustfound platform and insights into building trust in
                                digital communities through transparent donation systems.</p>
                            <div class="event-speakers">
                                <p><strong>Speakers:</strong> Omar Hassan, Community Leaders, Nonprofit Organizations
                                </p>
                            </div>
                            <button class="btn btn-secondary watch-replay-btn" data-event="digital-philanthropy" data-watch-link="https://youtu.be/placeholder">Watch
                                Replay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section -->
    <section class="hero section reveal">
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title" style="padding-top: 30px;">Blog & Events</h1>
                <p class="hero-subtitle">Stay updated with our latest insights, innovations, and upcoming events</p>
            </div>
        </div>
    </section>
    <!-- Blog Posts Section -->
    <section class="section reveal" id="blog-posts">
        <div class="container">
            <div class="blog-category">
                <h3 class="category-title">Latest Blog Posts</h3>
                <div class="projects-grid">
                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">January 15, 2025</span>
                                <span class="blog-category-tag">AI & Innovation</span>
                            </div>
                            <h3 class="project-title">The Future of AI in Blockchain: Revolutionizing Digital Trust</h3>
                            <p class="project-desc">Exploring how artificial intelligence is transforming blockchain
                                technology, from smart contract optimization to predictive analytics in decentralized
                                systems. Discover the cutting-edge innovations shaping tomorrow's digital landscape.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Dr. Sarah Chen, AI Research Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="ai-blockchain">Read Full
                                Article</button>
                        </div>
                    </div>

                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">January 10, 2025</span>
                                <span class="blog-category-tag">Sustainability</span>
                            </div>
                            <h3 class="project-title">Building Sustainable Web3: Green Blockchain Solutions for Climate
                                Action</h3>
                            <p class="project-desc">How blockchain technology is being leveraged to combat climate
                                change through carbon tokenization, renewable energy tracking, and sustainable supply
                                chain management. A deep dive into our GreenTrust Nexus project.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Michael Rodriguez, Sustainability Director</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="green-web3">Read Full
                                Article</button>
                        </div>
                    </div>

                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">January 5, 2025</span>
                                <span class="blog-category-tag">Security</span>
                            </div>
                            <h3 class="project-title">Next-Gen Blockchain Security: From Forensics to Predictive
                                Protection</h3>
                            <p class="project-desc">An in-depth look at advanced blockchain security measures, including
                                our NodeWhisper forensics platform and BADIS immune system. Learn how AI-driven threat
                                detection is revolutionizing DApp security.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Alex Thompson, Security Architect</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="blockchain-security">Read Full
                                Article</button>
                        </div>
                    </div>

                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-globe-africa"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">December 28, 2023</span>
                                <span class="blog-category-tag">Innovation</span>
                            </div>
                            <h3 class="project-title">Empowering Africa Through Blockchain: Real-World Applications and
                                Impact</h3>
                            <p class="project-desc">Discover how blockchain technology is transforming African economies
                                through innovative solutions in logistics, payments, and agriculture. Case studies from
                                our HashPack Logistics and HederaPay projects.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Amara Okafor, Regional Innovation Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="africa-blockchain">Read Full
                                Article</button>
                        </div>
                    </div>

                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">December 20, 2023</span>
                                <span class="blog-category-tag">DeFi</span>
                            </div>
                            <h3 class="project-title">The Evolution of DeFi: Building User-Friendly Decentralized
                                Finance</h3>
                            <p class="project-desc">Exploring the latest trends in decentralized finance and how we're
                                making DeFi more accessible through intuitive interfaces and seamless user experiences.
                                Insights from our HashSwap development journey.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Jennifer Park, DeFi Product Manager</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="defi-evolution">Read Full
                                Article</button>
                        </div>
                    </div>

                    <div class="project-card blog-card">
                        <div class="project-img blog-img">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="project-content">
                            <div class="blog-meta">
                                <span class="blog-date">December 15, 2023</span>
                                <span class="blog-category-tag">Community</span>
                            </div>
                            <h3 class="project-title">Building Trust in Digital Communities: The Role of Transparent
                                Donations</h3>
                            <p class="project-desc">How blockchain technology is revolutionizing charitable giving and
                                community support through transparent, traceable donations. Learn about our Trustfound
                                platform and its impact on digital philanthropy.</p>
                            <div class="blog-author">
                                <p><strong>Author:</strong> Omar Hassan, Community Relations Manager</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-blog="digital-trust">Read Full
                                Article</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </section>
    <!-- </main> -->

    <!-- Modal -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="modal-close" id="modalClose">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
    <!-- Event Modal -->
    <div id="eventModalOverlay" class="modal-overlay" style="position:fixed; inset:0; background:rgba(0,0,0,0.93); display:none; align-items:center; justify-content:center; z-index:1000;">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle" tabindex="-1" style="background:#0f1226; color:#fff; width:90%; max-width:800px; border-radius:12px; overflow:auto; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
            <div class="modal-header" style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.1);">
                <h3 id="eventModalTitle" style="margin:0; font-size:1.25rem;"></h3>
            </div>
            <div class="modal-body" style="display:grid; grid-template-columns:1fr 1fr; gap:0;">
                <button id="closeEventModal" class="btn" aria-label="Close" title="Close">x</button>
                <div class="modal-image" style="height:300px; overflow:hidden;">
                    <img id="eventModalImage" src="" alt="Event image" style="width:100%; height:100%; object-fit:cover; display:block;">
                </div>
                <div class="modal-details" style="padding:16px 20px;">
                    <p id="eventModalMeta" style="margin:0 0 8px; opacity:0.85;"></p>
                    <p id="eventModalDescription" style="margin:0 0 16px; line-height:1.5; opacity:0.95;"></p>
                    <div class="modal-actions" style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a id="eventWatchLink" href="#" target="_blank" rel="noopener" class="btn btn-primary" aria-label="Watch replay video" style="background:#4a6cff; color:#fff; text-decoration:none; padding:10px 14px; border-radius:8px; display: flex;">Watch Replay</a>
                        <a id="eventXLink" href="#" target="_blank" rel="noopener" class="btn btn-secondary" aria-label="View full post on X" style="background:#1da1f2; color:#fff; text-decoration:none; padding:10px 14px; border-radius:8px; display: flex;">View More on X</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>

    <script>
        const CONFIG = {
            baseUrl: '/kaminovalabs_old/',
            uploadsPath: 'uploads/',
            eventsPath: 'uploads/events/'
        };

        (function() {
            // Project Modal Functionality (scoped to prevent redeclaration)
            const projectModalEl = document.getElementById('projectModal');
            const projectModalBody = document.getElementById('modalBody');
            const projectModalClose = document.getElementById('modalClose');
            const readMoreBtns = document.querySelectorAll('.read-more-btn');

            // Project details data
            const projectDetails = {
                greentrust: {
                    title: "GreenTrust Nexus",
                    status: "Under Development",
                    category: "BioFuture & Governance",
                    description: "Climate, Trust & Tokenized Sustainability",
                    fullDescription: "A comprehensive Web3 ecosystem designed for climate tokenization, trust networks, and incentive-driven governance. GreenTrust Nexus aims to revolutionize how we approach environmental sustainability through blockchain technology, creating transparent and verifiable carbon credit systems, environmental impact tracking, and community-driven governance mechanisms.",
                    features: [
                        "Climate tokenization and carbon credit management",
                        "Trust network establishment for environmental projects",
                        "Incentive-driven governance for sustainability initiatives",
                        "Transparent environmental impact tracking",
                        "Community voting on climate projects"
                    ],
                    team: [{
                        role: "Founder",
                        name: "Team Lead"
                    }]
                },
                nodewhisper: {
                    title: "NodeWhisper",
                    status: "Under Development",
                    category: "Forensics & Security",
                    description: "Blockchain Forensics & Threat Intelligence Platform",
                    fullDescription: "Next-generation forensic tool equipped with advanced threat heatmaps, behavioral fingerprinting, and collaborative case rooms. NodeWhisper provides comprehensive blockchain analysis capabilities for law enforcement, compliance teams, and security professionals.",
                    features: [
                        "Advanced threat heatmaps and visualization",
                        "Behavioral fingerprinting for suspicious activities",
                        "Collaborative case rooms for team investigations",
                        "Real-time blockchain transaction monitoring",
                        "Comprehensive forensic reporting tools"
                    ],
                    team: [{
                        role: "Founder",
                        name: "Team Lead"
                    }]
                },
                badis: {
                    title: "BADIS (Blockchain-Agnostic DApp Immune System)",
                    status: "Novel Idea Proposal to Open Zeppelin",
                    category: "Forensics & Security",
                    description: "AI-Driven Real-Time DApp Security",
                    fullDescription: "A revolutionary cross-chain immune system that delivers automated AI threat detection and real-time patching for decentralized applications. BADIS represents a paradigm shift in DApp security, providing proactive protection across multiple blockchain networks.",
                    features: [
                        "Cross-chain compatibility and monitoring",
                        "AI-powered threat detection algorithms",
                        "Real-time automated patching system",
                        "Behavioral analysis of smart contracts",
                        "Immune system learning and adaptation"
                    ],
                    team: [{
                        role: "Founder",
                        name: "Team Lead"
                    }]
                },
                dpm: {
                    title: "DPM (Decentralized Privacy Mesh)",
                    status: "Coming Soon",
                    category: "Forensics & Security",
                    description: "Reinventing Privacy in Modular Blockchain Ecosystems",
                    fullDescription: "An innovative privacy mesh solution utilizing zero-knowledge proofs and advanced encryption techniques specifically designed for modular blockchain ecosystems. DPM ensures complete privacy while maintaining interoperability across different blockchain networks.",
                    features: [
                        "Zero-knowledge proof implementation",
                        "Advanced encryption for modular blockchains",
                        "Cross-chain privacy preservation",
                        "Decentralized privacy mesh architecture",
                        "Seamless integration with existing DApps"
                    ],
                    team: [{
                        role: "Founder",
                        name: "Team Lead"
                    }]
                },
                hashpack: {
                    title: "HashPack Logistics",
                    status: "Under Development",
                    category: "Logistics & Utility",
                    description: "Reinventing Africa's Deliveries",
                    fullDescription: "A blockchain-powered last-mile delivery solution specifically designed for the African market. HashPack Logistics provides real-time tracking, secure payments, and comprehensive trust systems for riders and vendors, revolutionizing the delivery ecosystem across Africa.",
                    features: [
                        "Real-time package tracking and monitoring",
                        "Secure blockchain-based payment system",
                        "Rider and vendor trust scoring system",
                        "Route optimization for African logistics",
                        "Mobile-first design for accessibility"
                    ],
                    team: [{
                        role: "CTO",
                        name: "Chief Technology Officer"
                    }]
                },
                hederapay: {
                    title: "HederaPay",
                    status: "Under Development",
                    category: "Finance & Payments",
                    description: "Decentralized Bill & Mobile Top-Up Manager",
                    fullDescription: "A comprehensive Web3-powered application designed for the African market that tracks airtime and data usage, enables instant top-ups with HBAR or stablecoins, and executes secure bill payments through Hedera smart contracts with immutable receipts and budgeting insights.",
                    features: [
                        "Airtime and data usage tracking",
                        "Instant top-ups with HBAR and stablecoins",
                        "Secure bill payment through smart contracts",
                        "Immutable transaction receipts",
                        "Advanced budgeting and financial insights"
                    ],
                    team: [{
                        role: "CTO",
                        name: "Chief Technology Officer"
                    }]
                },
                hashswap: {
                    title: "HashSwap",
                    status: "Under Development",
                    category: "Finance & Payments",
                    description: "DEX for Seamless Hedera Trading",
                    fullDescription: "A decentralized exchange built specifically on the Hedera network, enabling efficient token swaps, liquidity pools, and fast, low-cost transactions. HashSwap integrates the Hedera Consensus Service for complete transparency and trust in all trading activities.",
                    features: [
                        "Seamless token swapping on Hedera",
                        "Liquidity pool creation and management",
                        "Fast and low-cost transactions",
                        "Hedera Consensus Service integration",
                        "Advanced trading analytics and tools"
                    ],
                    team: [{
                        role: "CTO",
                        name: "Chief Technology Officer"
                    }]
                },
                trustfound: {
                    title: "Trustfound",
                    status: "Under Development",
                    category: "Finance & Payments",
                    description: "Decentralized Donation & Transparency Platform",
                    fullDescription: "A Hedera-powered decentralized application designed for donations including Zakat, Sadaqah, and general charity in HBAR, stablecoins, or tokens. Trustfound uses the Hedera Consensus Service for immutable tracking, smart contracts for fair fund distribution, and provides a real-time public dashboard for complete transparency.",
                    features: [
                        "Multi-currency donation support (HBAR, stablecoins, tokens)",
                        "Immutable donation tracking via Hedera Consensus Service",
                        "Smart contract-based fair fund distribution",
                        "Real-time public transparency dashboard",
                        "Zakat, Sadaqah, and charity categorization"
                    ],
                    team: [{
                        role: "CTO",
                        name: "Chief Technology Officer"
                    }]
                },
                agrochainx: {
                    title: "AgroChainX",
                    status: "Advisory & Investor",
                    category: "Ecosystem & Advisory Projects",
                    description: "Blockchain Marketplace for Farmers & Traders",
                    fullDescription: "A comprehensive mobile application enabling farmers and traders to conduct secure commercial transactions. AgroChainX features a fair-price agricultural marketplace, shipment tracking, peer-to-peer crypto exchange, and Agrobot, an AI assistant providing farming, business, and crypto insights.",
                    features: [
                        "Fair-price agricultural marketplace",
                        "Secure commercial transaction system",
                        "Real-time shipment tracking",
                        "Peer-to-peer cryptocurrency exchange",
                        "Agrobot AI assistant for farming insights"
                    ],
                    team: [{
                        role: "Advisory & Investor",
                        name: "Strategic Advisor"
                    }]
                },
                smartag: {
                    title: "Smart Agriculture Detection",
                    status: "Advisory & Investor",
                    category: "Ecosystem & Advisory Projects",
                    description: "AI-Powered Crop, Pest & Soil Analysis",
                    fullDescription: "An advanced AI solution providing comprehensive crop detection, pest and disease identification with treatment recommendations, and detailed soil composition analysis. Currently in demo mode with plans for API integration to deliver real-world agricultural intelligence to farmers and agricultural professionals.",
                    features: [
                        "AI-powered crop detection and analysis",
                        "Pest and disease identification system",
                        "Treatment recommendation engine",
                        "Comprehensive soil composition analysis",
                        "API integration for real-world deployment"
                    ],
                    team: [{
                        role: "Advisory & Investor",
                        name: "Strategic Advisor"
                    }]
                },
                safeguard: {
                    title: "Safeguard",
                    status: "Advisory",
                    category: "Ecosystem & Advisory Projects",
                    description: "SME Operating System for Africa",
                    fullDescription: "An integrated online and offline platform specifically designed to empower African small and medium enterprises. Safeguard provides comprehensive business solutions including inventory management, payment processing, e-commerce capabilities, business authentication, and credit solutions—all unified in one powerful ecosystem.",
                    features: [
                        "Comprehensive inventory management system",
                        "Integrated payment processing solutions",
                        "E-commerce platform and tools",
                        "Business authentication and verification",
                        "Credit solutions and financial services"
                    ],
                    team: [{
                        role: "Advisory",
                        name: "Strategic Advisor"
                    }]
                }
            };

            // Open modal function
            function openProjectModal(projectKey) {
                const project = projectDetails[projectKey];
                if (!project) return;

                projectModalBody.innerHTML = `
                <h2>${project.title}</h2>
                <p class="modal-status"><strong>Status:</strong> ${project.status}</p>
                <p class="modal-category"><strong>Category:</strong> ${project.category}</p>
                <p class="modal-description"><strong>Description:</strong> ${project.description}</p>
                
                <div class="modal-section">
                    <h3>Full Description</h3>
                    <p>${project.fullDescription}</p>
                </div>

                <div class="modal-section">
                    <h3>Key Features</h3>
                    <ul>
                        ${project.features.map(feature => `<li>${feature}</li>`).join('')}
                    </ul>
                </div>

                <div class="modal-section">
                    <h3>Team</h3>
                    ${project.team.map(member => `<p><strong>${member.role}:</strong> ${member.name}</p>`).join('')}
                </div>
            `;

                projectModalEl.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            // Close modal function
            function closeModal() {
                projectModalEl.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Event listeners for modal
            readMoreBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const projectKey = btn.getAttribute('data-project');
                    openProjectModal(projectKey);
                });
            });

            projectModalClose.addEventListener('click', closeModal);

            // Close modal when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === projectModalEl) {
                    closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && projectModalEl.style.display === 'block') {
                    closeModal();
                }
            });

            // API Functions for Dynamic Content
            async function fetchAPI(endpoint) {
                try {
                    const response = await fetch(`api/get_content.php?${endpoint}`);
                    const data = await response.json();
                    return data.success ? data.data : null;
                } catch (error) {
                    console.error('API Error:', error);
                    return null;
                }
            }

            // Helper: resolve event image from various possible DB fields
            function resolveEventImage(event) {
                try {
                    const candidates = [
                        event.image_url,
                        event.image_path,
                        event.image,
                        event.featured_image
                    ];
                    let url = candidates.find(v => v && String(v).trim());
                    if (!url) return '';
                    url = String(url).trim();

                    // If absolute URL, keep as is
                    if (url.startsWith('http://') || url.startsWith('https://')) {
                        return url;
                    }

                    // Local path handling
                    if (url.startsWith(CONFIG.uploadsPath)) {
                        return `${CONFIG.baseUrl}${url}`;
                    }

                    // Otherwise assume it's a filename under events path
                    return `${CONFIG.baseUrl}${CONFIG.eventsPath}${url}`;
                } catch (e) {
                    console.warn('resolveEventImage error:', e);
                    return '';
                }
            }

            // Load Blog Posts
            async function loadBlogPosts() {
                const blogPosts = await fetchAPI('type=blog_posts&status=published&limit=100');
                if (blogPosts && blogPosts.length > 0) {
                    const blogGrid = document.querySelector('#blog-posts .projects-grid');
                    if (blogGrid) {
                        blogGrid.innerHTML = '';

                        blogPosts.forEach(post => {
                            const blogCard = document.createElement('div');
                            blogCard.className = 'project-card blog-card';
                            blogCard.innerHTML = `
                            <div class="project-img blog-img">
                                ${post.featured_image ? 
                                    `<img src="${post.featured_image}" alt="${post.title}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">` :
                                    '<i class="fas fa-blog"></i>'
                                }
                            </div>
                            <div class="project-content">
                                <div class="blog-meta">
                                    <span class="blog-date">${new Date(post.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                    <span class="blog-category-tag">${post.category || 'General'}</span>
                                </div>
                                <h3 class="project-title">${post.title}</h3>
                                <p class="project-desc">${post.excerpt || post.content.substring(0, 200) + '...'}</p>
                                <div class="blog-author">
                                    <p><strong>Author:</strong> ${post.author || 'Kaminova Labs Team'}</p>
                                </div>
                                <button class="btn btn-secondary read-more-btn" data-blog="${post.slug}">Read Full Article</button>
                            </div>
                        `;
                            blogGrid.appendChild(blogCard);
                        });

                        // Update blog details for modal
                        updateBlogDetails(blogPosts);
                        // Re-attach event listeners for new buttons
                        attachBlogModalEventListeners();
                    }
                }
            }

            // Load Events
            async function loadEvents() {
                // Fetch all active events and categorize client-side so everything shows
                const allEvents = await fetchAPI('type=events&active_only=1&limit=1000');
                const upcomingEvents = (allEvents || []).filter(e => String(e.event_type || '').toLowerCase() === 'upcoming');
                if (upcomingEvents && upcomingEvents.length > 0) {
                    const upcomingGrid = document.querySelector('#events .event-category .projects-grid');
                    if (upcomingGrid) {
                        upcomingGrid.innerHTML = '';

                        upcomingEvents.forEach(event => {
                            const imageSrc = resolveEventImage(event);
                            const eventCard = document.createElement('div');
                            eventCard.className = 'project-card event-card';

                            eventCard.innerHTML = `
                            <div class="project-img event-img">
                                ${imageSrc ? 
                                    `<img src="${imageSrc}" alt="${event.title}" class="fit-img">` :
                                    `<i class="${event.icon_class || 'fas fa-calendar-alt'}"></i>`
                                }
                            </div>
                            <div class="project-content">
                                <div class="event-meta">
                                    <span class="event-date">${event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'TBD'}</span>
                                    <span class="event-time">${event.event_time || 'TBD'}</span>
                                    <span class="event-location">${event.location || 'TBD'}</span>
                                </div>
                                <h3 class="project-title">${event.title}</h3>
                                <p class="project-desc">${event.description}</p>
                                ${event.speakers ? `<div class="event-speakers">
                                    <p><strong>Speakers:</strong> ${event.speakers}</p>
                                </div>` : ''}
                                <div class="event-actions" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    ${event.watch_link ?
                                        `<button class="btn btn-secondary watch-replay-btn" data-event="${event.slug}" data-watch-link="${event.watch_link || ''}" onclick="event.stopPropagation(); var l=this.dataset.watchLink; if(l) window.open(l,'_blank');">Watch Replay</button>` :
                                        ''}
                                    ${event.x_link ?
                                        `<a href="${event.x_link}" class="btn btn-outline x-link-btn" target="_blank" onclick="event.stopPropagation()">View Full Post on Twitter</a>` :
                                        ''}
                                </div>
                                ${event.registration_link ? 
                                    `<a href="${event.registration_link}" class="btn btn-primary register-btn" target="_blank" onclick="event.stopPropagation()">Register Now</a>` :
                                    '<button class="btn btn-primary register-btn" disabled onclick="event.stopPropagation()">Registration Coming Soon</button>'
                                }
                            </div>
                        `;
                            // Attach data for modal
                            eventCard.dataset.title = event.title || '';
                            eventCard.dataset.date = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) : '';
                            eventCard.dataset.time = event.event_time || '';
                            eventCard.dataset.location = event.location || '';
                            eventCard.dataset.description = event.description || '';
                            eventCard.dataset.image = imageSrc || '';
                            eventCard.dataset.xLink = event.x_link || '';
                            eventCard.dataset.watchLink = event.watch_link || ''; // Add this line
                            // Inline click handler to open modal from card
                            eventCard.setAttribute('onclick', 'openFromCardInline(this)');
                            // Ensure card is appended
                            upcomingGrid.appendChild(eventCard);
                        });
                    }
                }

                // Load past events
                const pastEvents = (allEvents || []).filter(e => String(e.event_type || '').toLowerCase() === 'past' || !e.event_type);
                if (pastEvents && pastEvents.length > 0) {
                    const pastEventCategory = document.querySelectorAll('.event-category')[1];
                    if (pastEventCategory) {
                        const pastGrid = pastEventCategory.querySelector('.projects-grid');
                        if (pastGrid) {
                            pastGrid.innerHTML = '';

                            pastEvents.forEach(event => {
                                const imageSrc = resolveEventImage(event);
                                const eventCard = document.createElement('div');
                                eventCard.className = 'project-card event-card past-event';
                                eventCard.innerHTML = `
                                <div class="project-img event-img">
                                    ${imageSrc ? 
                                        `<img src="${imageSrc}" alt="${event.title}" class="fit-img">` :
                                        `<i class="${event.icon_class || 'fas fa-calendar-alt'}"></i>`
                                    }
                                </div>
                                <div class="project-content">
                                    <div class="event-meta">
                                        <span class="event-date">${event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Past Event'}</span>
                                        <span class="event-time">${event.event_time || ''}</span>
                                        <span class="event-location">${event.location || ''}</span>
                                    </div>
                                    <h3 class="project-title">${event.title}</h3>
                                    <p class="project-desc">${event.description}</p>
                                    ${event.speakers ? `<div class="event-speakers">
                                        <p><strong>Speakers:</strong> ${event.speakers}</p>
                                    </div>` : ''}
                                    <div class="event-actions" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <button class="btn btn-secondary watch-replay-btn" data-event="${event.slug}" data-watch-link="${event.watch_link || ''}" onclick="event.stopPropagation(); var l=this.dataset.watchLink; if(l) window.open(l,'_blank');">Watch Replay</button>
                                        ${event.x_link ?
                                            `<a href="${event.x_link}" class="btn btn-outline x-link-btn" target="_blank" onclick="event.stopPropagation()">View Full Post on Twitter</a>` :
                                            ''}
                                    </div>
                                </div>
                            `;
                            // Attach data for modal
                            eventCard.dataset.title = event.title || '';
                                eventCard.dataset.date = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) : '';
                                eventCard.dataset.time = event.event_time || '';
                                eventCard.dataset.location = event.location || '';
                                eventCard.dataset.description = event.description || '';
                            eventCard.dataset.image = imageSrc || '';
                            eventCard.dataset.xLink = event.x_link || '';
                            eventCard.dataset.watchLink = event.watch_link || '';
                            // Inline click handler to open modal from card
                            eventCard.setAttribute('onclick', 'openFromCardInline(this)');
                            pastGrid.appendChild(eventCard);
                        });
                        }
                    }
                }
            }

            // Update blog details object with database data
            function updateBlogDetails(blogPosts) {
                window.blogDetails = {};
                blogPosts.forEach(post => {
                    window.blogDetails[post.slug] = {
                        title: post.title,
                        content: post.content,
                        excerpt: post.excerpt,
                        author: post.author,
                        category: post.category,
                        created_at: post.created_at,
                        featured_image: post.featured_image
                    };
                });
            }

            // Attach event listeners for blog modal functionality
            function attachBlogModalEventListeners() {
                const readMoreBtns = document.querySelectorAll('.read-more-btn');
                readMoreBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const blogKey = btn.getAttribute('data-blog');
                        openBlogModal(blogKey);
                    });
                });
            }

            // Open blog modal function
            function openBlogModal(blogKey) {
                const blog = window.blogDetails ? window.blogDetails[blogKey] : null;
                if (!blog) return;

                const modalBody = document.getElementById('modalBody');
                modalBody.innerHTML = `
                <h2>${blog.title}</h2>
                <div class="blog-meta" style="margin-bottom: 1rem;">
                    <p><strong>Author:</strong> ${blog.author || 'Kaminova Labs Team'}</p>
                    <p><strong>Category:</strong> ${blog.category || 'General'}</p>
                    <p><strong>Published:</strong> ${new Date(blog.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
                
                ${blog.featured_image ? `<img src="${blog.featured_image}" alt="${blog.title}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem;">` : ''}
                
                <div class="modal-section">
                    <div style="line-height: 1.6; white-space: pre-wrap;">${blog.content}</div>
                </div>
            `;

                const modal = document.getElementById('projectModal');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            // Load Site Settings for Footer
            async function loadSiteSettings() {
                const settings = await fetchAPI('type=settings');
                if (settings) {
                    // Update contact info in footer
                    const contactLinks = document.querySelectorAll('.footer-links li');
                    contactLinks.forEach(link => {
                        const text = link.textContent;
                        if (text.includes('Innovation Drive') && settings.contact_address) {
                            link.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${settings.contact_address}`;
                        }
                        if (text.includes('+234') && settings.contact_phone) {
                            link.innerHTML = `<i class="fas fa-phone"></i> ${settings.contact_phone}`;
                        }
                        if (text.includes('info@kaminovalabs.com') && settings.contact_email) {
                            link.innerHTML = `<i class="fas fa-envelope"></i> ${settings.contact_email}`;
                        }
                    });

                    // Update company name in footer
                    const footerLogo = document.querySelector('.footer-logo');
                    if (footerLogo && settings.company_name) {
                        footerLogo.textContent = settings.company_name;
                    }
                }
            }

            // Initialize Dynamic Content
            async function initializeDynamicContent() {
                await loadSiteSettings();
                await loadBlogPosts();
                await loadEvents();
            }

            // Dropdown functionality
            const dropdown = document.querySelector('.dropdown');
            const dropdownToggle = document.querySelector('.dropdown-toggle');

            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    // Only prevent default if we're trying to show dropdown, not navigate
                    if (!dropdown.classList.contains('active')) {
                        e.preventDefault();
                        dropdown.classList.add('active');
                    } else {
                        // Allow navigation when dropdown is already open
                        dropdown.classList.remove('active');
                    }
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            // Load dynamic content when page loads
            document.addEventListener('DOMContentLoaded', initializeDynamicContent);
        })();

        (function() {
            const overlay = document.getElementById('eventModalOverlay');
            const titleEl = document.getElementById('eventModalTitle');
            const imgEl = document.getElementById('eventModalImage');
            const metaEl = document.getElementById('eventModalMeta');
            const descEl = document.getElementById('eventModalDescription');
            const watchEl = document.getElementById('eventWatchLink');
            const xEl = document.getElementById('eventXLink');
            const closeBtn = document.getElementById('closeEventModal');

            // Function to open modal from event card data
            function openFromCard(card) {
                if (!card) return;
                const d = card.dataset || {};
                titleEl.textContent = d.title || '';
                imgEl.src = d.image || '';
                imgEl.alt = d.title || 'Event image';

                const date = d.date ? d.date : '';
                const time = d.time ? ` | ${d.time}` : '';
                const location = d.location ? ` | ${d.location}` : '';
                metaEl.textContent = `${date}${time}${location}`.trim();

                descEl.textContent = d.description || '';

                // Handle watch link
                if (d.watchLink) {
                    watchEl.href = d.watchLink;
                    watchEl.style.display = 'inline-flex';
                } else {
                    watchEl.removeAttribute('href');
                    watchEl.style.display = 'none';
                }

                // Handle X link
                if (d.xLink) {
                    xEl.href = d.xLink;
                    xEl.style.display = 'inline-flex';
                } else {
                    xEl.removeAttribute('href');
                    xEl.style.display = 'none';
                }

                overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                // Move keyboard focus to modal for accessibility
                try { closeBtn && closeBtn.focus(); } catch {}
            }

            // Expose handler globally for inline onclick usage
            window.openFromCardInline = openFromCard;

            // Function to attach event listeners to ALL event cards
            function attachEventModalListeners() {
                // Remove any existing listeners first
                document.querySelectorAll('.event-card').forEach(card => {
                    card.replaceWith(card.cloneNode(true));
                });

                // Attach click listeners to all event cards (both static and dynamic)
                document.querySelectorAll('.event-card').forEach(card => {
                    card.addEventListener('click', (e) => {
                        // Don't trigger if clicking on buttons inside the card
                        if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                            return;
                        }
                        openFromCard(card);
                    });
                });

                // Attach specific listeners to watch replay buttons
                document.querySelectorAll('.watch-replay-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const card = btn.closest('.event-card');
                        openFromCard(card);
                    });
                });

                // Attach specific listeners to register buttons (prevent card click)
                document.querySelectorAll('.register-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        // Let the button's native behavior handle registration
                    });
                });
            }

            // Close modal functionality
            function closeModal() {
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Close button event
            closeBtn.addEventListener('click', closeModal);

            // Close when clicking outside modal content
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeModal();
                }
            });

            // Close with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && overlay.style.display === 'flex') {
                    closeModal();
                }
            });

            // Initialize event listeners when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Attach listeners to initial static content
                attachEventModalListeners();

                // Re-attach listeners after dynamic content loads
                const originalLoadEvents = window.loadEvents;
                window.loadEvents = async function() {
                    if (originalLoadEvents) {
                        await originalLoadEvents();
                    }
                    // Re-attach listeners after dynamic content is loaded
                    setTimeout(attachEventModalListeners, 500);
                };
            });

        })();
    </script>
</body>

</html>


<!-- Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.style.display === 'block') {
        closeModal();
    }
});

// API Functions for Dynamic Content
async function fetchAPI(endpoint) {
    try {
        const response = await fetch(`/api/get_content.php?${endpoint}`);
        const data = await response.json();
        return data.success ? data.data : null;
    } catch (error) {
        console.error('API Error:', error);
        return null;
    }
}

// Load Blog Posts
async function loadBlogPosts() {
    const blogPosts = await fetchAPI('type=blog_posts&status=published&limit=10');
    if (blogPosts && blogPosts.length > 0) {
        const blogGrid = document.querySelector('#blog-posts .projects-grid');
        if (blogGrid) {
            blogGrid.innerHTML = '';
            
            blogPosts.forEach(post => {
                const blogCard = document.createElement('div');
                blogCard.className = 'project-card blog-card';
                blogCard.innerHTML = `
                    <div class="project-img blog-img">
                        ${post.featured_image ? 
                            `<img src="${post.featured_image}" alt="${post.title}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">` :
                            '<i class="fas fa-blog"></i>'
                        }
                    </div>
                    <div class="project-content">
                        <div class="blog-meta">
                            <span class="blog-date">${new Date(post.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            <span class="blog-category-tag">${post.category || 'General'}</span>
                        </div>
                        <h3 class="project-title">${post.title}</h3>
                        <p class="project-desc">${post.excerpt || post.content.substring(0, 200) + '...'}</p>
                        <div class="blog-author">
                            <p><strong>Author:</strong> ${post.author || 'Kaminova Labs Team'}</p>
                        </div>
                        <button class="btn btn-secondary read-more-btn" data-blog="${post.slug}">Read Full Article</button>
                    </div>
                `;
                blogGrid.appendChild(blogCard);
            });

            // Update blog details for modal
            updateBlogDetails(blogPosts);
            // Re-attach event listeners for new buttons
            attachBlogModalEventListeners();
        }
    }
}

// Load Events
async function loadEvents() {
    // Fetch all active events to render both upcoming and past
    const allEvents = await fetchAPI('type=events&active_only=1&limit=1000');

    // Upcoming
    const upcomingEvents = (allEvents || []).filter(e => String(e.event_type || '').toLowerCase() === 'upcoming');
    if (upcomingEvents && upcomingEvents.length > 0) {
        const upcomingGrid = document.querySelector('#events .event-category .projects-grid');
        if (upcomingGrid) {
            upcomingGrid.innerHTML = '';
            upcomingEvents.forEach(event => {
                const imageSrc = resolveEventImage(event);
                const eventCard = document.createElement('div');
                eventCard.className = 'project-card event-card';
                eventCard.innerHTML = `
                    <div class="project-img event-img">
                        ${imageSrc ? 
                            `<img src="${imageSrc}" alt="${event.title}" class="fit-img">` :
                            `<i class="${event.icon_class || 'fas fa-calendar-alt'}"></i>`
                        }
                    </div>
                    <div class="project-content">
                        <div class="event-meta">
                            <span class="event-date">${event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'TBD'}</span>
                            <span class="event-time">${event.event_time || 'TBD'}</span>
                            <span class="event-location">${event.location || 'TBD'}</span>
                        </div>
                        <h3 class="project-title">${event.title}</h3>
                        <p class="project-desc">${event.description}</p>
                        ${event.speakers ? `<div class="event-speakers">
                            <p><strong>Speakers:</strong> ${event.speakers}</p>
                        </div>` : ''}
                        <div class="event-actions" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            ${event.watch_link ?
                                `<button class="btn btn-secondary watch-replay-btn" data-event="${event.slug}" data-watch-link="${event.watch_link || ''}" onclick="event.stopPropagation(); var l=this.dataset.watchLink; if(l) window.open(l,'_blank');">Watch Replay</button>` :
                                ''}
                            ${event.x_link ?
                                `<a href="${event.x_link}" class="btn btn-outline x-link-btn" target="_blank" onclick="event.stopPropagation()">View Full Post on Twitter</a>` :
                                ''}
                        </div>
                        ${event.registration_link ? 
                            `<a href="${event.registration_link}" class="btn btn-primary register-btn" target="_blank" onclick="event.stopPropagation()">Register Now</a>` :
                            '<button class="btn btn-primary register-btn" disabled onclick="event.stopPropagation()">Registration Coming Soon</button>'
                        }
                    </div>
                `;
                // Dataset for modal
                eventCard.dataset.title = event.title || '';
                eventCard.dataset.date = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';
                eventCard.dataset.time = event.event_time || '';
                eventCard.dataset.location = event.location || '';
                eventCard.dataset.description = event.description || '';
                eventCard.dataset.image = imageSrc || '';
                eventCard.dataset.xLink = event.x_link || '';
                eventCard.dataset.watchLink = event.watch_link || '';
                eventCard.setAttribute('onclick', 'openFromCardInline(this)');
                upcomingGrid.appendChild(eventCard);
            });
        }
    }

    // Past
    const pastEvents = (allEvents || []).filter(e => String(e.event_type || '').toLowerCase() === 'past' || !e.event_type);
    if (pastEvents && pastEvents.length > 0) {
        const pastEventCategory = document.querySelectorAll('.event-category')[1];
        if (pastEventCategory) {
            const pastGrid = pastEventCategory.querySelector('.projects-grid');
            if (pastGrid) {
                pastGrid.innerHTML = '';
                pastEvents.forEach(event => {
                    const imageSrc = resolveEventImage(event);
                    const eventCard = document.createElement('div');
                    eventCard.className = 'project-card event-card past-event';
                    eventCard.innerHTML = `
                        <div class="project-img event-img">
                            ${imageSrc ? 
                                `<img src="${imageSrc}" alt="${event.title}" class="fit-img">` :
                                `<i class="${event.icon_class || 'fas fa-calendar-alt'}"></i>`
                            }
                        </div>
                        <div class="project-content">
                            <div class="event-meta">
                                <span class="event-date">${event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Past Event'}</span>
                                <span class="event-time">${event.event_time || ''}</span>
                                <span class="event-location">${event.location || ''}</span>
                            </div>
                            <h3 class="project-title">${event.title}</h3>
                            <p class="project-desc">${event.description}</p>
                            ${event.speakers ? `<div class="event-speakers">
                                <p><strong>Speakers:</strong> ${event.speakers}</p>
                            </div>` : ''}
                            <div class="event-actions" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button class="btn btn-secondary watch-replay-btn" data-event="${event.slug}" data-watch-link="${event.watch_link || ''}" onclick="event.stopPropagation(); var l=this.dataset.watchLink; if(l) window.open(l,'_blank');">Watch Replay</button>
                                ${event.x_link ?
                                    `<a href="${event.x_link}" class="btn btn-outline x-link-btn" target="_blank" onclick="event.stopPropagation()">View Full Post on Twitter</a>` :
                                    ''}
                            </div>
                        </div>
                    `;
                    // Dataset for modal
                    eventCard.dataset.title = event.title || '';
                    eventCard.dataset.date = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';
                    eventCard.dataset.time = event.event_time || '';
                    eventCard.dataset.location = event.location || '';
                    eventCard.dataset.description = event.description || '';
                    eventCard.dataset.image = imageSrc || '';
                    eventCard.dataset.xLink = event.x_link || '';
                    eventCard.dataset.watchLink = event.watch_link || '';
                    eventCard.setAttribute('onclick', 'openFromCardInline(this)');
                    pastGrid.appendChild(eventCard);
                });
            }
        }
    }
}

// Attach event listeners for event modal functionality
function attachEventModalEventListeners() {
    // Add click listeners to all event cards
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't open modal if clicking on a button
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            
            const eventData = {
                 title: card.dataset.title || '',
                 date: card.dataset.date || '',
                 time: card.dataset.time || '',
                 location: card.dataset.location || '',
                 description: card.dataset.description || '',
                 image: card.dataset.image || '',
                 xLink: card.dataset.xLink || '',
                 watchLink: card.dataset.watchLink || ''
             };
            
            openEventModal(eventData);
        });
    });

    // Add click listeners to watch replay buttons
    const watchReplayBtns = document.querySelectorAll('.watch-replay-btn');
    watchReplayBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent card click
            const watchLink = btn.dataset.watchLink;
            if (watchLink) {
                window.open(watchLink, '_blank');
            }
        });
    });
}

// Open event modal function
 function openEventModal(eventData) {
     const eventModalOverlay = document.getElementById('eventModalOverlay');
     const eventModalTitle = document.getElementById('eventModalTitle');
     const eventModalImage = document.getElementById('eventModalImage');
     const eventModalDate = document.getElementById('eventModalDate');
     const eventModalTime = document.getElementById('eventModalTime');
     const eventModalLocation = document.getElementById('eventModalLocation');
     const eventModalDescription = document.getElementById('eventModalDescription');
     const eventModalWatchBtn = document.getElementById('eventModalWatchBtn');
     const eventModalXBtn = document.getElementById('eventModalXBtn');

     if (eventModalOverlay) {
         // Populate dedicated event modal content
         if (eventModalTitle) eventModalTitle.textContent = eventData.title;
         if (eventModalDate) eventModalDate.textContent = eventData.date;
         if (eventModalTime) eventModalTime.textContent = eventData.time;
         if (eventModalLocation) eventModalLocation.textContent = eventData.location;
         if (eventModalDescription) eventModalDescription.textContent = eventData.description;

         // Handle image
         if (eventModalImage) {
             if (eventData.image) {
                 eventModalImage.src = eventData.image;
                 eventModalImage.style.display = 'block';
             } else {
                 eventModalImage.style.display = 'none';
             }
         }

         // Handle buttons
         if (eventModalWatchBtn && eventData.watchLink) {
             eventModalWatchBtn.style.display = 'inline-block';
             eventModalWatchBtn.onclick = () => window.open(eventData.watchLink, '_blank');
         } else if (eventModalWatchBtn) {
             eventModalWatchBtn.style.display = 'none';
         }

         if (eventModalXBtn && eventData.xLink) {
             eventModalXBtn.style.display = 'inline-block';
             eventModalXBtn.onclick = () => window.open(eventData.xLink, '_blank');
         } else if (eventModalXBtn) {
             eventModalXBtn.style.display = 'none';
         }

         // Show dedicated event modal
         eventModalOverlay.style.display = 'flex';
         document.body.style.overflow = 'hidden';
         return;
     }

     // Fallback: use existing project modal if event modal not present
     const fallbackModal = document.getElementById('projectModal');
     const fallbackBody = document.getElementById('modalBody');
     if (!fallbackModal || !fallbackBody) return;

     const metaHTML = `
         <div class="event-meta" style="margin: 0.5rem 0 1rem;">
             ${eventData.date ? `<p><strong>Date:</strong> ${eventData.date}</p>` : ''}
             ${eventData.time ? `<p><strong>Time:</strong> ${eventData.time}</p>` : ''}
             ${eventData.location ? `<p><strong>Location:</strong> ${eventData.location}</p>` : ''}
         </div>
     `;

     const actionsHTML = `
         <div class="modal-actions" style="display:flex; gap:.5rem; margin-top:1rem; flex-wrap:wrap;">
             ${eventData.watchLink ? `<a class="btn btn-primary" href="${eventData.watchLink}" target="_blank">Watch Replay</a>` : ''}
             ${eventData.xLink ? `<a class="btn btn-secondary" href="${eventData.xLink}" target="_blank">View more on X</a>` : ''}
         </div>
     `;

     fallbackBody.innerHTML = `
         ${eventData.image ? `<img src="${eventData.image}" alt="${eventData.title}" style="width:100%; max-height:300px; object-fit:cover; border-radius:10px; margin-bottom:1rem;">` : ''}
         <h2>${eventData.title || 'Event Details'}</h2>
         ${metaHTML}
         <div class="modal-section">
             <div style="line-height: 1.6; white-space: pre-wrap;">${eventData.description || ''}</div>
         </div>
         ${actionsHTML}
     `;

     fallbackModal.style.display = 'block';
     document.body.style.overflow = 'hidden';
 }

// Update blog details object with database data
function updateBlogDetails(blogPosts) {
    window.blogDetails = {};
    blogPosts.forEach(post => {
        window.blogDetails[post.slug] = {
            title: post.title,
            content: post.content,
            excerpt: post.excerpt,
            author: post.author,
            category: post.category,
            created_at: post.created_at,
            featured_image: post.featured_image
        };
    });
}

// Attach event listeners for blog modal functionality
function attachBlogModalEventListeners() {
    const readMoreBtns = document.querySelectorAll('.read-more-btn');
    readMoreBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const blogKey = btn.getAttribute('data-blog');
            openBlogModal(blogKey);
        });
    });
}

// Open blog modal function
function openBlogModal(blogKey) {
    const blog = window.blogDetails ? window.blogDetails[blogKey] : null;
    if (!blog) return;

    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
        <h2>${blog.title}</h2>
        <div class="blog-meta" style="margin-bottom: 1rem;">
            <p><strong>Author:</strong> ${blog.author || 'Kaminova Labs Team'}</p>
            <p><strong>Category:</strong> ${blog.category || 'General'}</p>
            <p><strong>Published:</strong> ${new Date(blog.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
        </div>
        
        ${blog.featured_image ? `<img src="${blog.featured_image}" alt="${blog.title}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem;">` : ''}
        
        <div class="modal-section">
            <div style="line-height: 1.6; white-space: pre-wrap;">${blog.content}</div>
        </div>
    `;

    const modal = document.getElementById('projectModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Load Site Settings for Footer
async function loadSiteSettings() {
    const settings = await fetchAPI('type=settings');
    if (settings) {
        // Update contact info in footer
        const contactLinks = document.querySelectorAll('.footer-links li');
        contactLinks.forEach(link => {
            const text = link.textContent;
            if (text.includes('Innovation Drive') && settings.contact_address) {
                link.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${settings.contact_address}`;
            }
            if (text.includes('+234') && settings.contact_phone) {
                link.innerHTML = `<i class="fas fa-phone"></i> ${settings.contact_phone}`;
            }
            if (text.includes('info@kaminovalabs.com') && settings.contact_email) {
                link.innerHTML = `<i class="fas fa-envelope"></i> ${settings.contact_email}`;
            }
        });

        // Update company name in footer
        const footerLogo = document.querySelector('.footer-logo');
        if (footerLogo && settings.company_name) {
            footerLogo.textContent = settings.company_name;
        }
    }
}

// Initialize Dynamic Content
async function initializeDynamicContent() {
    await loadSiteSettings();
    await loadBlogPosts();
    await loadEvents();
    
    // Attach event listeners after content is loaded
    attachBlogModalEventListeners();
    attachEventModalEventListeners();
}

// Dropdown functionality
const dropdown = document.querySelector('.dropdown');
const dropdownToggle = document.querySelector('.dropdown-toggle');

if (dropdownToggle) {
    dropdownToggle.addEventListener('click', function (e) {
        // Only prevent default if we're trying to show dropdown, not navigate
        if (!dropdown.classList.contains('active')) {
            e.preventDefault();
            dropdown.classList.add('active');
        } else {
            // Allow navigation when dropdown is already open
            dropdown.classList.remove('active');
        }
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    if (dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});

// Load dynamic content when page loads
document.addEventListener('DOMContentLoaded', initializeDynamicContent);
</script>
</body>

</html>