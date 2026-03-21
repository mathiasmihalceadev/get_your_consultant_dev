# Laravel Property Report System — Full Stack Plan

## Overview

You are building the backend and frontend for a property report generation system inside an **existing Laravel project** with an empty MySQL database already connected. Do not scaffold a new Laravel project.

The system has two faces:
- **Public flow** — a visitor chooses a report type, submits a property URL, and receives a PDF report via email. A public status page shows live report progress.
- **Admin dashboard** — a single admin user manages all reports, configures settings, and manually sends reports when auto-send is off.

Everything is served by Laravel. The UI stack is **Inertia.js + React + shadcn/ui + Tailwind CSS** (Laravel Breeze Inertia/React stack).

Read this entire document before starting. Execute one step at a time and confirm completion before moving to the next.

---

## Key Design Decisions

- **Three report types** — `purchase` (buying a property), `rental` (renting a property), `commercial` (commercial spaces). Each type uses its own AI prompt and JSON schema.
- **Report type selection is the first screen** — Before entering a URL, the visitor selects the report type from a clean selection screen.
- **Duplicate URL + type logic** — If a PDF already exists for a URL + report type combination, reuse it. A new `report` record is created but no regeneration happens.
- **All attempts are logged** — Even failed URL validations create a `report` record, giving the admin full visibility.
- **Auth lives in `users` table** — Breeze authenticates normally. `settings` stores only the three prompts and `auto_send`.
- **Storage is abstracted** — All file operations go through Laravel's `Storage` facade. Local disk for now, S3-ready by config change only.
- **OpenAI JSON is validated** — The prompt enforces JSON output. No retries — the job runs once. On failure the report is marked `error`.
- **Queues are required** — `GenerateReportJob` runs asynchronously. The `failed_jobs` table must exist. The job has `$tries = 1` and no backoff.
- **Live status page** — Built as an Inertia page that polls every 5 seconds via a React `useEffect` interval hitting a JSON endpoint.
- **Report logging channel** — All report generation activity (start, success, failure, duplicate detection) is logged to a dedicated `report` channel that writes to `storage/logs/report.log`.

---

## UI Design Direction

Inspired by **carvertical.com** — clean, data-driven, trustworthy.

- **Palette:** white background, near-black text (`#0a0a0a`), a single accent color (deep blue `#1a56db` or similar).
- **Typography:** Inter or system sans-serif. Large, confident headings. Tight, readable body text.
- **Layout:** Generous whitespace. Centered max-width containers. No clutter.
- **Components:** shadcn/ui for all interactive elements — buttons, inputs, cards, badges, alerts, tabs, dialogs.
- **Icons:** **Phosphor Icons** (`@phosphor-icons/react`) throughout — replace any Heroicons or Lucide usage.
- **Micro-interactions:** Subtle transitions on status changes and button states. Smooth fade-ins for page sections.
- **Report type selection screen:** Three large, hoverable card tiles — one per report type. Icon + title + short description each. A clear CTA when selected.

---

## Database Schema

### `reports`

| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| report_type | enum | `purchase`, `rental`, `commercial` |
| url | text | Property URL submitted |
| email | string, nullable | Nullable until step 2 of the flow |
| status | enum | `not_accessible`, `pending`, `to_be_sent`, `sent`, `error` |
| report_url | string, nullable | Storage path to the generated PDF |
| page_token | string, unique, nullable | `hash('sha256', $email . $url . $report_type)`, set at step 2 |
| error_message | text, nullable | Populated on `error` or `not_accessible` |
| processed_at | timestamp, nullable | Set when PDF generation completes |
| created_at / updated_at | timestamps | |

### `settings`

| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| purchase_prompt | longtext | OpenAI prompt for buying properties |
| rental_prompt | longtext | OpenAI prompt for rental properties |
| commercial_prompt | longtext | OpenAI prompt for commercial spaces |
| auto_send | boolean | Default `false` |

---

## Step 1 — Environment & Package Setup

1. Add to `.env`:
```
OPENAI_API_KEY=
QUEUE_CONNECTION=database
APP_URL=http://localhost:8000
```

2. Install packages:
```bash
composer require spatie/laravel-pdf
composer require laravel/breeze --dev
php artisan breeze:install react
npm install && npm run build
```

