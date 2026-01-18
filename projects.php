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
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kaminova Labs — Projects | Blockchain, AI & Cybersecurity</title>
        <meta name="description"
            content="Explore Kaminova Labs projects: blockchain, AI, cybersecurity and digital forensics initiatives currently in development, advisory or research.">

        <!-- Primary Meta Tags -->
        <meta name="title" content="Kaminova Labs — Projects | Blockchain, AI & Cybersecurity">
        <meta name="keywords"
            content="Kaminova Labs projects, blockchain projects, AI projects, cybersecurity projects, digital forensics">
        <meta name="author" content="Kaminova Labs">
        <meta name="robots" content="index, follow">
        <meta name="language" content="English">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://kaminovaglobal.com/projects">
        <meta property="og:title" content="Kaminova Labs — Projects">
        <meta property="og:description"
            content="Explore Kaminova Labs projects: blockchain, AI, cybersecurity and digital forensics initiatives currently in development, advisory or research.">
        <meta property="og:image" content="https://kaminovaglobal.com/img/logo3d.png">
        <meta property="og:site_name" content="Kaminova Labs">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="https://kaminovaglobal.com/projects">
        <meta name="twitter:title" content="Kaminova Labs — Projects">
        <meta name="twitter:description"
            content="Explore Kaminova Labs projects: blockchain, AI, cybersecurity and digital forensics initiatives.">
        <meta name="twitter:image" content="https://kaminovaglobal.com/img/logo3d.png">

        <!-- Additional SEO Meta Tags -->
        <meta name="theme-color" content="#000000">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <!-- Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Kaminova Labs",
            "url": "https://kaminovaglobal.com",
            "logo": "https://kaminovaglobal.com/img/logo3d.png",
            "description": "Multidisciplinary technology innovation company specializing in blockchain, AI, cybersecurity, and digital forensics",
            "foundingDate": "2015",
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer service"
            }
        }
    </script>

        <!-- Preload Critical Resources -->
        <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" as="script">
        <link rel="preload" href="styles.css" as="style">

        <!-- External Resources -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="styles.css">
        <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">

        <!-- Canonical URL -->
        <link rel="canonical" href="https://kaminovaglobal.com/projects">

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
            <li><a href="index#about">About</a></li>
            <li><a href="index#services">Services</a></li>
            <li><a href="index#mission">Mission</a></li>
            <li class="dropdown">
                <a href="projects" class="dropdown-toggle current-page">Projects <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="projects#biofuture-governance">BioFuture & Governance</a></li>
                    <li><a href="projects#forensics-security">Forensics & Security</a></li>
                    <li><a href="projects#logistics-utility">Logistics & Utility</a></li>
                    <li><a href="projects#finance-payments">Finance & Payments</a></li>
                    <li><a href="projects#ecosystem-advisory">Ecosystem & Advisory</a></li>
                </ul>
            </li>
            <!-- <li><a href="#team">Team</a></li> -->
            <li><a href="blog">Blog</a></li>
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
    </section> -->
    <!-- BioFuture & Governance -->
    <section class="section reveal" id="biofuture-governance">
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title" style="padding-top: 30px;">Projects</h1>
                <p class="hero-subtitle">Pioneering innovations across multiple domains</p>
            </div>
        </div>
        <div class="container">
            <div class="project-category">
                <h3 class="category-title">BioFuture & Governance</h3>
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">GreenTrust Nexus</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">Climate, Trust & Tokenized Sustainability - A Web3 ecosystem for
                                climate tokenization, trust networks, and incentive-driven governance.</p>
                            <div class="project-team">
                                <p><strong>Founder:</strong> Team Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="greentrust">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Forensics & Security -->
    <section class="section reveal" id="forensics-security">
        <div class="container">
            <div class="project-category">
                <h3 class="category-title">Forensics & Security</h3>
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">NodeWhisper</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">Blockchain Forensics & Threat Intelligence Platform - Next-gen
                                forensic tool with threat heatmaps, behavioral fingerprinting, and collaborative case
                                rooms.</p>
                            <div class="project-team">
                                <p><strong>Founder:</strong> Team Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="nodewhisper">Read
                                More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">BADIS</h3>
                            <p class="project-status">(Blockchain-Agnostic DApp Immune System)</p>
                            <p class="project-desc">A Novel Idea proposal to Open Zeppelin - AI-Driven Real-Time DApp
                                Security with cross-chain immune system delivering automated AI threat detection and
                                real-time patching.</p>
                            <div class="project-team">
                                <p><strong>Founder:</strong> Team Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="badis">Read More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-user-secret"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">DPM</h3>
                            <p class="project-status">(Decentralized Privacy Mesh) - Coming Soon</p>
                            <p class="project-desc">Reinventing Privacy in Modular Blockchain Ecosystems - Privacy mesh
                                with ZK-proofs and encryption for modular blockchains.</p>
                            <div class="project-team">
                                <p><strong>Founder:</strong> Team Lead</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="dpm">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logistics & Utility -->
    <section class="section reveal" id="logistics-utility">
        <div class="container">
            <div class="project-category">
                <h3 class="category-title">Logistics & Utility</h3>
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">HashPack Logistics</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">Reinventing Africa's Deliveries - Blockchain-powered last-mile
                                delivery with real-time tracking, secure payments, and rider/vendor trust.</p>
                            <div class="project-team">
                                <p><strong>CTO:</strong> Chief Technology Officer</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="hashpack">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Finance & Payments -->
    <section class="section reveal" id="finance-payments">
        <div class="container">
            <div class="project-category">
                <h3 class="category-title">Finance & Payments</h3>
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">HederaPay</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">Decentralized Bill & Mobile Top-Up Manager - A Web3-powered app for
                                Africa that tracks airtime/data, enables instant top-ups with HBAR or stablecoins.</p>
                            <div class="project-team">
                                <p><strong>CTO:</strong> Chief Technology Officer</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="hederapay">Read More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">HashSwap</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">DEX for Seamless Hedera Trading - A decentralized exchange built on
                                Hedera, enabling token swaps, liquidity pools, and fast, low-cost transactions.</p>
                            <div class="project-team">
                                <p><strong>CTO:</strong> Chief Technology Officer</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="hashswap">Read More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">Trustfound</h3>
                            <p class="project-status">(Under Development)</p>
                            <p class="project-desc">Decentralized Donation & Transparency Platform - A Hedera-powered
                                dApp for donations (Zakat, Sadaqah, Charity) with immutable tracking.</p>
                            <div class="project-team">
                                <p><strong>CTO:</strong> Chief Technology Officer</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="trustfound">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ecosystem & Advisory Projects -->
    <section class="section reveal" id="ecosystem-advisory">
        <div class="container">
            <div class="project-category">
                <h3 class="category-title">Ecosystem & Advisory Projects</h3>
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">AgroChainX</h3>
                            <p class="project-status">(Advisory & Investor)</p>
                            <p class="project-desc">Blockchain Marketplace for Farmers & Traders - Mobile app enabling
                                farmers and traders to conduct secure commercial transactions.</p>
                            <div class="project-team">
                                <p><strong>Role:</strong> Advisory & Investor</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="agrochainx">Read More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">Smart Agriculture Detection</h3>
                            <p class="project-status">(Advisory & Investor)</p>
                            <p class="project-desc">AI-Powered Crop, Pest & Soil Analysis - An AI solution that provides
                                crop detection, pest/disease identification with treatment recommendations.</p>
                            <div class="project-team">
                                <p><strong>Role:</strong> Advisory & Investor</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="smartag">Read More</button>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">Safeguard</h3>
                            <p class="project-status">(Advisory)</p>
                            <p class="project-desc">SME Operating System for Africa - An integrated online & offline
                                platform empowering African small businesses with inventory management, payments,
                                e-commerce.</p>
                            <div class="project-team">
                                <p><strong>Role:</strong> Advisory</p>
                            </div>
                            <button class="btn btn-secondary read-more-btn" data-project="safeguard">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>

    <script>
        // Three.js 3D Scene with Interactive Controls
        // Project Modal Functionality
        const modal = document.getElementById('projectModal');
        const modalBody = document.getElementById('modalBody');
        const modalClose = document.getElementById('modalClose');
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
                team: [
                    { role: "Founder", name: "Team Lead" }
                ]
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
                team: [
                    { role: "Founder", name: "Team Lead" }
                ]
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
                team: [
                    { role: "Founder", name: "Team Lead" }
                ]
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
                team: [
                    { role: "Founder", name: "Team Lead" }
                ]
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
                team: [
                    { role: "CTO", name: "Chief Technology Officer" }
                ]
            },
            hederapay: {
                title: "HederaPay",
                status: "Under Development",
                category: "Finance & Payments",
                description: "Decentralized Bill & Mobile Top-Up Manager",
                fullDescription: "A comprehensive Web3-powered app designed for the African market that tracks airtime and data usage, enables instant top-ups with HBAR or stablecoins, and executes secure bill payments through Hedera smart contracts with immutable receipts and budgeting insights.",
                features: [
                    "Airtime and data usage tracking",
                    "Instant top-ups with HBAR and stablecoins",
                    "Secure bill payment through smart contracts",
                    "Immutable transaction receipts",
                    "Advanced budgeting and financial insights"
                ],
                team: [
                    { role: "CTO", name: "Chief Technology Officer" }
                ]
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
                team: [
                    { role: "CTO", name: "Chief Technology Officer" }
                ]
            },
            trustfound: {
                title: "Trustfound",
                status: "Under Development",
                category: "Finance & Payments",
                description: "Decentralized Donation & Transparency Platform",
                fullDescription: "A Hedera-powered decentralized application designed for donations including Zakat, Sadaqah, Charity in HBAR, stablecoins, or tokens. Trustfound uses the Hedera Consensus Service for immutable tracking, smart contracts for fair fund distribution, and provides a real-time public dashboard for complete transparency.",
                features: [
                    "Multi-currency donation support (HBAR, stablecoins, tokens)",
                    "Immutable donation tracking via Hedera Consensus Service",
                    "Smart contract-based fair fund distribution",
                    "Real-time public transparency dashboard",
                    "Zakat, Sadaqah, and charity categorization"
                ],
                team: [
                    { role: "CTO", name: "Chief Technology Officer" }
                ]
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
                team: [
                    { role: "Advisory & Investor", name: "Strategic Advisor" }
                ]
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
                team: [
                    { role: "Advisory & Investor", name: "Strategic Advisor" }
                ]
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
                team: [
                    { role: "Advisory", name: "Strategic Advisor" }
                ]
            }
        };

        // Open modal function
        function openModal(projectKey) {
            const project = window.projectDetails ? window.projectDetails[projectKey] : null;
            if (!project) return;

            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
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

            const modal = document.getElementById('projectModal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Close modal function
        function closeModal() {
            const modal = document.getElementById('projectModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Event listeners for modal
        readMoreBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const projectKey = btn.getAttribute('data-project');
                openModal(projectKey);
            });
        });

        modalClose.addEventListener('click', closeModal);

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
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
                const response = await fetch(`/api/get_content?${endpoint}`);
                const data = await response.json();
                return data.success ? data.data : null;
                console.log(data.data);
            } catch (error) {
                console.error('API Error:', error);
                return null;
            }
        }

        // Load Projects by Category
        async function loadProjectsByCategory() {
            const projects = await fetchAPI('type=projects&active_only=1');
            if (projects && projects.length > 0) {
                // Group projects by category
                const categories = {
                    'BioFuture & Governance': [],
                    'Forensics & Security': [],
                    'Logistics & Utility': [],
                    'Finance & Payments': [],
                    'Ecosystem & Advisory Projects': []
                };

                projects.forEach(project => {
                    if (categories[project.category]) {
                        categories[project.category].push(project);
                    }
                });

                // Update each category section
                Object.keys(categories).forEach(categoryName => {
                    const categoryProjects = categories[categoryName];
                    const sectionId = categoryName.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-');
                    const section = document.getElementById(sectionId) || document.querySelector(`[id*="${sectionId.split('-')[0]}"]`);

                    if (section && categoryProjects.length > 0) {
                        const projectsGrid = section.querySelector('.projects-grid');
                        if (projectsGrid) {
                            projectsGrid.innerHTML = '';

                            categoryProjects.forEach(project => {
                                const projectCard = document.createElement('div');
                                projectCard.className = 'project-card';
                                projectCard.innerHTML = `
                                    <div class="project-img">
                                        <i class="${project.icon_class || 'fas fa-project-diagram'}"></i>
                                    </div>
                                    <div class="project-content">
                                        <h3 class="project-title">${project.title}</h3>
                                        <p class="project-status">(${project.status})</p>
                                        <p class="project-desc">${project.short_description}</p>
                                        <div class="project-team">
                                            <p><strong>${project.team_role}:</strong> ${project.team_name}</p>
                                        </div>
                                        <button class="btn btn-secondary read-more-btn" data-project="${project.slug}">Read More</button>
                                    </div>
                                `;
                                projectsGrid.appendChild(projectCard);
                            });
                        }
                    }
                });

                // Update project details for modal
                updateProjectDetails(projects);
                // Re-attach event listeners for new buttons
                attachModalEventListeners();
            }
        }

        // Update project details object with database data
        function updateProjectDetails(projects) {
            window.projectDetails = {};
            projects.forEach(project => {
                window.projectDetails[project.slug] = {
                    title: project.title,
                    status: project.status,
                    category: project.category,
                    description: project.short_description,
                    fullDescription: project.full_description,
                    features: project.features || [],
                    team: [
                        { role: project.team_role, name: project.team_name }
                    ]
                };
            });
        }

        // Attach event listeners for modal functionality
        function attachModalEventListeners() {
            const readMoreBtns = document.querySelectorAll('.read-more-btn');
            readMoreBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const projectKey = btn.getAttribute('data-project');
                    openModal(projectKey);
                });
            });
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
            await loadProjectsByCategory();
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