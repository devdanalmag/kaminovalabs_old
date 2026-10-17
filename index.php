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
}
$SITE_NAME = getSetting($pdo, 'company_name', 'KAMINOVA LABS');
$PHONE = getSetting($pdo, 'contact_phone', '+234 xxxxxxxxxx');
$EMAIL = getSetting($pdo, 'contact_email', 'info@kaminovaglobal.com');
$ADDRESS = getSetting($pdo, 'contact_address', '123 Innovation Drive, Nigeria');

// Fetch active partners for home page logo section
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

// Fetch active team members for home page team section
$team_members = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC LIMIT 4");
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
    <title>Kaminova Labs - Pioneering Digital Innovation | Blockchain, AI & Cybersecurity</title>
    <meta name="description"
        content="Kaminova Labs delivers cutting-edge blockchain, AI, cybersecurity & digital forensics solutions. Transforming industries through next-gen technology innovation since 2015.">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Kaminova Labs - Pioneering Digital Innovation | Blockchain, AI & Cybersecurity">
    <meta name="keywords"
        content="blockchain solutions, AI integration, cybersecurity, digital forensics, technology innovation, digital transformation, emerging technologies, Kaminova Labs">
    <meta name="author" content="Kaminova Labs">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kaminovaglobal.com/">
    <meta property="og:title" content="Kaminova Labs - Pioneering Digital Innovation">
    <meta property="og:description"
        content="Transforming Industries Through Blockchain, AI, Cybersecurity & Digital Forensics. Cutting-edge solutions for the digital age.">
    <meta property="og:image" content="https://kaminovaglobal.com/img/logo3d.png">
    <meta property="og:site_name" content="Kaminova Labs">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://kaminovaglobal.com/">
    <meta property="twitter:title" content="Kaminova Labs - Pioneering Digital Innovation">
    <meta property="twitter:description"
        content="Transforming Industries Through Blockchain, AI, Cybersecurity & Digital Forensics.">
    <meta property="twitter:image" content="https://kaminovaglobal.com/img/logo3d.png">

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
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "Worldwide"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer service"
            },
            "sameAs": []
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
    <link rel="canonical" href="https://kaminovaglobal.com/">
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

        <?php $currentPage = 'home';
        include 'includes/header.php'; ?>


    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-badge">Next-Gen Technology Solutions</span>
                <h1 class="hero-title">Pioneering The Future of Digital Innovation</h1>
                <p class="hero-desc">
                    Transforming Industries Through Blockchain, AI, Cybersecurity & Digital Forensics.
                    We deliver cutting-edge solutions to drive your business forward in the digital age.
                </p>
                <div class="hero-cta">
                    <a href="#services" class="btn btn-primary">Explore Solutions <i class="fas fa-arrow-right"></i></a>
                    <a href="contact.php" class="btn btn-secondary">Start Your Journey</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="main-visual">
                    <img src="img/logo3d.png" alt="Kaminova Logo" srcset="">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                </div>

                <div class="service-wheel">
                    <div class="wheel-center">
                        <div class="center-content">
                            <img src="img/logo3d.png" alt="Kaminova Logo" class="center-logo">
                        </div>
                    </div>

                    <div class="wheel-card" style="--i:1;">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-plug"></i>
                            </div>
                            <h4 class="card-title">AI Solutions</h4>
                            <!-- <p class="card-desc">Intelligent systems for modern businesses</p> -->
                        </div>
                    </div>

                    <div class="wheel-card" style="--i:2;">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <h4 class="card-title">Blockchain</h4>
                            <!-- <p class="card-desc">Secure decentralized applications</p> -->
                        </div>
                    </div>

                    <div class="wheel-card" style="--i:3;">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-cloud"></i>
                            </div>
                            <h4 class="card-title">Cloud Stack</h4>
                            <!-- <p class="card-desc">Scalable and reliable cloud solutions</p> -->
                        </div>
                    </div>

                    <div class="wheel-card" style="--i:4;">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h4 class="card-title">BioFuture</h4>
                            <!-- <p class="card-desc">Future defined by biodiversity and sustainability</p> -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section reveal" id="about">
        <h2 class="section-title">About Kaminova Labs</h2>

        <div class="about-content">
            <div class="about-text">
                <p>
                    Kaminova Labs is a multidisciplinary technology innovation company at the forefront of digital
                    transformation. Our mission is to tackle the most complex challenges in today’s digital economy
                    through advanced research, product development, strategic consulting, and capacity building.

                </p>
                <p>
                    Founded in 2021, Kaminova Labs has evolved into a trusted partner for organizations seeking to
                    harness emerging technologies to achieve sustainable growth and competitive advantage. Our team
                    combines deep expertise in D
                    distributed ledger technologies, artificial intelligence, Digital asset security and emerging
                    technologies, driving innovation across both traditional and decentralized ecosystems.
                </p>
                <p>
                    At Kaminova Labs, we believe technology is more than just code or infrastructure, it is the
                    foundation of progress, transparency, and empowerment in the 21st century. We exist to build that
                    future.
                </p>
            </div>

            <div class="company-image">
                <img src="img/3dlogo.png" alt="" srcset="">
            </div>
        </div>

        <!-- <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number" id="clients">150+</div>
                <div class="stat-label">Clients Worldwide</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="projects">240+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="team">45+</div>
                <div class="stat-label">Expert Team Members</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="years">8+</div>
                <div class="stat-label">Years of Excellence</div>
            </div>
        </div> -->
    </section>

    <!-- Services Section -->
    <section class="section reveal" id="services">
        <h2 class="section-title">Our Services</h2>
        <div class="cards-grid">
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-link"></i></div>
                <h3 class="card-title">Blockchain Solutions</h3>
                <p class="card-description">
                    Building decentralized platforms that ensure transparency, security, and trust in digital
                    ecosystems.
                    We create blockchain solutions that reshape industries and set new standards.
                </p>
            </div>
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-robot"></i></div>
                <h3 class="card-title">AI Integration</h3>
                <p class="card-description">
                    Harnessing artificial intelligence to create intelligent systems that learn, adapt, and solve
                    complex
                    problems with unprecedented efficiency and accuracy.
                </p>
            </div>
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-shield-alt"></i></div>
                <h3 class="card-title">Cybersecurity</h3>
                <p class="card-description">
                    Protecting digital assets with advanced threat detection, risk management, and comprehensive
                    security strategies tailored to modern challenges.
                </p>
            </div>
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-search"></i></div>
                <h3 class="card-title">Digital Forensics</h3>
                <p class="card-description">
                    Investigating and analyzing digital evidence with cutting-edge forensic techniques to ensure
                    accountability and justice in the digital realm.
                </p>
            </div>
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-lightbulb"></i></div>
                <h3 class="card-title">Consultancy</h3>
                <p class="card-description">
                    Guiding organizations through digital transformation with expert advisory services that align
                    technology adoption with business goals and compliance requirements.
                </p>
            </div>
            <div class="holographic-card">
                <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3 class="card-title">Capacity Building</h3>
                <p class="card-description">
                    Developing the next generation of tech leaders through comprehensive training programs,
                    workshops, and mentorship initiatives.
                </p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="mission" class="section reveal">
        <h2 class="section-title">Our Mission</h2>
        <p class="hero-desc" style="text-align: center; max-width: 800px; margin: 0 auto 3rem;">Our mission is built
            around five key commitments:</p>
        <div class="mission-grid">
            <div class="mission-card">
                <div class="mission-card-inner">
                    <div class="mission-card-front">
                        <h3>Driving Innovation in Emerging Technologies</h3>
                        <p>Lead pioneering research and development in blockchain, AI, cybersecurity, and digital
                            forensics.</p>
                    </div>
                    <div class="mission-card-back">
                        <p>Translate emerging technologies into practical solutions that address security, transparency,
                            and compliance challenges. Build tools and platforms that reshape industries while setting
                            new standards for trust and accountability.</p>
                    </div>
                </div>
            </div>
            <div class="mission-card">
                <div class="mission-card-inner">
                    <div class="mission-card-front">
                        <h3>Providing Expert Consultancy & Advisory Services</h3>
                        <p>Support businesses, governments, and institutions in navigating digital transformation.</p>
                    </div>
                    <div class="mission-card-back">
                        <p>Offer advisory services on blockchain adoption, cybersecurity strategy, digital forensics,
                            and AI integration. Guide organizations to adopt emerging technologies in ways that align
                            with sustainability, compliance, and long-term growth.</p>
                    </div>
                </div>
            </div>
            <div class="mission-card">
                <div class="mission-card-inner">
                    <div class="mission-card-front">
                        <h3>Capacity Building & Education</h3>
                        <p>Develop training programs, workshops, and mentorship initiatives.</p>
                    </div>
                    <div class="mission-card-back">
                        <p>Build a talent pipeline equipped with the knowledge and skills to thrive in the digital
                            economy. Foster partnerships with universities, research centers, and communities to expand
                            digital literacy and innovation capacity.</p>
                    </div>
                </div>
            </div>
            <div class="mission-card">
                <div class="mission-card-inner">
                    <div class="mission-card-front">
                        <h3>Promoting Transparency, Governance & Security</h3>
                        <p>Build platforms that ensure accountability, transparency, and trust.</p>
                    </div>
                    <div class="mission-card-back">
                        <p>Strengthen cybersecurity resilience through advanced solutions in threat detection, risk
                            management, and forensic analysis. Contribute to building secure, ethical, and transparent
                            governance structures in decentralized environments.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="section reveal" id="projects">
        <h2 class="section-title">Our Projects</h2>

        <!-- BioFuture & Governance -->
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
                        <p class="project-desc">Climate, Trust & Tokenized Sustainability - A Web3 ecosystem for climate
                            tokenization, trust networks, and incentive-driven governance.</p>
                        <a href="projects.html" class="btn btn-secondary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Modal -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="modal-close" id="modalClose">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Project details will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <section class="section reveal" id="team">
        <h2 class="section-title">Leadership & Research Team</h2>
        <p class="hero-desc" style="text-align: center; max-width: 800px; margin: 0 auto 3rem;">
            Multidisciplinary innovators pioneering breakthroughs across cryptographic protocols, AI architectures, and offensive cybersecurity.
        </p>

        <?php if (!empty($team_members)): ?>
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
                                <p class="team-member-bio"><?= htmlspecialchars(mb_strimwidth($member['bio'], 0, 140, '...')) ?></p>
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

            <div style="text-align: center; margin-top: 2rem;">
                <a href="team.php" class="btn btn-secondary">
                    <span>Meet the Full Team</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Ecosystem Partners Section -->
    <section class="section reveal" id="partners">
        <h2 class="section-title">Ecosystem Partners</h2>
        <p class="hero-desc" style="text-align: center; max-width: 800px; margin: 0 auto 3rem;">
            Collaborating with pioneering Web3 protocols, AI platforms, cybersecurity innovators, and enterprise networks.
        </p>

        <div class="partners-home-wrapper">
            <div class="partners-logo-grid">
                <?php if (empty($partners)): ?>
                    <p style="color: rgba(255,255,255,0.6); text-align: center; grid-column: 1 / -1;">
                        Partner network updating...
                    </p>
                <?php else: ?>
                    <?php foreach ($partners as $partner): ?>
                        <a href="partners.php" 
                           class="partner-logo-item" 
                           title="<?= htmlspecialchars($partner['name']) ?><?= !empty($partner['title']) ? ' - ' . htmlspecialchars($partner['title']) : '' ?>"
                           aria-label="<?= htmlspecialchars($partner['name']) ?>">
                            <?php if (!empty($partner['logo'])): ?>
                                <img src="<?= htmlspecialchars($partner['logo']) ?>" 
                                     alt="<?= htmlspecialchars($partner['name']) ?>" 
                                     class="home-partner-logo"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="fallback-logo-text" style="display: none;">
                                    <i class="fas fa-handshake"></i>
                                    <span><?= htmlspecialchars($partner['name']) ?></span>
                                </div>
                            <?php else: ?>
                                <div class="fallback-logo-text">
                                    <i class="fas fa-handshake"></i>
                                    <span><?= htmlspecialchars($partner['name']) ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="partner-tooltip"><?= htmlspecialchars($partner['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="partners-cta-row">
                <a href="partners.php" class="btn btn-secondary">
                    <span>Explore All Ecosystem Partners</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Company Achievements Section -->
    <section class="section reveal" id="achievements">
        <h2 class="section-title">Our Impact & Achievements</h2>
        <div class="achievements-container">
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="achievement-number">50+</div>
                <div class="achievement-label">Projects Delivered</div>
                <p class="achievement-desc">Successfully completed innovative solutions across multiple industries</p>
            </div>
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="achievement-number">99.9%</div>
                <div class="achievement-label">Security Success Rate</div>
                <p class="achievement-desc">Maintaining the highest standards in cybersecurity implementations</p>
            </div>
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="achievement-number">25+</div>
                <div class="achievement-label">Enterprise Clients</div>
                <p class="achievement-desc">Trusted by leading organizations worldwide for digital transformation</p>
            </div>
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="achievement-number">300%</div>
                <div class="achievement-label">Average ROI Increase</div>
                <p class="achievement-desc">Delivering measurable business value through innovative technology</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section reveal" id="contact">
        <h2 class="section-title">Get In Touch</h2>
        <div class="contact-container">
            <div class="contact-form-wrapper">
                <h3>Send Us a Message</h3>
                <form id="contactForm" class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Your Message" required style="min-height: 150px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Message <i class="fas fa-paper-plane"></i></button>
                    <div id="formStatus" class="form-status"></div>
                </form>
            </div>
            <div class="contact-info-wrapper">
                <h3>Contact Information</h3>
                <p class="contact-intro">Join us in building a digital world where innovation creates value for all.</p>
                <div style="margin-bottom: 25px;">
                    <a href="contact.php" class="btn btn-secondary w-100" style="padding: 10px 16px; font-size: 0.9rem; text-align: center; justify-content: center;">
                        <i class="fas fa-satellite-dish"></i> Dedicated Contact & Regional Terminal <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                    </a>
                </div>
                <div class="info-cards">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h4>Our Headquarters</h4>
                            <p><?= htmlspecialchars($ADDRESS) ?></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h4>Call Us</h4>
                            <p><?= htmlspecialchars($PHONE) ?></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h4>Email Us</h4>
                            <p><?= htmlspecialchars($EMAIL) ?></p>
                        </div>
                    </div>
                </div>
                <div class="social-connect">
                    <h4>Connect With Us</h4>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>

</html>