> **Note on Spatie Laravel PDF:** This package uses headless Chromium (via Browsershot) to render PDFs. Ensure `puppeteer` and a compatible Chromium binary are available in the environment. Install the Node dependency:
> ```bash
> npm install puppeteer
> ```
> If running on a server without a display, set the `PUPPETEER_SKIP_CHROMIUM_DOWNLOAD` env and point `CHROMIUM_PATH` to a system Chromium binary, or let Puppeteer download its own. Configure the Browsershot binary path in `config/media-library.php` or via `Browsershot::setBinPath()` if needed. Publish the Spatie PDF config if you need to customise it:
> ```bash
> php artisan vendor:publish --provider="Spatie\LaravelPdf\PdfServiceProvider"
> ```

3. Install shadcn/ui (follow the Laravel + Vite setup):
```bash
npx shadcn@latest init
```
Accept defaults. Configure `components.json` to use `@/components` as the alias.

Install required shadcn components:
```bash
npx shadcn@latest add button card input label badge alert tabs textarea checkbox separator
```

4. Install Phosphor Icons:
```bash
npm install @phosphor-icons/react
```

> **Important:** Do not use Lucide React or Heroicons anywhere. Use exclusively `@phosphor-icons/react` for all icons throughout the project.

5. Set up queues:
```bash
php artisan queue:table
php artisan queue:failed-table
```

6. Create migrations for `reports` and `settings` using the schema above. Run:
```bash
php artisan migrate
```

7. Run `php artisan storage:link` to make stored PDFs publicly accessible.

8. Create and run a seeder that:
   - Seeds one admin user into the `users` table (`name`, `email`, bcrypt `password`).
   - Seeds one row into `settings` with `auto_send = false` and the default prompts defined in Step 2.

9. Disable Breeze's registration route in `routes/web.php` — comment it out or delete it. The admin is seeded only.

10. Configure `config/session.php` — ensure sessions work correctly for the admin login.

---

## Step 2 — Logging Channel

Add a dedicated `report` channel to `config/logging.php`:

```php
'report' => [
    'driver' => 'single',
    'path' => storage_path('logs/report.log'),
    'level' => 'debug',
    'days' => 30,
],
```

Use this channel throughout the report generation process:

```php
Log::channel('report')->info('Report job started', ['report_id' => $report->id, 'type' => $report->report_type]);
Log::channel('report')->info('Duplicate URL detected — reusing existing PDF', [...]);
Log::channel('report')->info('PDF generated successfully', ['report_id' => $report->id]);
Log::channel('report')->error('OpenAI request failed', ['report_id' => $report->id, 'error' => $e->getMessage()]);
Log::channel('report')->error('JSON parsing failed', ['report_id' => $report->id, 'raw_response' => $raw]);
```

---

## Step 3 — OpenAI Service & Prompts

Create `app/Services/OpenAIService.php`. Register it in `AppServiceProvider` as a singleton.

Create two typed exceptions:
- `app/Exceptions/OpenAIRequestException.php`
- `app/Exceptions/OpenAIJsonException.php`

### Method: `validateUrl(string $url): array`

- Call OpenAI (`gpt-4o`) with this system prompt:
  > *"You are a URL accessibility checker. The user will give you a URL. Respond ONLY with a JSON object: `{"accessible": true}` if the URL is publicly reachable and contains a property listing, or `{"accessible": false, "reason": "..."}` if not."*
- User message: the URL string.
- Parse the response. Return `['success' => true]` or `['success' => false, 'message' => $reason]`.
- On HTTP/network exception: throw `OpenAIRequestException`.
- Log with the `report` channel on both success and failure.

### Method: `generateReportData(string $url, string $prompt): array`

- Call OpenAI with `$prompt` as the system message and the URL as the user message.
- Attempt `json_decode` on the response. If it fails, throw `OpenAIJsonException` immediately — **no retries**.
- On success: return the parsed array.
- On HTTP/network exception: throw `OpenAIRequestException`.
- Log with the `report` channel on both success and failure.

Use `OPENAI_API_KEY` from `.env`. All calls use `gpt-4o` with `max_tokens: 4000`.

### Default Prompts (used in seeder)

