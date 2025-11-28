# GUVI Internship - Signup/Login/Profile Project

## What this project includes
- Register (MySQL stores credentials)
- Login (creates token stored in Redis)
- Profile update (stored in MongoDB collection `profiles`)
- Frontend: Bootstrap + jQuery + custom JS
- Backend: PHP (mysqli prepared statements for MySQL)

## Requirements (server side)
1. PHP 8+ with:
   - mysqli extension
   - phpredis extension (optional but recommended) OR a Redis client
   - mongodb extension (for MongoDB driver)
2. Composer to install mongodb/mongodb PHP library (used by mongo.php)
3. MySQL server
4. MongoDB server
5. Redis server
6. Web server (Apache/Nginx) with document root pointing to this project

## Setup steps (quick)
1. Place project in your webserver document root (or use php -S for quick testing).
2. Import SQL:
   ```
   mysql -u root -p < sql/users.sql
   ```
3. Install PHP libraries with composer (in backend/):
   ```
   cd backend
   composer require mongodb/mongodb
   ```
4. Ensure phpredis or another Redis client is available.
5. Update database credentials in `backend/db.php` if needed.
6. Start services: MySQL, MongoDB, Redis, PHP/Apache.

## Notes
- Login session token is stored in browser `localStorage` and Redis (key: `session:{token}`).
- All backend endpoints accept and return JSON.
- AJAX calls use jQuery only (no HTML form submission).
- PHP uses prepared statements for MySQL to avoid SQL injection.

## Troubleshooting
- If MongoDB extension or composer library is missing, disable mongo parts or install dependencies.
- If Redis is not available, session storage will not persist — login will still return a token but update_profile will fail.

