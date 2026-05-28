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

SMARTBILL_USERNAME=billing@example.com
SMARTBILL_TOKEN=your-smartbill-token
SMARTBILL_COMPANY_VAT_CODE=RO12345678
SMARTBILL_INVOICE_SERIES=FCT
SMARTBILL_PAYMENT_TYPE=Card online
SMARTBILL_TEST_FLOW_DRAFT=true
SMARTBILL_TAX_NAME=Normala
SMARTBILL_TAX_PERCENTAGE=19
SMARTBILL_TAX_INCLUDED=true

APP_PUBLIC_WIZARD_MAINTENANCE=false

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@example.com
```

`SMARTBILL_TAX_*` should match a tax configuration that already exists in SmartBill Cloud. If your company is not a VAT payer, leave those values empty.

`SMARTBILL_TEST_FLOW_DRAFT=true` keeps SmartBill invoices created by the admin billing-test flow in draft mode. `APP_PUBLIC_WIZARD_MAINTENANCE=true` blocks the public wizard at step one and shows a branded maintenance modal instead of continuing into report generation.

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

Reports, paid-report follow-up work, and SmartBill invoice sync run asynchronously. You **must** run the queue worker:

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

SmartBill request and response activity is logged separately:

```
storage/logs/smartbill.log
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