#### Purchase Prompt (buying a property)

> *"You are an AI consultant and professional real estate market analyst. Your task is to analyze a property listing from a provided URL. Extract all relevant information from the listing and evaluate the property using realistic market logic. The property may be located in any country. Adapt your evaluation to the local market conditions, city characteristics, and economic context. If some information is missing from the listing, estimate reasonable values using typical market patterns for the location.*
>
> *Return strictly valid JSON. Do not include explanations, comments, or additional text.*
>
> *Output structure:*
> *`{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "rooms": 0, "floor": "", "year_built": "", "condition": "", "asking_price": 0, "price_per_sqm": 0 }, "price_evaluation": { "estimated_market_value": 0, "price_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "price_score": 1 }, "area_analysis": { "suitable_for_living": true, "suitable_for_investment": true, "safety": 1, "quietness": 1, "pollution": 1, "traffic": 1, "amenities": { "schools": true, "kindergartens": true, "hospitals": true, "public_transport": true, "shopping_centers": true }, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "value_impact": "", "estimated_cost_if_missing": 0, "parking_score": 1 }, "investment": { "estimated_monthly_rent": 0, "demand_level": "low | medium | high", "gross_yield": 0, "net_yield": 0, "occupancy_rate": 0, "ideal_tenant": "", "investment_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_living": "" }, "risk_analysis": { "construction_risk": "", "legal_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "BUY | NEGOTIATE | AVOID" } }`*"

#### Rental Prompt (renting a property)

> *"You are an AI consultant and professional rental market analyst. Your task is to analyze a rental listing from a provided URL. Extract all relevant information and evaluate the property as a rental opportunity using realistic market logic. Adapt your evaluation to the local rental market, city characteristics, and economic context. Estimate reasonable values when data is missing.*
>
> *Return strictly valid JSON. Do not include explanations, comments, or additional text.*
>
> *Output structure:*
> *`{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "rooms": 0, "floor": "", "year_built": "", "condition": "", "asking_rent_monthly": 0, "price_per_sqm_monthly": 0 }, "rent_evaluation": { "estimated_fair_rent": 0, "rent_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "rent_score": 1 }, "area_analysis": { "suitable_for_living": true, "safety": 1, "quietness": 1, "pollution": 1, "traffic": 1, "amenities": { "schools": true, "kindergartens": true, "hospitals": true, "public_transport": true, "shopping_centers": true }, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "included_in_rent": true, "estimated_extra_cost": 0, "parking_score": 1 }, "livability": { "natural_light": "good | medium | poor", "noise_level": "low | medium | high", "ventilation": "good | medium | poor", "storage": "adequate | limited | none", "ideal_tenant_profile": "", "livability_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_living": "" }, "risk_analysis": { "landlord_risk": "", "legal_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "RENT | NEGOTIATE | AVOID" } }`*"

#### Commercial Prompt (commercial spaces)

> *"You are an AI consultant and professional commercial real estate analyst. Your task is to analyze a commercial property listing from a provided URL. Evaluate the space for business use, investment potential, and market positioning using realistic commercial real estate logic. Adapt to the local market. Estimate reasonable values when data is missing.*
>
> *Return strictly valid JSON. Do not include explanations, comments, or additional text.*
>
> *Output structure:*
> *`{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "floor": "", "year_built": "", "condition": "", "asking_price": 0, "asking_rent_monthly": 0, "price_per_sqm": 0, "zoning": "" }, "price_evaluation": { "estimated_market_value": 0, "estimated_fair_rent": 0, "price_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "price_score": 1 }, "area_analysis": { "foot_traffic": "low | medium | high", "visibility": "low | medium | high", "accessibility": 1, "competition_density": "low | medium | high", "public_transport": true, "parking_nearby": true, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "spaces": 0, "value_impact": "", "parking_score": 1 }, "investment": { "estimated_monthly_rent": 0, "demand_level": "low | medium | high", "gross_yield": 0, "net_yield": 0, "occupancy_rate": 0, "ideal_business_type": [], "investment_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_business": "" }, "risk_analysis": { "construction_risk": "", "legal_risk": "", "zoning_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "BUY | NEGOTIATE | AVOID" } }`*"

---

## Step 4 — Public Flow (Routes, Controllers, Inertia Pages)

