<?php
/**
 * Reusable Header Component
 * 
 * Usage: Set $currentPage before including this file
 * Example: $currentPage = 'events'; include 'includes/header.php';
 * 
 * Valid $currentPage values: 'home', 'about', 'services', 'mission', 'projects', 'events', 'achievements', 'contact'
 */

// Default to empty if not set
$currentPage = $currentPage ?? '';

if (!function_exists('isActive')) {
    function isActive($page, $currentPage)
    {
        return $page === $currentPage ? 'current-page' : '';
    }
}
?>
<header id="header">
    <a href="./" class="logo">
        <img src="img/logo3d.png" alt="KAMINOVA LABS">
        <?= htmlspecialchars($SITE_NAME ?? 'KAMINOVA LABS') ?>
    </a>

    <ul class="nav-links">
        <li><a href="./" class="<?= isActive('home', $currentPage) ?>">Home</a></li>
        <li><a href="index.php#about" class="<?= isActive('about', $currentPage) ?>">About</a></li>
        <li><a href="index.php#services" class="<?= isActive('services', $currentPage) ?>">Services</a></li>
        <li><a href="index.php#mission" class="<?= isActive('mission', $currentPage) ?>">Mission</a></li>
        <li class="dropdown">
            <a href="projects.php" class="dropdown-toggle <?= isActive('projects', $currentPage) ?>">Projects <i
                    class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
                <li><a href="projects.php#biofuture-governance">BioFuture & Governance</a></li>
                <li><a href="projects.php#forensics-security">Forensics & Security</a></li>
                <li><a href="projects.php#logistics-utility">Logistics & Utility</a></li>
                <li><a href="projects.php#finance-payments">Finance & Payments</a></li>
                <li><a href="projects.php#ecosystem-advisory">Ecosystem & Advisory</a></li>
            </ul>
        </li>
        <li><a href="contact.php#team">Team</a></li>
        <li><a href="partners.php" class="<?= isActive('partners', $currentPage) ?>">Partners</a></li>
        <li><a href="event.php" class="<?= isActive('events', $currentPage) ?>">Events</a></li>
        <li><a href="blog.php" class="<?= isActive('blog', $currentPage) ?>">Blog</a></li>
        <li><a href="contact.php" class="<?= isActive('contact', $currentPage) ?>">Contact</a></li>
    </ul>

    <!-- <a href="#" class="header-cta">Get Started</a> -->

    <div class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </div>
</header>