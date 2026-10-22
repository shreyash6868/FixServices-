FIXSERVICES - PHP + MYSQL COLLEGE MINI PROJECT
================================================

REQUIREMENTS
- XAMPP (Apache + MySQL/MariaDB)
- PHP 8+
- Browser
- Internet connection for Bootstrap CDN (or download Bootstrap locally)

INSTALLATION
1. Extract the FixServices folder into:
   C:\xampp\htdocs\FixServices
2. Open XAMPP Control Panel and start Apache and MySQL.
3. Open http://localhost/phpmyadmin
4. Import database.sql using the Import tab.
   This creates the fixservices database, tables, and starter service categories.
5. If your MySQL username/password differs from XAMPP defaults, edit config.php.
6. Open http://localhost/FixServices/

CREATE AN ADMIN ACCOUNT
1. Register a normal customer account using register.php.
2. In phpMyAdmin, open the fixservices database and run this query,
   replacing the email with the account you registered:
   UPDATE users SET role='admin' WHERE email='your-email@example.com';
3. Log out and log in again. Open the Admin dashboard from the navigation.

TEST THE BOOKING FLOW
1. Register a provider account.
2. Log in as admin and approve the provider.
3. Log in as provider and add a service.
4. Register/login as a customer and book that service.
5. Log in as provider to accept, start, and complete the booking.
6. Log in as customer to review a completed booking.

DATABASE TABLES
users, categories, services, bookings, reviews, complaints

SECURITY NOTES
- Passwords are stored with password_hash() and checked with password_verify().
- SQL operations use PDO prepared statements.
- Forms use a CSRF token.
- User-facing output is escaped with htmlspecialchars().
- This is an educational starter project. Before public deployment, add stronger
  validation, rate limiting, email verification, audit logging, backups, and
  production server configuration.
