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
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="img/logo-tp.png" type="image/x-icon">
    <link rel="canonical" href="https://kaminovaglobal.com/contact.php">

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
            <i class="fas fa-satellite-dish"></i> Direct Communications Terminal
        </div>
        <h1 class="hero-title" style="margin-bottom: 1.2rem;">Let's Build The Future Together</h1>
        <p class="hero-desc" style="max-width: 820px; margin: 0 auto; font-size: 1.15rem; color: rgba(255, 255, 255, 0.85);">
            Whether you are exploring transformative blockchain integration, next-generation AI architectures, threat intelligence forensics, or strategic ecosystem advisory, our team is ready to collaborate.
        </p>
    </section>

    <!-- Main Contact Layout Section -->
    <section class="contact-page-wrapper">
        <div class="contact-layout-grid">
            
            <!-- Left Column: Terminal Contact Form -->
            <div class="contact-terminal-card">
                <div class="terminal-header">
                    <div class="terminal-header-title">
                        <i class="fas fa-terminal"></i> Transmission Portal
                    </div>
                    <div class="terminal-status-light">
                        Systems Online &bull; Encrypted
                    </div>
                </div>

                <form id="contactForm" class="contact-form" autocomplete="on">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user-circle"></i> Full Name <span style="color: var(--electric-blue);">*</span></label>
                        <input type="text" id="name" name="name" placeholder="e.g. Dr. Alexander Vance" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address <span style="color: var(--electric-blue);">*</span></label>
                        <input type="email" id="email" name="email" placeholder="e.g. alexander@enterprise.com" required>
                    </div>

                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Inquiry Subject / Department <span style="color: var(--electric-blue);">*</span></label>
                        <input type="text" id="subject" name="subject" list="subject-suggestions" placeholder="Select or type inquiry subject" required>
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
                        <label for="message"><i class="fas fa-comment-alt"></i> Message Details <span style="color: var(--electric-blue);">*</span></label>
                        <textarea id="message" name="message" placeholder="Provide a summary of your inquiry, technical scope, or partnership proposal..." required style="min-height: 160px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="padding: 14px 28px; font-size: 1.05rem;">
                        Transmit Message <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
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

        <!-- Global Regional Nodes Showcase -->
        <div class="regional-nodes-section">
            <h2 class="section-title" style="font-size: 2rem;">Global Innovation Network</h2>
            <p style="text-align: center; color: rgba(255, 255, 255, 0.75); max-width: 700px; margin: 0 auto;">
                Distributed teams and collaborative research nodes driving digital transformation across continents.
            </p>

            <div class="nodes-grid">
                <div class="node-card">
                    <div class="node-region">Primary Hub &bull; West Africa</div>
                    <div class="node-title">Abuja & Lagos Innovation Hubs</div>
                    <div class="node-desc">Core protocol engineering, fintech sandbox partnerships, and strategic blockchain adoption operations.</div>
                </div>

                <div class="node-card">
                    <div class="node-region">European Alliance &bull; UK & EU</div>
                    <div class="node-title">London & Zurich Nodes</div>
                    <div class="node-desc">Decentralized governance research, Web3 zero-knowledge cryptography, and European institutional advisory.</div>
                </div>

                <div class="node-card">
                    <div class="node-region">North America &bull; Global R&D</div>
                    <div class="node-title">Silicon Valley Research Link</div>
                    <div class="node-desc">AI neural model benchmarking, deep forensic algorithms, and cloud native decentralized infrastructure.</div>
                </div>

                <div class="node-card">
                    <div class="node-region">Decentralized Grid &bull; Worldwide</div>
                    <div class="node-title">Autonomous Validator Mesh</div>
                    <div class="node-desc">24/7 global uptime monitoring, peer-to-peer security sensors, and distributed cryptographic validation.</div>
                </div>
            </div>
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
                    You can submit an inquiry via our transmission portal above selecting "Cybersecurity Audit & Threat Intelligence". Please include repository links, architectural documentation, and desired timelines. Our forensics team will provide an initial vulnerability scope and quote within 24 hours.
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
