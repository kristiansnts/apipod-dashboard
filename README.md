<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
<img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="PHP 8.2+">
<img src="https://img.shields.io/badge/Laravel-12.x-red" alt="Laravel 12">
</p>

# API Pod Dashboard

A comprehensive Laravel-based dashboard for managing LLM API usage, subscriptions, payments, and rate limits. Built for API pod services to provide users with subscription plans, usage analytics, and payment processing.

## ✨ Features

- **Multi‑provider LLM Support** – Integrate with various LLM providers (OpenAI, Google AI Studio, etc.) with per‑model rate limits (RPM, TPM, RPD)
- **Subscription & Billing** – Create and manage subscription plans with automatic payment processing via Xendit
- **Usage Analytics** – Detailed token usage tracking with quota resets and real‑time logs
- **OAuth Authentication** – GitHub OAuth integration for easy sign‑up and login
- **Admin Panel** – Built with Filament for managing users, plans, providers, and payments
- **Shop Interface** – User‑friendly shop view for purchasing subscription plans
- **Exchange Rate Integration** – Automatic currency conversion for international pricing
- **Webhook Support** – Handle payment notifications from Xendit

## 🛠 Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Blade templates, Tailwind CSS v4, Instrument Sans font
- **Database**: PostgreSQL (default), supports Laravel's database drivers
- **Admin**: Filament PHP
- **Payments**: Xendit PHP SDK
- **Authentication**: Laravel Socialite (GitHub OAuth)
- **Exchange Rates**: Laravel Exchange Rates package
- **Queue & Cache**: Redis (optional), database queue driver

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/apipod-dashboard.git
   cd apipod-dashboard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Set up environment variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` with your database, GitHub OAuth, Xendit, and exchange‑rate API keys.

5. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

## ⚙️ Environment Configuration

Key environment variables in `.env`:

```env
DB_CONNECTION=pgsql
DB_DATABASE=apipod

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

XENDIT_SECRET_KEY=
XENDIT_PUBLIC_KEY=

EXCHANGE_RATES_API_KEY=
USD_TO_IDR_FALLBACK_RATE=15800
```

See `.env.example` for all available options.

## 📦 Project Structure

- `app/Models/` – Eloquent models (User, Plan, Payment, Provider, LlmModel, etc.)
- `app/Http/Controllers/` – Web controllers (ShopController, PaymentController, SocialiteController)
- `app/Services/` – Business logic (TokenUsageService, etc.)
- `resources/views/` – Blade templates with Tailwind CSS
- `routes/web.php` – Web routes (shop, auth, webhooks)
- `database/migrations/` – Database schema definitions

## 🧪 Running Tests

```bash
composer test
```

## 🔧 Development

The project includes a convenient `dev` script that runs the development server, queue worker, log tail, and Vite dev server concurrently:

```bash
composer run dev
```

For a full setup (install dependencies, generate key, migrate, build assets):

```bash
composer run setup
```

## 📄 License

The Laravel framework is open‑source software licensed under the [MIT license](https://opensource.org/licenses/MIT).