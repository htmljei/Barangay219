# E-Barangay Information Management System

A comprehensive web-based system for managing barangay operations, resident information, certificates, blotters, complaints, and announcements for Barangay 195, Tondo, Manila.

## Features

- **User Management**: Role-based access control (Barangay Captain, Secretary, Treasurer, Kagawad, SK Chairman)
- **Resident Information**: Complete resident database with search and filtering
- **Household Management**: Family/household record management
- **Certificate Issuance**: Process and track certificate requests (Barangay Clearance, Certificate of Indigency, etc.)
- **Blotter Management**: Digital record book for incidents and disputes
- **Complaints Module**: File and track resident complaints
- **Announcements**: Post and manage public notices
- **Reports**: Generate statistics and reports for submission to higher offices

## Technology Stack

- **Backend**: PHP 7.4+ / 8.x
- **Database**: MySQL
- **Frontend**: Bootstrap 5.3
- **Architecture**: MVC-inspired with RESTful API endpoints

## Installation

1. **Database Setup**
   - Create a MySQL database named `ebarangay_db` (or update `config/constants.php`)
   - Import the schema: `database/schema.sql`
   - Import initial data: `database/seeds.sql`

2. **Configuration**
   - Update database credentials in `config/constants.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'ebarangay_db');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```
   - Update base URLs in `config/constants.php` if needed

3. **Web Server**
   - Point your web server document root to the `public/` directory
   - Ensure PHP has PDO MySQL extension enabled
   - Apache: Enable mod_rewrite for .htaccess support

4. **Default Login**
   - Username: `admin`
   - Password: `admin123` (change immediately after first login)

## File Structure

```
e-barangay-system/
├── api/                    # REST API endpoints
│   ├── auth.php
│   ├── users.php
│   ├── resident.php
│   ├── households.php
│   ├── certificates.php
│   ├── blotter.php
│   ├── complaints.php
│   ├── announcement.php
│   └── reports.php
├── config/                 # Configuration files
│   ├── database.php       # Database connection class
│   └── constants.php      # System constants
├── database/               # Database files
│   ├── schema.sql         # Database schema
│   └── seeds.sql          # Initial data
├── includes/               # Reusable components
│   ├── header.php
│   ├── sidebar.php
│   ├── footer.php
│   └── auth-check.php     # Authentication helpers
├── public/                 # Frontend pages
│   ├── index.php          # Login page
│   ├── dashboard.php
│   ├── residents.php
│   ├── households.php
│   ├── certificates.php
│   ├── blotter.php
│   ├── complaints.php
│   ├── announcement.php
│   ├── reports.php
│   ├── users.php
│   ├── profile.php
│   └── assets/            # CSS, JS, images
└── .htaccess              # Apache configuration
```

## User Roles & Permissions

- **Barangay Captain**: Full system access (admin)
- **Secretary**: Manage residents, certificates, blotters, complaints, announcements, reports
- **Treasurer**: View reports, manage certificates
- **Kagawad**: View and update assigned cases, manage announcements
- **SK Chairman**: Limited access to announcements and public data

## Security Features

- Password hashing using bcrypt
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Session management
- Role-based access control
- CSRF token support (ready for implementation)

## API Endpoints

All API endpoints follow the pattern: `/api/{module}.php?action={action}`

Example:
- `GET /api/resident.php?action=list` - List all residents
- `POST /api/resident.php?action=create` - Create new resident
- `POST /api/certificates.php?action=approve&id=1` - Approve certificate

## Development Notes

- All user inputs are sanitized using `sanitizeInput()` function
- Database queries use prepared statements
- Error logging is enabled in debug mode
- Bootstrap 5.3 is loaded via CDN

## Support

For issues or questions, contact the development team.

## License

This project is developed for Barangay 195, Tondo, Manila.