All routes are public — no authentication required.

### Route 1: `GET /`

**Controller:** `PublicReportController@index`

Renders the report type selection page.

**Inertia Page:** `resources/js/Pages/Public/Index.jsx`

Design requirements:
- Centered layout, generous vertical padding.
- Headline: *"Get Your Property Report"* — large, bold.
- Subheadline: *"Choose the type of analysis you need."*
- Three large selection cards (shadcn `Card`) in a responsive grid:
  - **Purchase Report** — Icon: `House` (Phosphor) — *"Full analysis for buying a property. Price evaluation, investment potential, risk assessment."*
  - **Rental Report** — Icon: `Key` (Phosphor) — *"Evaluate a rental listing. Fair rent analysis, livability score, hidden costs."*
  - **Commercial Report** — Icon: `Buildings` (Phosphor) — *"Analyse commercial spaces. Foot traffic, zoning, investment yield."*
- Cards are clickable. Selected card gets a highlighted border (accent color).
- A *"Continue"* shadcn `Button` (disabled until a card is selected) navigates to `/submit-url?type={report_type}`.

---

### Route 2: `GET /submit-url`

**Controller:** `PublicReportController@showUrlForm`

Reads `type` query parameter (`purchase | rental | commercial`). Redirects to `/` if missing or invalid.

**Inertia Page:** `resources/js/Pages/Public/SubmitUrl.jsx`

Design requirements:
- Small breadcrumb or back link to `/` at the top.
- Heading reflects the report type: *"Purchase Report"*, *"Rental Analysis"*, or *"Commercial Report"*.
- Single URL input (shadcn `Input`) with a label: *"Property listing URL"*.
- Submit button (shadcn `Button`).
- Inline validation error below input if present.
- Below the form: a *"How it works"* section — 3 steps: Submit URL → Confirm your email → Receive report.
- Clean, editorial layout.

---

### Route 3: `POST /validate-url`

**Controller:** `PublicReportController@validateUrl`

Logic:
1. Validate `url` (required, valid URL) and `report_type` (required, in `purchase|rental|commercial`).
2. Create a `report` record immediately with `status = pending`, `url`, `report_type` set; `email` and `page_token` as null.
3. Log: `"URL submitted for validation"` with report ID, type, and URL.
4. Call `OpenAIService::validateUrl($url)`.
5. If it fails: update the record to `status = not_accessible`, store message in `error_message`. Log the failure. Return Inertia redirect back with error: *"This URL could not be accessed or does not appear to be a property listing."*
6. If it succeeds: store `report_id` in session. Log success. Redirect to `/submit-email`.

---

### Route 4: `GET /submit-email`

**Controller:** `PublicReportController@showEmailForm`

- Retrieve `report_id` from the session. If missing, redirect to `/`.
- Pass the report (url + report_type) to the Inertia page.

**Inertia Page:** `resources/js/Pages/Public/SubmitEmail.jsx`

Design requirements:
- Show the validated URL as a read-only `Badge` or muted text above the input.
- Show the report type label (e.g., *"Purchase Report"*) as a secondary badge.
- Email input with label *"Your email address"*.
- Submit button.
- Helper text: *"You will receive your report within 24 hours."*

---

### Route 5: `POST /submit-email`

**Controller:** `PublicReportController@submitEmail`

Logic:
1. Validate `email` (required, valid email) and `report_id` (required, exists in `reports`).
2. Load the report. If status is not `pending`, redirect to `/` with error.
3. Generate `page_token = hash('sha256', $email . $report->url . $report->report_type)`.
4. Update the report: set `email`, `page_token`.
5. **Duplicate URL + type check:** Query for any other report with the same `url` + `report_type`, a non-null `report_url`, and status `sent` or `to_be_sent`.
   - If found: copy `report_url` to the new record. Log: `"Duplicate detected — reusing existing PDF"`. If `auto_send` is true, dispatch `SendReportMail`, set `status = sent`. Otherwise set `status = to_be_sent`. Skip job dispatch.
   - If not found: set `status = pending`. Dispatch `GenerateReportJob` with `$report->id`. Log: `"GenerateReportJob dispatched"`.
6. Redirect to `/report/{page_token}`.

