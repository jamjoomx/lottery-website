# 🎰 Lottery Website — Full Stack (PHP Laravel)

A full-stack lottery platform with secure user registration, ticket purchasing, random draw engine, and an admin dashboard.

## Tech Stack
- **Backend:** PHP 8.x, Laravel 10
- **Frontend:** Angular, Bootstrap 5
- **Database:** MySQL
- **API:** JSON REST API

## Features
- User registration & login (hashed passwords)
- Buy lottery tickets with unique number generation
- Automated draw system (scheduled via Laravel Task Scheduler)
- Admin dashboard: manage draws, view winners, export reports
- Winner notification system

## Setup Instructions

```bash
# 1. Clone the repo
git clone https://github.com/YOUR_USERNAME/lottery-website.git
cd lottery-website

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Set your DB credentials in .env
# DB_DATABASE=lottery_db
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 5. Generate app key
php artisan key:generate

# 6. Run migrations and seed
php artisan migrate --seed

# 7. Serve the app
php artisan serve
```

## Database Schema
- `users` — id, name, email, password, balance
- `tickets` — id, user_id, numbers, draw_id, status
- `draws` — id, winning_numbers, drawn_at, status
- `winners` — id, ticket_id, user_id, prize_amount

## API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/auth/register | Register new user |
| POST | /api/auth/login | Login |
| GET | /api/tickets | Get user tickets |
| POST | /api/tickets/buy | Purchase a ticket |
| GET | /api/draws/latest | Get latest draw result |
| GET | /api/admin/draws | Admin: all draws |
