# IT Helpdesk Ticketing System

A complete internal IT support platform built with **PHP, MySQL, Tailwind CSS and Vanilla JavaScript**. No frameworks.

## Features
- User & Admin authentication
- Ticket creation with file uploads
- Admin dashboard with statistics
- Ticket search, filter, pagination
- Activity logs and status history
- Dark/light mode
- Responsive UI

## Requirements
- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ / MariaDB 10.2+
- Web server (Apache/Nginx)

## Installation
1. Clone the repository into your web root.
2. Create a MySQL database and import `database/schema.sql`.
3. Edit `includes/config.php` with your database credentials.
4. Ensure the `uploads/` folder is writable by the web server.
5. Login with default credentials:
   - Admin: `admin@company.com` / `password123`
   - User:  `john@company.com` / `password123`
6. Enjoy!

## Project Structure
- `css/` – Custom styles
- `js/` – Vanilla JavaScript helpers
- `includes/` – PHP includes (auth, DB, templates)
- `uploads/` – User attachments
- `database/` – SQL schema and sample data

## Security
- Prepared statements throughout
- Password hashing (bcrypt)
- File upload validation
- Session-based authentication