---

### Route 6: `GET /report/{page_token}`

**Controller:** `PublicReportController@status`

- Find report by `page_token` or abort 404.
- Pass the report data to the Inertia page.

**Inertia Page:** `resources/js/Pages/Public/ReportStatus.jsx`

Design requirements:
- Centered card layout (shadcn `Card`).
- Property URL and report type badge at the top.
- Visual status indicator that changes based on current status:
  - `pending` → `CircleNotch` (Phosphor, spinning) — *"Your report is being generated…"*
  - `to_be_sent` → `Clock` (Phosphor) — *"Your report is ready and will be sent shortly."*
  - `sent` → `CheckCircle` (Phosphor, green) — *"Your report has been sent to your email."*
  - `error` → `XCircle` (Phosphor, red) — *"There was an error generating your report. Our team has been notified."*
  - `not_accessible` → `Warning` (Phosphor, amber) — *"This URL could not be accessed."*
- **Polling:** Use `useEffect` with `setInterval` (5000ms) to call `GET /api/report-status/{page_token}` and update the component state. Clear the interval when status is `sent` or `error`.
- Subtle fade transition on status change.

---

### Route 7: `GET /api/report-status/{page_token}`

**Controller:** `PublicReportController@statusJson`

- Find report by `page_token` or return 404 JSON.
- Return JSON: `{ "status": "...", "report_url": "..." }`.

---

## Step 5 — Queued Job

Create `app/Jobs/GenerateReportJob.php`.

```php
public $tries = 1;
// No backoff — the job runs once only.
```

Steps inside the job:

1. Load report by ID. If not found, log a warning and return silently.
2. Log: `"GenerateReportJob started"` with report ID and type.
3. Load `settings` (single row, `Settings::first()`).
4. Select the correct prompt based on `$report->report_type`:
   ```php
   $prompt = match($report->report_type) {
       'purchase'   => $settings->purchase_prompt,
       'rental'     => $settings->rental_prompt,
       'commercial' => $settings->commercial_prompt,
   };
   ```
5. Call `OpenAIService::generateReportData($report->url, $prompt)`.
   - On `OpenAIJsonException` or `OpenAIRequestException`: log the error with `report` channel, set `status = error`, store message in `error_message`, set `processed_at = now()`, save and return.
6. Generate the PDF using **Spatie Laravel PDF**. Render a Blade view `reports.template` and save it directly to the storage disk:
   ```php
   use Spatie\LaravelPdf\Facades\Pdf;

   $path = storage_path("app/reports/{$report->page_token}.pdf");

   Pdf::view('reports.template', ['data' => $reportData, 'report' => $report])
       ->format('a4')
       ->save($path);

   $report->report_url = Storage::url("reports/{$report->page_token}.pdf");
   ```
   Ensure the `storage/app/reports/` directory exists (create it in the job if necessary: `Storage::makeDirectory('reports')`).
   Wrap the `Pdf::` call in a try/catch for any `\Exception` — on failure log the error with the `report` channel, set `status = error`, and return.
8. Set `processed_at = now()`.
9. Log: `"PDF generated and stored"` with path.
10. Check `$settings->auto_send`:
    - `true` → dispatch `SendReportMail`, set `status = sent`.
    - `false` → set `status = to_be_sent`.
11. Save the report. Log: `"GenerateReportJob completed"`.

---

## Step 6 — PDF Template

Create `resources/views/reports/template.blade.php`.

Requirements:
- **Full CSS support** — Spatie Laravel PDF renders via headless Chromium, so external stylesheets, CSS variables, flexbox, grid, and modern CSS all work. You may use a dedicated stylesheet at `public/css/report.pdf.css` (linked with an absolute `file://` path or a `asset()` URL) or embed a `<style>` block directly in the template.
- **Do not use Tailwind utility classes** in this template — the Tailwind build is not available in the PDF context unless you compile a separate stylesheet. Use plain CSS with clear class names instead.
- Professional, clean layout. White background (`#fff`), dark text (`#111`), accent color (`#1a56db`) for headings and section dividers.
- The template should adapt based on `$report->report_type`:
  - Use `@if($report->report_type === 'purchase')` blocks to conditionally render sections relevant to each type (e.g., purchase verdict vs rental verdict vs commercial zoning).
