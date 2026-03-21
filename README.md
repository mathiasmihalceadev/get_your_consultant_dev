# Property Report System

A full-stack Laravel application that generates AI-powered property reports (purchase, rental, commercial) and delivers them as PDF via email.

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3+, MySQL
- **Frontend:** Inertia.js v2, React 18, Tailwind CSS 4, shadcn/ui
- **Icons:** @phosphor-icons/react
- **PDF:** spatie/laravel-pdf (headless Chromium via Puppeteer)
- **AI:** OpenAI GPT-4o

## Setup

### 1. Environment

Copy `.env.example` to `.env` and configure:

```env
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

OPENAI_API_KEY=sk-...

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@example.com
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Database

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

This seeds:

- **Admin user:** `admin@example.com` / `password`
- **Settings:** Default AI prompts for all three report types, auto_send enabled

### 4. Build Frontend

```bash
npm run dev    # development with HMR
npm run build  # production build
```

### 5. Queue Worker

Reports are generated asynchronously. You **must** run the queue worker:

```bash
php artisan queue:work
```

## Architecture

### Public Flow

1. Visitor selects report type (purchase / rental / commercial)
2. Submits a property URL → validated by OpenAI
3. Enters email address → job dispatched
4. Status page polls every 5 seconds until PDF is ready
5. If `auto_send` is on, email is sent automatically

### Admin Dashboard

- **Login:** `/login` → `admin@example.com` / `password`
- **Dashboard:** `/admin/dashboard` — view all reports, filter by status/type
- **Report Detail:** `/admin/reports/{id}` — view report data, download PDF, manually send
- **Settings:** `/admin/settings` — edit AI prompts per report type, toggle auto_send

### Duplicate Detection

If a PDF already exists for the same URL + report type, the existing PDF is reused without re-generating.

## Logging

Report generation activity is logged to a dedicated channel:

```
storage/logs/report.log
```

## Key Files

| Area                | Path                                               |
| ------------------- | -------------------------------------------------- |
| Public controller   | `app/Http/Controllers/PublicReportController.php`  |
| Admin controller    | `app/Http/Controllers/AdminController.php`         |
| Settings controller | `app/Http/Controllers/AdminSettingsController.php` |
| OpenAI service      | `app/Services/OpenAIService.php`                   |
| Report job          | `app/Jobs/GenerateReportJob.php`                   |
| PDF template        | `resources/views/reports/template.blade.php`       |
| Email template      | `resources/views/emails/report.blade.php`          |
| Report model        | `app/Models/Report.php`                            |
| Settings model      | `app/Models/Settings.php`                          |
| Routes              | `routes/web.php`                                   |
