# CRM Portal

A comprehensive Customer Relationship Management (CRM) system built with PHP and MySQL. This application helps businesses manage contacts, companies, deals, and activities in one centralized platform.

## Features

- **User Authentication**: Secure login/logout system with password hashing
- **Dashboard**: Overview with statistics and recent activities
- **Contact Management**: Full CRUD operations for managing contacts
  - Create, read, update, and delete contacts
  - Associate contacts with companies
  - Track contact status (Lead, Prospect, Customer, Inactive)
  - Assign contacts to team members
- **Company Management**: Manage company information and relationships
- **Deal Tracking**: Monitor sales opportunities and pipeline
  - Track deal stages (Qualification, Proposal, Negotiation, Closed Won/Lost)
  - Monitor deal values and probabilities
  - Expected close dates
- **Activity Management**: Log and schedule activities
  - Calls, meetings, emails, tasks, and notes
  - Activity status tracking (Scheduled, Completed, Cancelled)
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, JavaScript
- **Architecture**: MVC pattern with Object-Oriented PHP

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server with mod_rewrite enabled
- PDO PHP Extension

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/crm-portal.git
cd crm-portal
```

### 2. Database Setup

1. Create a new MySQL database:

```sql
CREATE DATABASE crm_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:

```bash
mysql -u your_username -p crm_portal < database/schema.sql
```

Or manually import using phpMyAdmin or your preferred MySQL client.

### 3. Configure Database Connection

Edit `config/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_portal');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Web Server Configuration

#### Apache

Make sure your DocumentRoot points to the CRM portal directory, or create a virtual host:

```apache
<VirtualHost *:80>
    ServerName crm.local
    DocumentRoot /path/to/crm-portal

    <Directory /path/to/crm-portal>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name crm.local;
    root /path/to/crm-portal;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Set Permissions

Ensure the web server has read access to all files:

```bash
chmod -R 755 /path/to/crm-portal
```

### 6. Access the Application

Open your web browser and navigate to:
- http://localhost/crm-portal (if installed in a subdirectory)
- http://crm.local (if using virtual host)

## Default Login Credentials

```
Username: admin
Password: admin123
```

**Important**: Change the default password immediately after first login!

## Project Structure

```
crm-portal/
├── assets/
│   └── css/
│       └── style.css          # Main stylesheet
├── config/
│   └── config.php             # Application configuration
├── database/
│   └── schema.sql             # Database schema and sample data
├── includes/
│   ├── database.php           # Database connection class
│   ├── functions.php          # Helper functions
│   ├── header.php             # Common header template
│   └── footer.php             # Common footer template
├── models/
│   ├── User.php               # User model
│   ├── Contact.php            # Contact model
│   ├── Company.php            # Company model
│   ├── Deal.php               # Deal model
│   └── Activity.php           # Activity model
├── index.php                  # Entry point
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── dashboard.php              # Dashboard
├── contacts.php               # Contacts list
├── contact_form.php           # Add/Edit contact
├── contact_view.php           # View contact details
├── contact_delete.php         # Delete contact handler
├── companies.php              # Companies list
├── deals.php                  # Deals list
├── activities.php             # Activities list
└── README.md                  # This file
```

## Database Schema

The application uses the following main tables:

- **users**: User accounts and authentication
- **contacts**: Customer and lead information
- **companies**: Company/organization data
- **deals**: Sales opportunities and pipeline
- **activities**: Scheduled and completed activities

See `database/schema.sql` for the complete schema definition.

## Usage Guide

### Managing Contacts

1. **Add a New Contact**:
   - Navigate to Contacts → Add New Contact
   - Fill in the contact information
   - Assign to a company (optional)
   - Set contact status and source
   - Click "Create Contact"

2. **View Contact Details**:
   - Click on any contact name in the contacts list
   - View complete contact information
   - See associated activities

3. **Edit Contact**:
   - From contact details page, click "Edit"
   - Update information as needed
   - Click "Update Contact"

4. **Delete Contact**:
   - From contact details or list page, click "Delete"
   - Confirm deletion

### Using Filters

All list pages support filtering:
- **Contacts**: Filter by status and search by name/email
- **Companies**: Search by name/industry/email
- **Deals**: Filter by stage
- **Activities**: Filter by type and status

## Security Features

- Password hashing using PHP's `password_hash()` function
- SQL injection prevention using PDO prepared statements
- XSS protection through input sanitization
- Session-based authentication
- CSRF token support (in helper functions)

## Customization

### Changing Application Name

Edit `config/config.php`:

```php
define('APP_NAME', 'Your CRM Name');
```

### Modifying Date/Time Formats

Edit the date format constants in `config/config.php`:

```php
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_DATETIME_FORMAT', 'M d, Y h:i A');
```

### Adding Custom Fields

1. Add column to database table
2. Update the corresponding model class
3. Add form fields to the form page
4. Update view page to display the new field

## Development

### Adding New Features

1. Create/update model classes in `models/` directory
2. Create view pages in the root directory
3. Update navigation in `includes/header.php`
4. Add appropriate styling in `assets/css/style.css`

### Code Standards

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Sanitize all user inputs
- Use prepared statements for database queries

## Troubleshooting

### Database Connection Errors

- Verify database credentials in `config/config.php`
- Ensure MySQL service is running
- Check database user has proper permissions

### Login Issues

- Clear browser cache and cookies
- Verify database contains the admin user
- Check PHP session configuration

### Page Not Found Errors

- Ensure mod_rewrite is enabled (Apache)
- Check web server configuration
- Verify file permissions

## Future Enhancements

Potential features for future development:

- [ ] Email integration
- [ ] Task management system
- [ ] Document attachments
- [ ] Reporting and analytics
- [ ] Export data to CSV/Excel
- [ ] API endpoints for integrations
- [ ] User roles and permissions
- [ ] Email templates
- [ ] Calendar view for activities
- [ ] Mobile app

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For issues, questions, or contributions, please open an issue on GitHub.

## Credits

Developed with PHP, MySQL, and modern web technologies.

---

**Note**: This is a demonstration CRM application. For production use, consider implementing additional security measures, error handling, logging, and testing.