- Structured sections for all types:
  1. **Header** — Report type label, generation date, property URL.
  2. **Property Summary** — Key details grid.
  3. **Evaluation** — Price/rent evaluation with market positioning.
  4. **Area Analysis** — Scores and amenity checklist.
  5. **Parking** — Parking details.
  6. **Investment / Livability** — Type-specific section.
  7. **Risk Analysis** — Bullet list.
  8. **Final Score & Verdict** — Prominent overall score, recommendation badge, verdict.
  9. **Footer** — Status page URL, generated by line.
- Use `$data['key'] ?? 'N/A'` for every field to handle missing data gracefully.

---

## Step 7 — Mailable

Create `app/Mail/ReportMail.php`. Implement `ShouldQueue`.

- Accepts a `Report` model.
- Retrieves the PDF from storage using the absolute path: `storage_path("app/reports/{$report->page_token}.pdf")`
- Attaches it with filename `property-report.pdf`.
- Subject: `"Your Property Report is Ready"` (include report type in subject: e.g., *"Your Purchase Property Report is Ready"*)
- Body (Blade view `emails.report`):
  - Display report type label (Purchase / Rental / Commercial).
  - *"Your [type] report for [URL] is attached."*
  - *"You can also check your report status at: [status URL]"*

Create `resources/views/emails/report.blade.php`.

---

## Step 8 — Admin Dashboard

All routes are prefixed `/admin` and protected by the `auth` middleware.

### Admin Layout

Create `resources/js/Layouts/AdminLayout.jsx`:
- Fixed sidebar with navigation links: Dashboard, Settings.
- Top bar with admin email and logout button.
- Main content slot (children).
- Tailwind-based, dark sidebar (slate-900), white content area.
- Phosphor icons for nav items: `ChartBar` (Dashboard), `Gear` (Settings).

---

### Route: `GET /admin/dashboard`

**Controller:** `AdminController@dashboard`

- Paginate all reports (20 per page), sorted by `created_at` desc.
- Allow filtering by `status` and `report_type` via query parameters.
- Pass reports, current filter, and summary counts to the Inertia page.

**Inertia Page:** `resources/js/Pages/Admin/Dashboard.jsx`

Design requirements:
- Summary stat cards at the top (shadcn `Card`): total reports, pending, to_be_sent, sent, error.
- Filter tabs (shadcn `Tabs`) for status + a separate select for report type.
- Table with columns: ID, Type (badge), URL (truncated, 40 chars, title tooltip), Email, Status (colored shadcn `Badge`), Created At, Processed At, Actions.
- Status badge colors:
  - `not_accessible` → yellow
  - `pending` → blue
  - `to_be_sent` → orange
  - `sent` → green
  - `error` → red
- Report type badge colors:
  - `purchase` → violet
  - `rental` → teal
  - `commercial` → amber
- Actions: **"Send"** button (only `to_be_sent`), **"View PDF"** link (only if `report_url` set), **"Details"** link.
- Pagination at the bottom.

---

### Route: `GET /admin/reports/{id}`

**Controller:** `AdminController@show`

**Inertia Page:** `resources/js/Pages/Admin/ReportDetail.jsx`

- Full report details in a clean card layout.
- Status badge and report type badge.
- If `error_message` is set: shadcn `Alert` (destructive variant).
- If `report_url` is set: download/view PDF button with `FilePdf` Phosphor icon.
- If `status = to_be_sent`: prominent *"Send Report"* button (POST to `/admin/reports/{id}/send`).

---

### Route: `POST /admin/reports/{id}/send`

**Controller:** `AdminController@send`

Logic:
1. Load report. If status is not `to_be_sent`, redirect back with error.
2. Dispatch `SendReportMail`.
3. Update `status = sent`. Save.
4. Log: `"Report manually sent by admin"` with report ID.
5. Redirect back with success: *"Report has been sent to {email}."*

---

### Route: `GET /admin/settings`

**Controller:** `AdminSettingsController@show`

**Inertia Page:** `resources/js/Pages/Admin/Settings.jsx`

