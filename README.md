# Kaminova Labs - Admin Panel

A comprehensive PHP admin panel for managing the Kaminova Labs website content including blog posts, projects, events, team members, and site settings.

## Features

- **Blog Management**: Create, edit, and delete blog posts with image uploads
- **Project Management**: Manage projects with categories, features, and team information
- **Event Management**: Handle upcoming and past events with registration links
- **Team Management**: Add and manage team members with profiles
- **Site Settings**: Update contact information, company details, and statistics
- **Secure Authentication**: Login system with session management
- **Responsive Design**: Modern, mobile-friendly admin interface
- **File Uploads**: Support for image uploads with automatic directory creation
- **API Integration**: RESTful API for frontend content loading

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP (for local development)

### Setup Instructions

1. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u root -p < database/kaminova_labs.sql
   ```

2. **Configuration**
   - Update database credentials in `config/database.php` if needed
   - Default settings are configured for XAMPP (localhost, root, no password)

3. **File Permissions**
   ```bash
   # Create upload directories and set permissions
   mkdir -p uploads/{blog,projects,team,events}
   chmod 755 uploads/
   chmod 755 uploads/*
   ```

4. **Admin Access**
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`
   - Change these credentials after first login

## Directory Structure

```
kaminovalabs_old/
├── admin/                  # Admin panel files
│   ├── assets/            # CSS and JS files
│   ├── auth.php           # Authentication system
│   ├── login.php          # Login page
│   ├── dashboard.php      # Main dashboard
│   ├── blog.php           # Blog management
│   ├── projects.php       # Project management
│   ├── events.php         # Event management
│   ├── team.php           # Team management
│   └── settings.php       # Site settings
├── api/                   # API endpoints
│   └── get_content.php    # Content API
├── config/                # Configuration files
│   └── database.php       # Database connection
├── database/              # Database schema
│   └── kaminova_labs.sql  # SQL setup file
├── uploads/               # File uploads
│   ├── blog/             # Blog images
│   ├── projects/         # Project images
│   ├── team/             # Team photos
│   └── events/           # Event images
├── index.html            # Main website
├── blog.html             # Blog page
└── projects.html         # Projects page
```

## Usage

### Admin Panel Access

1. Navigate to `/admin/` in your browser
2. Login with admin credentials
3. Use the sidebar navigation to access different sections

### Managing Content

#### Blog Posts
- Create new posts with rich text content
- Upload featured images
- Set publication status (draft/published)
- Organize by categories and authors

#### Projects
- Add projects with detailed descriptions
- Categorize by project type
- Include feature lists and team information
- Set display order and active status

#### Events
- Create upcoming and past events
- Set dates, times, and locations
- Add speaker information
- Include registration links

#### Team Members
- Add team member profiles
- Upload profile photos
- Include social media links
- Set display order

#### Site Settings
- Update company information
- Modify contact details
- Change hero section content
- Update statistics

### API Usage

The system provides a RESTful API for frontend integration:

```javascript
// Get site settings
fetch('/api/get_content.php?type=settings')

// Get published blog posts
fetch('/api/get_content.php?type=blog_posts&status=published&limit=5')

// Get active projects by category
fetch('/api/get_content.php?type=projects&category=BioFuture%20%26%20Governance&active_only=1')

// Get team members
fetch('/api/get_content.php?type=team_members&active_only=1')

// Get upcoming events
fetch('/api/get_content.php?type=events&event_type=upcoming&limit=3')
```

## Database Schema

### Tables

- **admin_users**: Admin authentication
- **blog_posts**: Blog content management
- **projects**: Project portfolio
- **team_members**: Team information
- **site_settings**: Configuration settings
- **events**: Event management

### Key Features

- Auto-incrementing IDs
- Timestamp tracking (created_at, updated_at)
- JSON storage for complex data (project features)
- Status management (active/inactive, published/draft)
- SEO-friendly slugs

## Security Features

- Password hashing with PHP's password_hash()
- SQL injection prevention with prepared statements
- Session-based authentication
- File upload validation
- CSRF protection ready

## Customization

### Adding New Content Types

1. Create database table
2. Add admin management page
3. Update API endpoints
4. Modify frontend integration

### Styling

- Edit `admin/assets/admin.css` for admin panel styling
- Responsive design with CSS Grid and Flexbox
- Modern gradient themes and animations

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check credentials in `config/database.php`
   - Ensure MySQL service is running

2. **File Upload Issues**
   - Check directory permissions (755)
   - Verify PHP upload settings

3. **Login Problems**
   - Verify admin user exists in database
   - Check session configuration

### Support

For technical support or questions:
- Email: admin@kaminovalabs.com
- Check database logs for error details
- Verify PHP error logs

## License

© 2025 Kaminova Labs. All Rights Reserved.