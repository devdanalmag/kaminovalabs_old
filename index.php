<?php
require_once __DIR__ . '/config/database.php';
$pdo = null;
try {
    $pdo = getDB();
} catch (Throwable $e) {
    $pdo = null;
}
function getSetting($pdo, $key, $default = '') {
    if (!$pdo) return $default;
    try {
        $stmt = $pdo->prepare("SELECT setting_value AS v FROM site_settings WHERE setting_key = :k LIMIT 1");
        $stmt->execute([':k' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['v']) && $row['v'] !== null && $row['v'] !== '') return $row['v'];
    } catch (Throwable $e) {}
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
    <title>Kaminova Labs - Pioneering Digital Innovation</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">
</head>

<body>
    <!-- Loader -->
    <!-- <div class="loader" id="loader">
        <div class="loader-circle"></div>
    </div> -->

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
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#mission">Mission</a></li>
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
            <li><a href="blog.php">Blog</a></li>
            <li><a href="#achievements">Achievements</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>

        <!-- <a href="#" class="header-cta">Get Started</a> -->

        <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </div>
    </header>

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
                    <a href="#contact" class="btn btn-secondary">Start Your Journey</a>
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
                    We are a multidisciplinary technology innovation company at the forefront of digital transformation.
                    Our mission is to solve the most complex challenges in today's digital economy through cutting-edge
                    research, product development, consulting, and capacity building.
                </p>
                <p>
                    Founded in 2015, Kaminova Labs has grown to become a trusted partner for organizations seeking to
                    leverage emerging technologies for competitive advantage. Our team of experts brings decades of
                    experience in blockchain, artificial intelligence, cybersecurity, and digital forensics.
                </p>
                <p>
                    We believe technology is more than just tools and code — it is the foundation of progress,
                    transparency, and empowerment in the 21st century.
                </p>
            </div>

            <div class="company-image">
                <img src="img/3dlogo.png" alt="" srcset="">
            </div>
        </div>

        <div class="stats-container">
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
        </div>
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
                        <p class="project-desc">Climate, Trust & Tokenized Sustainability - A Web3 ecosystem for climate tokenization, trust networks, and incentive-driven governance.</p>
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

    <!-- Team Section
    <section class="section reveal" id="team">
        <h2 class="section-title">Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-img">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="member-name">Dr. Elena Kaminova</h3>
                <p class="member-role">Founder & CEO</p>
            </div>
            <div class="team-member">
                <div class="member-img">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="member-name">Marcus Chen</h3>
                <p class="member-role">CTO</p>
            </div>
            <div class="team-member">
                <div class="member-img">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="member-name">Sophia Williams</h3>
                <p class="member-role">AI Research Lead</p>
            </div>
            <div class="team-member">
                <div class="member-img">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="member-name">David Okon</h3>
                <p class="member-role">Security Director</p>
            </div>
        </div>
    </section> -->

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
    <section class="contact-section reveal" id="contact">
        <h2 class="section-title">Ready to Transform Your Future?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 40px; color: rgba(255,255,255,0.9);">
            Join us in building a digital world where innovation creates value for all.
        </p>
        <div class="hero-cta">
            <a href="#" class="btn btn-primary">Get Started</a>
            <a href="#" class="btn btn-secondary">Learn More</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>

</html>