- Form with shadcn `Tabs` — one tab per prompt type: *"Purchase"*, *"Rental"*, *"Commercial"*.
- Each tab has a large shadcn `Textarea` for the respective prompt with a helper text explaining the expected JSON structure.
- A shadcn `Checkbox` for `auto_send` with label *"Automatically send reports after generation"*.
- Save button.
- Success flash message on save.

---

### Route: `POST /admin/settings`

**Controller:** `AdminSettingsController@update`

- Validate: `purchase_prompt`, `rental_prompt`, `commercial_prompt` (all required strings), `auto_send` boolean.
- Update the single `settings` row.
- Redirect back with success flash.

---

## Step 9 — Flash Messages & Error Handling

1. Create a global flash/toast system using a React context or a shared `usePage().props.flash` helper. Display success/error flash messages using shadcn `Alert` in the shared layouts.

2. Add a global exception handler entry for `OpenAIRequestException` and `OpenAIJsonException` in `bootstrap/app.php` — log them with the `report` channel and full context.

3. Add rate limiting to the two public POST routes in `routes/web.php`:
```php
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/validate-url', ...);
    Route::post('/submit-email', ...);
});
```

---

## Step 10 — Final Checks & Documentation

1. Run `php artisan route:list` — verify all public, API, and admin routes are registered.
2. Run `php artisan migrate:fresh --seed` — confirm migrations and seeders work cleanly.
3. Run `npm run build` — confirm Vite + React assets compile without errors.
4. Verify `php artisan storage:link` has been run.
5. Confirm the `failed_jobs` table exists.
6. Verify that Puppeteer/Chromium is correctly installed and that Spatie Laravel PDF can generate a test PDF. You can do a quick smoke test from Tinker:
   ```php
   Spatie\LaravelPdf\Facades\Pdf::html('<h1>Test</h1>')->save(storage_path('app/test.pdf'));
   ```
   Confirm `storage/app/test.pdf` is created and valid before relying on the job.
7. Verify `storage/logs/report.log` is created and written to when a report job runs.
7. Add a `README.md` to the project root documenting:
   - How to set up `.env` variables.
   - How to run the queue worker: `php artisan queue:work` (no `--tries` flag needed since `$tries = 1`).
   - Seeded admin credentials.
   - Location of the report log: `storage/logs/report.log`.

---

## Full Route Summary

| Method | URI | Auth | Description |
|---|---|---|---|
| GET | `/` | — | Report type selection screen |
| GET | `/submit-url` | — | URL submission form (type via query param) |
| POST | `/validate-url` | — | Validate URL via OpenAI |
| GET | `/submit-email` | — | Email submission form |
| POST | `/submit-email` | — | Store email, dispatch job |
| GET | `/report/{page_token}` | — | Live status page (Inertia + polling) |
| GET | `/api/report-status/{page_token}` | — | JSON status endpoint (polled by frontend) |
| GET | `/admin/dashboard` | auth | Report list |
| GET | `/admin/reports/{id}` | auth | Report detail |
| POST | `/admin/reports/{id}/send` | auth | Manually send report |
| GET | `/admin/settings` | auth | Settings form |
| POST | `/admin/settings` | auth | Save settings |
| GET | `/login` | guest | Admin login (Breeze) |
| POST | `/logout` | auth | Logout |

---

## Agent Execution Checklist

- [ ] Step 1 — Environment & Package Setup (Breeze Inertia/React, shadcn/ui, Phosphor icons)
- [ ] Step 2 — `report` logging channel in `config/logging.php`
- [ ] Step 3 — OpenAI Service (`validateUrl`, `generateReportData`, typed exceptions, 3 prompts in seeder)
- [ ] Step 4 — Public Flow (7 routes, controllers, Inertia pages, polling status page)
- [ ] Step 5 — `GenerateReportJob` (single run, `$tries = 1`, report channel logging, type-based prompt selection)
- [ ] Step 6 — PDF Blade template (plain CSS, Spatie PDF via headless Chromium, adaptive sections per report type)
- [ ] Step 7 — `ReportMail` mailable (queueable, PDF attachment, type-aware subject)
- [ ] Step 8 — Admin dashboard, report detail, manual send, settings (tabbed prompts)
- [ ] Step 9 — Flash messages, exception logging, rate limiting
- [ ] Step 10 — Final checks, queue worker documentation, README
