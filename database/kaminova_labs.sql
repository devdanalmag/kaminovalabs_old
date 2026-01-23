-- Kaminova Labs Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS kaminova_labs;
USE kaminova_labs;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    author VARCHAR(100),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(100) NOT NULL,
    status VARCHAR(100),
    short_description TEXT,
    full_description LONGTEXT,
    features JSON,
    team_role VARCHAR(100),
    team_name VARCHAR(100),
    project_image VARCHAR(255),
    icon_class VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Team members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT,
    image VARCHAR(255),
    email VARCHAR(100),
    linkedin VARCHAR(255),
    twitter VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site settings table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('text', 'textarea', 'email', 'phone', 'url', 'json') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    event_date DATE,
    event_time TIME,
    location VARCHAR(255),
    event_type ENUM('upcoming', 'past') DEFAULT 'upcoming',
    speakers TEXT,
    registration_link VARCHAR(255),
    watch_link VARCHAR(255),
    x_link VARCHAR(255),
    featured_image VARCHAR(255),
    icon_class VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO admin_users (username, email, password) VALUES 
('admin', 'admin@kaminovalabs.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('company_name', 'Kaminova Labs', 'text', 'Company Name'),
('company_tagline', 'Pioneering Digital Innovation', 'text', 'Company Tagline'),
('contact_email', 'info@kaminovalabs.com', 'email', 'Contact Email'),
('contact_phone', '+234 xxxxxxxxxx', 'phone', 'Contact Phone'),
('contact_address', '123 Innovation Drive, Nigeria', 'text', 'Contact Address'),
('about_description', 'We are a multidisciplinary technology innovation company at the forefront of digital transformation.', 'textarea', 'About Description'),
('hero_title', 'Pioneering The Future of Digital Innovation', 'text', 'Hero Section Title'),
('hero_description', 'Transforming Industries Through Blockchain, AI, Cybersecurity & Digital Forensics. We deliver cutting-edge solutions to drive your business forward in the digital age.', 'textarea', 'Hero Section Description'),
('stats_clients', '150', 'text', 'Number of Clients'),
('stats_projects', '240', 'text', 'Number of Projects'),
('stats_team', '45', 'text', 'Team Members'),
('stats_years', '8', 'text', 'Years of Experience');

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, excerpt, content, category, author, status) VALUES
('The Future of AI in Blockchain: Revolutionizing Digital Trust', 'ai-blockchain-future', 'Exploring how artificial intelligence is transforming blockchain technology, from smart contract optimization to predictive analytics in decentralized systems.', 'Exploring how artificial intelligence is transforming blockchain technology, from smart contract optimization to predictive analytics in decentralized systems. Discover the cutting-edge innovations shaping tomorrow\'s digital landscape.', 'AI & Innovation', 'Dr. Sarah Chen', 'published'),
('Building Sustainable Web3: Green Blockchain Solutions for Climate Action', 'green-web3-sustainability', 'How blockchain technology is being leveraged to combat climate change through carbon tokenization, renewable energy tracking, and sustainable supply chain management.', 'How blockchain technology is being leveraged to combat climate change through carbon tokenization, renewable energy tracking, and sustainable supply chain management. A deep dive into our GreenTrust Nexus project.', 'Sustainability', 'Michael Rodriguez', 'published');

-- Insert sample projects
INSERT INTO projects (title, slug, category, status, short_description, full_description, features, team_role, team_name, icon_class, display_order) VALUES
('GreenTrust Nexus', 'greentrust-nexus', 'BioFuture & Governance', 'Under Development', 'Climate, Trust & Tokenized Sustainability - A Web3 ecosystem for climate tokenization, trust networks, and incentive-driven governance.', 'A comprehensive Web3 ecosystem designed for climate tokenization, trust networks, and incentive-driven governance. GreenTrust Nexus aims to revolutionize how we approach environmental sustainability through blockchain technology, creating transparent and verifiable carbon credit systems, environmental impact tracking, and community-driven governance mechanisms.', '["Climate tokenization and carbon credit management", "Trust network establishment for environmental projects", "Incentive-driven governance for sustainability initiatives", "Transparent environmental impact tracking", "Community voting on climate projects"]', 'Founder', 'Team Lead', 'fas fa-leaf', 1),
('NodeWhisper', 'nodewhisper', 'Forensics & Security', 'Under Development', 'Blockchain Forensics & Threat Intelligence Platform - Next-gen forensic tool with threat heatmaps, behavioral fingerprinting, and collaborative case rooms.', 'Next-generation forensic tool equipped with advanced threat heatmaps, behavioral fingerprinting, and collaborative case rooms. NodeWhisper provides comprehensive blockchain analysis capabilities for law enforcement, compliance teams, and security professionals.', '["Advanced threat heatmaps and visualization", "Behavioral fingerprinting for suspicious activities", "Collaborative case rooms for team investigations", "Real-time blockchain transaction monitoring", "Comprehensive forensic reporting tools"]', 'Founder', 'Team Lead', 'fas fa-search', 2);