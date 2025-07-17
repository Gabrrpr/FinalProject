# Automated Electronic Voting System

A secure, PHP/MySQL-based voting system with role-based access, real-time results, and robust security.

## Features
- Secure authentication (hashed passwords, sessions)
- Role-based access (admin, voter)
- One vote per user per election
- Admin panel for election/candidate management
- Real-time vote counting (post-election)
- Accessible, responsive UI

## Project Structure
- `/public` – Entry point, static assets
- `/src` – PHP logic (auth, voting, admin)
- `/templates` – HTML templates
- `/tests` – Unit & integration tests
- `config.php` – Database configuration

## Setup
1. Import the `database.sql` file to set up the schema.
2. Configure `config.php` with your DB credentials.
3. Place project files in your web server root.
4. Open `public/index.php` in your browser.

## Security
- All inputs sanitized
- Sessions secured
- SQL injection/XSS protected

## Testing
- Run `/tests` scripts for unit/integration testing.

---

For detailed guidelines, see the project documentation.
