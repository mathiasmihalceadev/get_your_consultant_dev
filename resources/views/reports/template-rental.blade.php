<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport AnalizÄƒ Proprietate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        @page { size: A4; margin: 10mm 0 20mm 0; }
        @page :first { margin-top: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #34306a;
            --primary-light: #45418a;
            --secondary: #4e59b7;
            --tertiary: #7380d9;
            --neutral: #64748b;
            --green: #00A556;
            --green-light: #ecfdf5;
            --green-border: #a7f3d0;
            --amber: #d97706;
            --amber-light: #fffbeb;
            --amber-border: #fde68a;
            --red: #e05252;
            --red-light: #fef2f2;
            --red-border: #fecaca;
            --bg: #ffffff;
            --card: #ffffff;
            --border: #dfe3f3;
            --text: #231f57;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --gradient-accent: linear-gradient(90deg, var(--secondary), var(--tertiary));
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background: var(--bg);
            font-size: 12px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* â”€â”€ PAGE LAYOUT â”€â”€ */
        .page {
            width: 210mm;
            padding: 0;
            margin: 0 auto;
            background: var(--bg);
            page-break-after: always;
            position: relative;
        }
        .page:last-child { page-break-after: avoid; }
        .page-inner-first {
            padding: 6px 16px 4px;
        }

        /* â”€â”€ ARTICLE FLOW (detail pages) â”€â”€ */
        .article-flow {
            width: 210mm;
            margin: 0 auto;
            background: var(--bg);
            padding: 0 16px 0;
        }
        .article-flow .chapter-content:last-child {
            padding-bottom: 20px;
        }

        /* â”€â”€ HEADER BAR â”€â”€ */
        .header-bar {
            background: linear-gradient(135deg, var(--primary) 0%, #28255a 100%);
            padding: 10px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo { height: 44px; }
        .header-left { display: flex; align-items: center; }
        .header-badge-gen {
            font-size: 9px; color: rgba(255,255,255,0.4);
            font-weight: 500;
        }

        /* â”€â”€ HERO â”€â”€ */
        .hero-card {
            background: var(--card);
            border-radius: 12px;
            padding: 10px 20px;
            margin-bottom: 6px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .hero-card::before {
            display: none;
        }
        .hero-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .hero-name { font-size: 18px; font-weight: 800; color: var(--primary); margin-bottom: 4px; }
        .hero-address { font-size: 11px; color: var(--text-muted); }
        .hero-price-box {
            text-align: right;
            background: linear-gradient(135deg, var(--secondary), var(--tertiary));
            color: #fff; padding: 10px 16px; border-radius: 8px;
            min-width: 130px;
        }
        .hero-price { font-size: 24px; font-weight: 900; line-height: 1; }
        .hero-price-label { font-size: 9px; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
        .hero-tagline {
            font-size: 11px; font-style: italic; color: var(--tertiary);
            padding: 4px 12px; background: rgba(115,128,217,0.08);
            border-radius: 4px; margin-bottom: 8px; display: inline-block;
        }
        .hero-grid { display: flex; flex-wrap: wrap; gap: 0; }
        .hero-stat { width: 16.666%; padding: 6px 8px; border-right: 1px solid var(--border); }
        .hero-stat:last-child { border-right: none; }
        .hero-stat-label {
            font-size: 8px; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600;
        }
        .hero-stat-value { font-size: 13px; font-weight: 700; color: var(--primary); }

        /* â”€â”€ KPI ROW â”€â”€ */
        .kpi-row {
            display: flex; gap: 6px; margin-bottom: 6px;
        }
        .kpi-card {
            flex: 1; background: var(--card); border-radius: 10px;
            padding: 8px 8px; text-align: center;
            border: 1px solid var(--border);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column; align-items: center;
        }
        .kpi-card::before {
            display: none;
        }
        .kpi-circle {
            width: 52px; height: 52px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 6px;
        }
        .kpi-circle.grade-green { background: var(--green-light); border: 2px solid var(--green-border); }
        .kpi-circle.grade-amber { background: var(--amber-light); border: 2px solid var(--amber-border); }
        .kpi-circle.grade-red { background: var(--red-light); border: 2px solid var(--red-border); }
        .kpi-score { font-size: 18px; font-weight: 900; line-height: 1; }
        .kpi-max { font-size: 10px; font-weight: 500; color: var(--text-light); }
        .kpi-label {
            font-size: 8.5px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.3px; margin-top: 2px;
        }
        .kpi-trend { font-size: 9px; margin-top: 2px; }
        .trend-up { color: var(--green); }
        .trend-stable { color: var(--neutral); }
        .trend-down { color: var(--red); }

        /* â”€â”€ CHARTS â”€â”€ */
        .charts-grid {
            display: flex; gap: 6px; margin-bottom: 4px;
        }
        .chart-box {
            flex: 1; min-width: 0; background: var(--card); border-radius: 10px;
            padding: 10px; border: 1px solid var(--border);
            position: relative; overflow: hidden;
        }
        .chart-box::before {
            display: none;
        }
        .chart-title {
            font-size: 10px; font-weight: 700; color: var(--primary);
            text-transform: uppercase; letter-spacing: 0.4px;
            margin-bottom: 6px; text-align: center;
        }
        .chart-canvas-wrap { position: relative; }

        /* â”€â”€ POLLUTION WIDGET â”€â”€ */
        .pollution-widget { display: flex; flex-direction: column; }
        .env-aqi { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .env-aqi-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .env-aqi-val { font-size: 14px; font-weight: 800; }
        .env-aqi-meta { display: flex; flex-direction: column; }
        .env-aqi-label { font-size: 10px; font-weight: 700; color: var(--primary); }
        .env-metrics { display: flex; flex-direction: column; gap: 3px; }
        .env-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 3px 0; border-bottom: 1px solid var(--border); font-size: 9px;
        }
        .env-row:last-child { border-bottom: none; }
        .env-key { font-weight: 600; color: var(--neutral); }
        .env-val { font-weight: 700; color: var(--primary); }

        /* â”€â”€ BADGES â”€â”€ */
        .badges-row { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 4px; }
        .badge-item {
            display: flex; align-items: center; gap: 5px;
            padding: 5px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 600;
        }
        .badge-positive { background: var(--green-light); color: #065f46; border: 1px solid var(--green-border); }
        .badge-negative { background: var(--red-light); color: #991b1b; border: 1px solid var(--red-border); }
        .badge-icon { font-size: 13px; }

        /* â”€â”€ VERDICT (Page 1) â”€â”€ */
        .verdict-card {
            background: linear-gradient(135deg, var(--primary) 0%, #22204b 100%);
            border-radius: 12px; padding: 10px 18px; color: #fff;
            position: relative; overflow: hidden;
        }
        .verdict-card::before {
            content: ''; position: absolute; top: -40px; right: -20px;
            width: 120px; height: 120px;
            background: radial-gradient(circle, rgba(115,128,217,0.18) 0%, transparent 70%);
        }
        .verdict-card::after {
            content: ''; position: absolute; bottom: -30px; left: -20px;
            width: 100px; height: 100px;
            background: radial-gradient(circle, rgba(78,89,183,0.18) 0%, transparent 70%);
        }
        .verdict-top { display: flex; align-items: center; gap: 16px; margin-bottom: 6px; position: relative; z-index: 1; }
        .verdict-score-circle {
            width: 56px; height: 56px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 900; color: #fff;
            border: 2px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .verdict-score-circle small { font-size: 11px; font-weight: 600; opacity: 0.7; }
        .verdict-rec-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.5; }
        .verdict-rec { font-size: 22px; font-weight: 900; letter-spacing: 1.5px; }
        .verdict-rec.buy { color: #4ade80; }
        .verdict-rec.negotiate { color: var(--secondary); }
        .verdict-rec.avoid { color: #f87171; }
        .verdict-oneliner { font-size: 11px; opacity: 0.85; margin-bottom: 4px; line-height: 1.5; position: relative; z-index: 1; }
        .verdict-lists { display: flex; gap: 18px; position: relative; z-index: 1; }
        .verdict-list { flex: 1; }
        .verdict-list-title {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            opacity: 0.5; margin-bottom: 5px;
        }
        .verdict-list ul { list-style: none; padding: 0; }
        .verdict-list li {
            font-size: 10px; padding: 2px 0; display: flex; align-items: flex-start; gap: 4px; line-height: 1.35;
        }
        .verdict-list li .vl-icon { flex-shrink: 0; font-size: 11px; }
        .verdict-list.positive .vl-icon { color: #4ade80; }
        .verdict-list.negative .vl-icon { color: #f87171; }

        /* â”€â”€ CHAPTER HEADINGS (article flow) â”€â”€ */
        .chapter-heading {
            padding: 20px 0 6px;
            display: flex; align-items: center; gap: 10px;
            margin: 0;
            border-bottom: 2px solid var(--primary);
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .chapter-heading:first-child { margin-top: 12px; }
        .ch-icon {
            width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;
        }
        .ch-icon svg { width: 18px; height: 18px; fill: none; stroke: var(--primary); stroke-width: 2; }
        .ch-title { font-size: 16px; font-weight: 800; color: var(--primary); letter-spacing: 0.3px; }
        .ch-number { margin-left: auto; font-size: 10px; color: var(--text-muted); font-weight: 600; }

        .chapter-content {
            padding: 14px 0 6px;
        }

        .summary-box {
            background: linear-gradient(135deg, #f0f4ff 0%, #e8ecff 100%);
            border-left: 3px solid var(--tertiary);
            border-radius: 0 8px 8px 0;
            padding: 12px 16px; font-size: 11px; line-height: 1.55;
            color: var(--text); margin-bottom: 16px;
        }

        .section-block {
            margin-bottom: 14px;
        }

        .section-heading {
            font-size: 13px; font-weight: 700; color: var(--primary);
            margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
        }
        .section-heading::before {
            content: ''; width: 3px; height: 14px;
            background: var(--tertiary); border-radius: 2px; flex-shrink: 0;
        }
        .section-body { font-size: 11px; line-height: 1.55; color: #334155; margin-bottom: 8px; }

        /* Data grid */
        .data-grid {
            display: flex; flex-wrap: wrap;
            border: 1px solid var(--border); border-radius: 8px;
            overflow: hidden; margin-bottom: 12px; background: var(--card);
        }
        .data-cell {
            width: 50%; padding: 7px 14px;
            border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .data-cell:nth-child(odd) { border-right: 1px solid var(--border); }
        .data-cell:nth-last-child(-n+2) { border-bottom: none; }
        .data-cell.full-width { width: 100%; border-right: none; }
        .data-cell.highlight-yellow { background: #FEF9C3; }
        .data-label { font-size: 10px; color: var(--text-muted); font-weight: 500; }
        .data-value { font-size: 11.5px; font-weight: 700; color: var(--primary); }
        .data-value .unit { font-weight: 500; font-size: 10px; color: var(--text-muted); }

        /* Item lists */
        .item-list { padding: 0; margin: 0 0 10px; list-style: none; }
        .item-list li {
            padding: 5px 10px 5px 12px; border-left: 3px solid var(--tertiary);
            margin-bottom: 3px; font-size: 11px; line-height: 1.45;
            background: #f8f9fb; border-radius: 0 4px 4px 0; color: #334155;
        }
        .item-list.green-border li { border-left-color: var(--green); }
        .item-list.red-border li { border-left-color: var(--red); }
        .item-list.orange-border li { border-left-color: var(--secondary); }

        /* Negotiation */
        .negotiation-box {
            background: linear-gradient(135deg, #fef9c3 0%, #fef3c7 100%);
            border: 1px solid #fde68a; border-radius: 8px;
            padding: 12px 14px; margin-bottom: 12px;
        }
        .negotiation-target { font-size: 11px; font-weight: 700; color: var(--primary); margin-bottom: 6px; }
        .negotiation-target span { font-size: 17px; color: var(--green); }
        .negotiation-list { list-style: none; padding: 0; }
        .negotiation-list li {
            padding: 2px 0; font-size: 10.5px; color: #713f12;
            display: flex; gap: 5px;
        }
        .negotiation-list li::before { content: '-'; font-size: 10px; flex-shrink: 0; }

        /* Cost items table */
        .cost-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10.5px; border-radius: 8px; overflow: hidden; }
        .cost-table th {
            background: var(--primary); color: #fff;
            padding: 6px 12px; text-align: left;
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700;
        }
        .cost-table td { padding: 6px 12px; border-bottom: 1px solid var(--border); }
        .cost-table tr:nth-child(even) td { background: #f8f9fb; }
        .cost-table .included { color: var(--green); font-weight: 600; }
        .cost-table .not-included { color: var(--red); font-weight: 600; }
        .cost-table tfoot td {
            font-weight: 700; background: #f0f2f7;
            border-top: 2px solid var(--primary);
        }

        /* Risk table */
        .risk-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10.5px; }
        .risk-table th {
            background: var(--primary); color: #fff;
            padding: 6px 12px; text-align: left;
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700;
        }
        .risk-table td { padding: 6px 12px; border-bottom: 1px solid var(--border); vertical-align: top; }
        .risk-table tr:nth-child(even) td { background: #f8f9fb; }
        .severity-badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
        }
        .severity-mic { background: var(--green-light); color: #065f46; }
        .severity-mediu { background: var(--amber-light); color: #92400e; }
        .severity-mare { background: var(--red-light); color: #991b1b; }

        /* Checklist */
        .checklist { list-style: none; padding: 0; margin-bottom: 12px; }
        .checklist li {
            display: flex; align-items: flex-start; gap: 7px;
            padding: 6px 12px; margin-bottom: 3px; border-radius: 6px;
            font-size: 11px; background: #f8f9fb;
        }
        .checklist-checkbox {
            width: 13px; height: 13px; border: 2px solid var(--border);
            border-radius: 2px; flex-shrink: 0; margin-top: 2px;
        }
        .priority-badge {
            display: inline-block; padding: 2px 7px; border-radius: 8px;
            font-size: 8px; font-weight: 700; text-transform: uppercase; flex-shrink: 0;
        }
        .priority-urgent { background: var(--red-light); color: #991b1b; }
        .priority-important { background: var(--yellow-light); color: #854d0e; }
        .priority-recomandat { background: #dbeafe; color: #1e40af; }

        /* Clauses */
        .clauses-list { list-style: none; padding: 0; margin-bottom: 12px; }
        .clauses-list li {
            padding: 3px 0 3px 18px; font-size: 10.5px; color: #334155; position: relative;
        }
        .clauses-list li::before {
            content: 'Â§'; position: absolute; left: 3px;
            color: var(--tertiary); font-weight: 700;
        }

        /* Amenities */
        .amenity-grid { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 12px; }
        .amenity-chip {
            display: flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 500;
            background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
        }
        .amenity-chip .amenity-dist {
            font-size: 9px; color: #15803d; font-weight: 700;
        }

        /* Transport lines */
        .transport-lines { display: flex; flex-wrap: wrap; gap: 4px; margin: 4px 0 12px; }
        .transport-line {
            background: #dbeafe; color: #1e40af;
            padding: 3px 9px; border-radius: 12px;
            font-size: 10px; font-weight: 600;
        }

        /* Installations table */
        .install-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10.5px; }
        .install-table th {
            background: var(--primary); color: #fff;
            padding: 6px 12px; text-align: left;
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700;
        }
        .install-table td { padding: 6px 12px; border-bottom: 1px solid var(--border); }
        .install-table tr:nth-child(even) td { background: #f8f9fb; }
        .condition-badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 9px; font-weight: 700;
        }
        .condition-bun { background: var(--green-light); color: #166534; }
        .condition-medie { background: var(--yellow-light); color: #854d0e; }
        .condition-vechi { background: var(--red-light); color: #991b1b; }

        /* Tenant profiles */
        .profile-card {
            display: flex; align-items: center; padding: 10px 14px;
            margin-bottom: 6px; background: var(--card); border-radius: 8px;
            border-left: 3px solid var(--tertiary); gap: 10px;
            border: 1px solid var(--border); border-left: 3px solid var(--tertiary);
        }
        .profile-score { font-size: 20px; font-weight: 900; color: var(--primary); min-width: 34px; text-align: center; }
        .profile-info { flex: 1; }
        .profile-name { font-size: 11px; font-weight: 700; color: var(--primary); }
        .profile-reason { font-size: 10px; color: var(--text-muted); line-height: 1.35; }

        .not-rec-list { list-style: none; padding: 0; margin-bottom: 10px; }
        .not-rec-list li {
            padding: 5px 12px; font-size: 10px; color: #991b1b;
            background: #fef2f2; border-radius: 4px; margin-bottom: 3px;
            border-left: 3px solid var(--red);
        }
        .not-rec-list li .not-rec-name { font-weight: 700; }
        .not-rec-list li .not-rec-reason { font-size: 9px; color: var(--text-muted); }

        /* Exit conditions */
        .exit-box {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 8px; padding: 12px 14px; margin-bottom: 12px;
        }
        .exit-box .exit-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 10.5px; }
        .exit-box .exit-label { color: var(--text-muted); }
        .exit-box .exit-value { font-weight: 600; color: var(--primary); }

        /* Viewing checklist */
        .viewing-list { list-style: none; padding: 0; margin-bottom: 12px; columns: 2; column-gap: 14px; }
        .viewing-list li {
            font-size: 10px; padding: 2px 0 2px 16px; position: relative;
            color: #334155; break-inside: avoid; margin-bottom: 4px;
        }
        .viewing-list li::before {
            content: '-'; position: absolute; left: 0; top: 2px; font-size: 9px;
        }

        /* Next steps */
        .step-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 7px 14px; margin-bottom: 5px; border-radius: 6px; background: #f8f9fb;
        }
        .step-number {
            width: 22px; height: 22px; background: var(--primary); color: #fff;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 800; flex-shrink: 0;
        }
        .step-content { flex: 1; }
        .step-action { font-size: 11px; font-weight: 600; color: var(--primary); }
        .step-timeline { font-size: 9px; color: var(--text-muted); }

        /* Questions */
        .question-list { list-style: none; padding: 0; counter-reset: q; }
        .question-list li {
            counter-increment: q; padding: 6px 12px 6px 30px;
            font-size: 11px; color: #334155; position: relative;
            margin-bottom: 4px; background: #f8f9fb;
            border-radius: 6px; line-height: 1.45;
        }
        .question-list li::before {
            content: counter(q); position: absolute; left: 8px; top: 6px;
            width: 18px; height: 18px; background: var(--tertiary); color: #fff;
            border-radius: 50%; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* Score breakdown */
        .score-breakdown { display: flex; gap: 10px; margin: 12px 0; }
        .score-dim {
            flex: 1; text-align: center; background: var(--card);
            border-radius: 8px; padding: 12px 6px; border: 1px solid var(--border);
        }
        .score-dim-value { font-size: 22px; font-weight: 900; color: var(--primary); }
        .score-dim-label { font-size: 9px; color: var(--text-muted); font-weight: 600; }
        .score-dim-weight { font-size: 8px; color: var(--text-light); margin-top: 2px; }

        /* Final verdict */
        .final-verdict-card {
            background: linear-gradient(135deg, var(--primary) 0%, #1a1930 100%);
            border-radius: 12px; padding: 22px; color: #fff;
            text-align: center; margin: 16px 0; position: relative; overflow: hidden;
        }
        .final-verdict-card::before {
            content: ''; position: absolute; top: -30px; right: -30px;
            width: 120px; height: 120px;
            background: radial-gradient(circle, rgba(115,128,217,0.18) 0%, transparent 70%);
        }
        .final-score { font-size: 52px; font-weight: 900; color: #fff; line-height: 1; }
        .final-score span { font-size: 22px; opacity: 0.6; }
        .final-verdict-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; margin-top: 2px; }
        .final-rec { font-size: 24px; font-weight: 900; margin-top: 8px; letter-spacing: 2px; }
        .final-rec.negotiate { color: var(--secondary); }
        .final-rec.buy { color: #4ade80; }
        .final-rec.avoid { color: #f87171; }

        /* Positive/negative developments */
        .dev-positive { margin-bottom: 5px; font-size: 10px; font-weight: 700; color: var(--green); }
        .dev-negative { margin-bottom: 5px; font-size: 10px; font-weight: 700; color: var(--red); }

        /* Action items */
        .action-items {
            margin-top: 14px; padding-top: 12px;
            border-top: 2px solid var(--border);
        }
        .action-items-title {
            font-size: 10px; font-weight: 700; color: var(--primary);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 7px; display: flex; align-items: center; gap: 4px;
        }
        .action-items ul { list-style: none; padding: 0; }
        .action-items li {
            display: flex; align-items: flex-start; gap: 5px;
            padding: 2px 0; font-size: 10px; color: #334155;
        }
        .action-items li::before { content: '-'; color: var(--tertiary); font-size: 11px; flex-shrink: 0; }

        .warning-box {
            font-size: 10px; color: #854d0e; background: var(--yellow-light);
            padding: 7px 10px; border-radius: 6px; margin-bottom: 10px;
        }

        .air-quality-box {
            display: flex; gap: 12px; margin-bottom: 10px;
        }
        .air-quality-box .aq-badge {
            background: var(--green-light); color: #166534;
            padding: 10px 16px; border-radius: 8px; text-align: center;
            font-weight: 700; font-size: 14px; min-width: 70px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .air-quality-box .aq-badge small { font-size: 8px; font-weight: 600; text-transform: uppercase; }
        .air-quality-box .aq-text { flex: 1; font-size: 10px; color: #334155; line-height: 1.45; }

        /* â”€â”€ PRINT PAGE-BREAK RULES â”€â”€ */
        .verdict-card,
        .hero-card,
        .kpi-row,
        .charts-grid,
        .badges-row,
        .data-grid,
        .cost-table,
        .risk-table,
        .install-table,
        .checklist li,
        .negotiation-box,
        .profile-card,
        .final-verdict-card,
        .score-breakdown,
        .step-item,
        .exit-box,
        .air-quality-box,
        .summary-box,
        .item-list,
        .clauses-list,
        .question-list,
        .viewing-list {
            page-break-inside: avoid;
        }
        .chapter-heading,
        .section-heading,
        .dev-positive,
        .dev-negative {
            page-break-after: avoid;
        }
        .action-items {
            page-break-inside: avoid;
        }
        .section-body {
            orphans: 3;
            widows: 3;
        }
    </style>
</head>
<body>

@php
    $logoPath = public_path('images/logo-dark.jpg');
    $logoBase64 = file_exists($logoPath)
        ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
        : '';

    $hero = $data['page_one']['hero'] ?? [];
    $kpis = $data['page_one']['kpis'] ?? [];
    $charts = $data['page_one']['charts'] ?? [];
    $badges = $data['page_one']['badges'] ?? [];
    $verdict = $data['page_one']['verdict'] ?? [];
    $pages = $data['pages'] ?? [];
    $meta = $data['report_meta'] ?? [];

    $donutColors = ['#34306a', '#4e59b7', '#7380d9', '#64748b', '#e05252'];

    $barChart = $donutChart = $hBarChart = $radarChart = $pollutionChart = null;
    foreach ($charts as $c) {
        if ($c['type'] === 'bar_comparison') $barChart = $c;
        if ($c['type'] === 'donut') $donutChart = $c;
        if ($c['type'] === 'bar_horizontal') $hBarChart = $c;
        if ($c['type'] === 'bar_hourly') $hBarChart = $c;
        if ($c['type'] === 'radar') $radarChart = $c;
        if ($c['type'] === 'pollution') $pollutionChart = $c;
    }

    // Color mapping: 0-6 red, 6-8 amber, 8-10 green
    function gradeColor($value, $invert = false) {
        if ($invert) $value = 10 - $value;
        if ($value >= 8) return '#00A556';
        if ($value >= 6) return '#d97706';
        return '#e05252';
    }
    function gradeClass($value, $invert = false) {
        if ($invert) $value = 10 - $value;
        if ($value >= 8) return 'grade-green';
        if ($value >= 6) return 'grade-amber';
        return 'grade-red';
    }
@endphp

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{--  PAGE 1: DASHBOARD                                      --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="page">
    <div class="header-bar">
        <div class="header-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" class="header-logo">
            @endif
        </div>
        <div class="header-badge-gen">{{ date('d.m.Y', strtotime($meta['generated_at'] ?? 'now')) }}</div>
    </div>

    <div class="page-inner-first">
        {{-- Hero --}}
        <div class="hero-card">
            <div class="hero-top">
                <div>
                    <div class="hero-name">{{ $hero['property_name'] ?? '' }}</div>
                    <div class="hero-address">{{ $hero['address'] ?? '' }}</div>
                </div>
                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <div class="hero-price-box" style="padding: 8px 12px; min-width: 110px;">
                        <div class="hero-price" style="font-size: 18px;">â‚¬{{ number_format($hero['asking_rent_monthly'] ?? 0) }}</div>
                        <div class="hero-price-label">Chirie cerutÄƒ / lunÄƒ</div>
                    </div>
                    @if(!empty($hero['estimated_rent']))
                    <div class="hero-price-box" style="background: linear-gradient(135deg, var(--green), #047857); min-width: 160px; padding: 12px 18px;">
                        <div class="hero-price" style="font-size: 32px;">â‚¬{{ number_format($hero['estimated_rent']) }}</div>
                        <div class="hero-price-label">Chirie estimatÄƒ / lunÄƒ</div>
                    </div>
                    @endif
                </div>
            </div>
            @if(!empty($hero['tagline']))
                <div class="hero-tagline">{{ $hero['tagline'] }}</div>
            @endif
            <div class="hero-grid">
                <div class="hero-stat">
                    <div class="hero-stat-label">SuprafaÈ›Äƒ</div>
                    <div class="hero-stat-value">{{ $hero['size_sqm'] ?? '' }} mÂ²</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-label">Camere</div>
                    <div class="hero-stat-value">{{ $hero['rooms'] ?? '-' }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-label">Etaj</div>
                    <div class="hero-stat-value">{{ $hero['floor'] ?? '' }}@if(!empty($hero['total_floors']))/{{ $hero['total_floors'] }}@endif</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-label">Stare</div>
                    <div class="hero-stat-value">{{ ucfirst($hero['condition'] ?? '') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-label">An constr.</div>
                    <div class="hero-stat-value">{{ $hero['year_built'] ?? '-' }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-label">Disponibil</div>
                    <div class="hero-stat-value">{{ $hero['available_from'] ?? '' }}</div>
                </div>
            </div>
        </div>

        {{-- KPIs as badge-colored circles --}}
        <div class="kpi-row">
            @foreach($kpis as $kpi)
                @php
                    $isInverted = ($kpi['id'] ?? '') === 'risk_score';
                    $kpiColor = gradeColor($kpi['value'], $isInverted);
                    $kpiClass = gradeClass($kpi['value'], $isInverted);
                    $trendSymbol = match($kpi['trend'] ?? 'stable') { 'up' => '+', 'down' => '-', default => '=' };
                @endphp
                <div class="kpi-card">
                    <div class="kpi-circle {{ $kpiClass }}">
                        <div class="kpi-score" style="color: {{ $kpiColor }};">{{ $kpi['value'] }}<span class="kpi-max">/{{ $kpi['max'] ?? 10 }}</span></div>
                    </div>
                    <div class="kpi-label">{{ $kpi['label'] ?? '' }}</div>
                    <div class="kpi-trend trend-{{ $kpi['trend'] ?? 'stable' }}">{{ $trendSymbol }} {{ ucfirst($kpi['trend'] ?? 'stabil') }}</div>
                </div>
            @endforeach
        </div>

        {{-- Charts Row 1: Horizontal Bar + Donut --}}
        <div class="charts-grid">
            @if($hBarChart)
            <div class="chart-box">
                <div class="chart-title">{{ $hBarChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap">
                    <canvas id="hBarChart" height="120"></canvas>
                </div>
            </div>
            @endif

            @if($donutChart)
            <div class="chart-box">
                <div class="chart-title">{{ $donutChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap">
                    <canvas id="donutChart" height="120"></canvas>
                </div>
            </div>
            @endif
        </div>

        {{-- Charts Row 2: Pollution + Radar + Horizontal Bar --}}
        <div class="charts-grid">
            @if($pollutionChart)
            @php
                $pd = $pollutionChart['data'] ?? [];
                $aqiVal = $pd['aqi'] ?? 0;
                $aqiClass = $aqiVal <= 50 ? 'grade-green' : ($aqiVal <= 100 ? 'grade-amber' : 'grade-red');
                $aqiColor = $aqiVal <= 50 ? '#00A556' : ($aqiVal <= 100 ? '#d97706' : '#e05252');
            @endphp
            <div class="chart-box pollution-widget" style="flex: 0.7;">
                <div class="chart-title">{{ $pollutionChart['title'] ?? 'Indicatori de mediu' }}</div>
                <div class="env-aqi">
                    <div class="env-aqi-circle {{ $aqiClass }}">
                        <span class="env-aqi-val" style="color: {{ $aqiColor }};">{{ $aqiVal }}</span>
                    </div>
                    <div class="env-aqi-meta">
                        <span class="env-aqi-label">AQI â€” {{ $pd['aqi_label'] ?? '' }}</span>
                    </div>
                </div>
                <div class="env-metrics">
                    <div class="env-row"><span class="env-key">Calitate aer</span><span class="env-val">{{ $pd['air_quality_label'] ?? ($pd['aqi_label'] ?? 'â€“') }}</span></div>
                    <div class="env-row"><span class="env-key">Zgomot</span><span class="env-val">{{ $pd['noise_label'] ?? 'â€“' }} ({{ $pd['noise_db'] ?? 'â€“' }} dB)</span></div>
                    <div class="env-row"><span class="env-key">Spatii verzi</span><span class="env-val">{{ $pd['green_coverage_pct'] ?? 'â€“' }}%</span></div>
                    <div class="env-row"><span class="env-key">Surse poluare</span><span class="env-val">{{ $pd['pollution_sources'] ?? 'Niciuna Ã®n apropiere' }}</span></div>
                    <div class="env-row"><span class="env-key">Walkability</span><span class="env-val">{{ $pd['walkability_label'] ?? 'â€“' }}</span></div>
                </div>
            </div>
            @endif

            @if($radarChart)
            <div class="chart-box" style="display:flex;flex-direction:column;">
                <div class="chart-title">{{ $radarChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap" style="flex:1;position:relative;">
                    <canvas id="radarChart" style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
            @endif

            @if($barChart)
            <div class="chart-box" style="display:flex;flex-direction:column;">
                <div class="chart-title">{{ $barChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap" style="flex:1;position:relative;">
                    <canvas id="barChart" style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
            @endif
        </div>

        {{-- Badges --}}
        @if(!empty($badges))
        <div class="badges-row">
            @foreach($badges as $badge)
                <div class="badge-item {{ ($badge['positive'] ?? true) ? 'badge-positive' : 'badge-negative' }}">
                    <span class="badge-icon">
                        @switch($badge['icon'] ?? '')
                            @case('parking') P @break
                            @case('bus') T @break
                            @case('eye') V @break
                            @case('map') Z @break
                            @case('wind') A @break
                            @case('arrow-up') U @break
                            @case('home') C @break
                            @case('zap') E @break
                            @case('heart') I @break
                            @default - @break
                        @endswitch
                    </span>
                    <span>{{ $badge['label'] ?? '' }}: <strong>{{ $badge['value'] ?? '' }}</strong></span>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Verdict --}}
        @if(!empty($verdict))
        @php
            $recClass = match(strtoupper($verdict['recommendation'] ?? '')) {
                'NEGOCIAZÄ‚' => 'negotiate',
                'CUMPÄ‚RÄ‚', 'ÃŽNCHIRIAZÄ‚' => 'buy',
                'EVITÄ‚' => 'avoid',
                default => 'negotiate',
            };
            $verdictScoreColor = gradeColor($verdict['overall_score'] ?? 0);
        @endphp
        <div class="verdict-card">
            <div class="verdict-top">
                <div class="verdict-score-circle" style="background: {{ $verdictScoreColor }};">{{ $verdict['overall_score'] ?? '' }}<small>/10</small></div>
                <div>
                    <div class="verdict-rec-label">Recomandare finala</div>
                    <div class="verdict-rec {{ $recClass }}">{{ $verdict['recommendation'] ?? '' }}</div>
                </div>
            </div>
            <div class="verdict-oneliner">{{ $verdict['one_liner'] ?? '' }}</div>
            <div class="verdict-lists">
                @if(!empty($verdict['ideal_for']))
                <div class="verdict-list positive">
                    <div class="verdict-list-title">Ideal pentru</div>
                    <ul>@foreach($verdict['ideal_for'] as $item)<li><span class="vl-icon">-</span> {{ $item }}</li>@endforeach</ul>
                </div>
                @endif
                @if(!empty($verdict['not_ideal_for']))
                <div class="verdict-list negative">
                    <div class="verdict-list-title">Nu se recomanda</div>
                    <ul>@foreach($verdict['not_ideal_for'] as $item)<li><span class="vl-icon">-</span> {{ $item }}</li>@endforeach</ul>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{--  DETAIL PAGES â€” CONTINUOUS ARTICLE FLOW                  --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="article-flow">
@foreach($pages as $pageIdx => $pg)
    {{-- Chapter heading --}}
    <div class="chapter-heading">
        <div class="ch-icon">
            @switch($pg['icon'] ?? '')
                @case('euro')
                    <svg viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @break
                @case('map-pin')
                    <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    @break
                @case('home')
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    @break
                @case('briefcase')
                    <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                    @break
                @case('shield')
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    @break
                @case('star')
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @break
                @default
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
            @endswitch
        </div>
        <span class="ch-title">{{ $pg['title'] ?? '' }}</span>
        <span class="ch-number">{{ $pageIdx + 2 }}/{{ count($pages) + 1 }}</span>
    </div>

    <div class="chapter-content">
        @if(!empty($pg['summary_text']))
            <div class="summary-box">{{ $pg['summary_text'] }}</div>
        @endif

        @foreach(($pg['sections'] ?? []) as $section)
            <div class="section-block">
                <div class="section-heading">
                    @php
                        $hLower = strtolower($section['heading'] ?? '');
                    @endphp
                    {{ $section['heading'] ?? '' }}
                </div>

                @if(!empty($section['body']))
                    <div class="section-body">{{ $section['body'] }}</div>
                @endif

                {{-- Data points --}}
                @if(!empty($section['data_points']))
                    <div class="data-grid">
                        @foreach($section['data_points'] as $dp)
                            @php
                                $dpLabel = strtolower($dp['label'] ?? '');
                                $isHighlight = str_contains($dpLabel, 'corectÄƒ') || str_contains($dpLabel, 'preÈ› / mp');
                            @endphp
                            <div class="data-cell{{ count($section['data_points']) % 2 !== 0 && $loop->last ? ' full-width' : '' }}{{ $isHighlight ? ' highlight-yellow' : '' }}">
                                <span class="data-label">{{ $dp['label'] ?? '' }}</span>
                                <span class="data-value">
                                    @if(is_numeric($dp['value'] ?? ''))
                                        {{ number_format($dp['value'], ($dp['value'] == intval($dp['value'])) ? 0 : 1) }}
                                    @else
                                        {{ ucfirst($dp['value'] ?? '') }}
                                    @endif
                                    @if(!empty($dp['unit'])) <span class="unit">{{ $dp['unit'] }}</span> @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Item lists --}}
                @if(!empty($section['items']))
                    @php
                        $lc = '';
                        $hLow = strtolower($section['heading'] ?? '');
                        if (str_contains($hLow, 'cresc') || str_contains($hLow, 'ce creÈ™te')) $lc = 'green-border';
                        elseif (str_contains($hLow, 'scad') || str_contains($hLow, 'ascuns') || str_contains($hLow, 'ce scade')) $lc = 'red-border';
                    @endphp
                    <ul class="item-list {{ $lc }}">
                        @foreach($section['items'] as $item)<li>{{ $item }}</li>@endforeach
                    </ul>
                @endif

                {{-- Cost items table --}}
                @if(!empty($section['cost_items']))
                    <table class="cost-table">
                        <thead><tr><th>Cost</th><th style="text-align:right">Suma</th><th style="text-align:center">Inclus in chirie</th></tr></thead>
                        <tbody>
                            @foreach($section['cost_items'] as $ci)
                                <tr>
                                    <td>{{ $ci['label'] ?? '' }}</td>
                                    <td style="text-align:right;font-weight:600;">â‚¬{{ $ci['value'] ?? 0 }}/lunÄƒ</td>
                                    <td style="text-align:center;" class="{{ ($ci['included_in_rent'] ?? false) ? 'included' : 'not-included' }}">
                                        {{ ($ci['included_in_rent'] ?? false) ? 'Da' : 'Nu' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if(!empty($section['total_estimated_monthly_cost']))
                        <tfoot>
                            <tr><td>Total estimat</td><td style="text-align:right;">â‚¬{{ number_format($section['total_estimated_monthly_cost']) }}/lunÄƒ</td><td></td></tr>
                        </tfoot>
                        @endif
                    </table>
                @endif

                {{-- Negotiation --}}
                @if(!empty($section['negotiation_arguments']))
                    <div class="negotiation-box">
                        @if(!empty($section['target_rent']))
                            <div class="negotiation-target">Chirie tinta: <span>â‚¬{{ number_format($section['target_rent']) }}/lunÄƒ</span></div>
                        @endif
                        <ul class="negotiation-list">
                            @foreach($section['negotiation_arguments'] as $arg)<li>{{ $arg }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                {{-- Transport --}}
                @if(!empty($section['transport']))
                    @php $tr = $section['transport']; @endphp
                    <div class="data-grid">
                        <div class="data-cell"><span class="data-label">Transport public</span><span class="data-value">{{ ($tr['public_transport'] ?? false) ? 'Da' : 'Nu' }}</span></div>
                        <div class="data-cell"><span class="data-label">Frecventa</span><span class="data-value">{{ $tr['frequency_minutes'] ?? '' }} min</span></div>
                        <div class="data-cell"><span class="data-label">Centrul orasului</span><span class="data-value">{{ $tr['city_center_minutes'] ?? '' }} min</span></div>
                        <div class="data-cell"><span class="data-label">Parcare</span><span class="data-value">{{ $tr['parking_spaces'] ?? 0 }} loc{{ ($tr['parking_included_in_rent'] ?? false) ? ' (inclus)' : '' }}{{ (!($tr['parking_included_in_rent'] ?? false) && !empty($tr['parking_extra_cost_eur'])) ? ' (â‚¬'.$tr['parking_extra_cost_eur'].'/lunÄƒ)' : '' }}</span></div>
                    </div>
                    @if(!empty($tr['nearest_stop_minutes_walk']))
                        <div style="font-size:10px;color:#334155;margin:4px 0 6px;">Cea mai apropiata statie: <strong>{{ $tr['nearest_stop_minutes_walk'] }} min mers pe jos</strong>
                            @if(!empty($tr['bike_friendly'])) Â· Prietenos biciclete @endif
                        </div>
                    @endif
                    @if(!empty($tr['transport_lines']))
                        <div class="transport-lines">
                            @foreach($tr['transport_lines'] as $line)<span class="transport-line">{{ $line }}</span>@endforeach
                        </div>
                    @endif
                @endif

                {{-- Amenities --}}
                @if(!empty($section['amenities']) && is_array($section['amenities']))
                    <div class="amenity-grid">
                        @foreach($section['amenities'] as $key => $am)
                            @if(is_array($am) && ($am['exists'] ?? false))
                                <div class="amenity-chip">
                                    - {{ $am['details'] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                    @if(!empty($am['distance_minutes_walk']))
                                        <span class="amenity-dist">{{ $am['distance_minutes_walk'] }} min</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Positive/negative developments --}}
                @if(!empty($section['positive_developments']))
                    <div class="dev-positive">Factori pozitivi:</div>
                    <ul class="item-list green-border">
                        @foreach($section['positive_developments'] as $pd)<li>{{ $pd }}</li>@endforeach
                    </ul>
                @endif
                @if(!empty($section['negative_developments']))
                    <div class="dev-negative">Factori de risc:</div>
                    <ul class="item-list red-border">
                        @foreach($section['negative_developments'] as $nd)<li>{{ $nd }}</li>@endforeach
                    </ul>
                @endif

                {{-- Legacy: trend_drivers / risk_factors --}}
                @if(!empty($section['trend_drivers']))
                    <div class="dev-positive">Factori pozitivi:</div>
                    <ul class="item-list green-border">@foreach($section['trend_drivers'] as $td)<li>{{ $td }}</li>@endforeach</ul>
                @endif
                @if(!empty($section['risk_factors']))
                    <div class="dev-negative">Factori de risc:</div>
                    <ul class="item-list red-border">@foreach($section['risk_factors'] as $rf)<li>{{ $rf }}</li>@endforeach</ul>
                @endif

                {{-- Air quality --}}
                @if(!empty($section['air_quality']))
                    <div class="air-quality-box">
                        <div class="aq-badge"><span>{{ ucfirst($section['air_quality']) }}</span><small>Calitate aer</small></div>
                        <div class="aq-text">{{ $section['impact_on_living'] ?? $section['impact_on_business'] ?? '' }}</div>
                    </div>
                    @if(!empty($section['potential_issues']))
                        @foreach($section['potential_issues'] as $pi)
                            <div class="warning-box">{{ $pi }}</div>
                        @endforeach
                    @endif
                @endif

                {{-- Installations table --}}
                @if(!empty($section['installations']))
                    <table class="install-table">
                        <thead><tr><th>Element</th><th>Stare</th><th>Detalii</th></tr></thead>
                        <tbody>
                            @foreach($section['installations'] as $inst)
                                @php
                                    $cond = strtolower($inst['condition'] ?? 'medie');
                                    $condClass = match(true) {
                                        str_contains($cond, 'bun') || str_contains($cond, 'nou') => 'condition-bun',
                                        str_contains($cond, 'medi') => 'condition-medie',
                                        str_contains($cond, 'vechi') || str_contains($cond, 'absent') => 'condition-vechi',
                                        default => 'condition-medie',
                                    };
                                @endphp
                                <tr>
                                    <td style="font-weight:600;">{{ $inst['item'] ?? '' }}</td>
                                    <td><span class="condition-badge {{ $condClass }}">{{ $inst['condition'] ?? '' }}</span></td>
                                    <td>{{ $inst['notes'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Ideal tenant profiles --}}
                @if(!empty($section['ideal_tenant_profiles']))
                    @foreach($section['ideal_tenant_profiles'] as $tp)
                        <div class="profile-card">
                            <div class="profile-score">{{ $tp['fit_score'] ?? '' }}</div>
                            <div class="profile-info">
                                <div class="profile-name">{{ $tp['profile'] ?? $tp['type'] ?? '' }}</div>
                                <div class="profile-reason">{{ $tp['reason'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Not recommended for --}}
                @if(!empty($section['not_recommended_for']))
                    <div style="margin-top:5px;margin-bottom:5px;">
                        <div style="font-size:10px;font-weight:700;color:#991b1b;margin-bottom:4px;">Nu se recomanda:</div>
                        <ul class="not-rec-list">
                            @foreach($section['not_recommended_for'] as $nr)
                                <li>
                                    <span class="not-rec-name">{{ $nr['profile'] ?? $nr['type'] ?? '' }}</span>
                                    <div class="not-rec-reason">{{ $nr['reason'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Business types (legacy) --}}
                @if(!empty($section['ideal_business_types']))
                    @foreach($section['ideal_business_types'] as $bt)
                        <div class="profile-card">
                            <div class="profile-score">{{ $bt['fit_score'] ?? '' }}</div>
                            <div class="profile-info">
                                <div class="profile-name">{{ $bt['type'] ?? '' }}</div>
                                <div class="profile-reason">{{ $bt['reason'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
                @if(!empty($section['not_recommended']))
                    <div style="margin-top:5px;margin-bottom:5px;">
                        <div style="font-size:10px;font-weight:700;color:#991b1b;margin-bottom:4px;">Nu se recomanda:</div>
                        <ul class="not-rec-list">
                            @foreach($section['not_recommended'] as $nr)
                                <li><span class="not-rec-name">{{ $nr['type'] ?? '' }}</span><div class="not-rec-reason">{{ $nr['reason'] ?? '' }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Scenarios (legacy) --}}
                @if(!empty($section['scenarios']))
                    @foreach($section['scenarios'] as $sc)
                        <div style="border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px;background:linear-gradient(135deg,#f8f7fd 0%,#fff 100%);">
                            <div style="font-size:12px;font-weight:700;color:var(--primary);margin-bottom:3px;">{{ $sc['scenario_name'] ?? '' }}</div>
                            <div style="font-size:10px;color:#334155;margin-bottom:6px;line-height:1.45;">{{ $sc['description'] ?? '' }}</div>
                            <div style="display:flex;gap:8px;">
                                <div style="flex:1;text-align:center;padding:6px;background:var(--card);border-radius:6px;border:1px solid var(--border);">
                                    <div style="font-size:15px;font-weight:800;color:var(--green);">â‚¬{{ number_format($sc['estimated_monthly_revenue_potential'] ?? 0) }}</div>
                                    <div style="font-size:8px;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Venituri lunare</div>
                                </div>
                                <div style="flex:1;text-align:center;padding:6px;background:var(--card);border-radius:6px;border:1px solid var(--border);">
                                    <div style="font-size:15px;font-weight:800;color:var(--primary);">â‚¬{{ number_format($sc['setup_cost_estimate'] ?? 0) }}</div>
                                    <div style="font-size:8px;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Cost amenajare</div>
                                </div>
                                <div style="flex:1;text-align:center;padding:6px;background:var(--card);border-radius:6px;border:1px solid var(--border);">
                                    <div style="font-size:15px;font-weight:800;color:#4e59b7;">{{ $sc['break_even_months'] ?? '' }} luni</div>
                                    <div style="font-size:8px;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Break-even</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Competitor types (legacy) --}}
                @if(!empty($section['competitor_types']))
                    <ul class="item-list orange-border">@foreach($section['competitor_types'] as $ct)<li>{{ $ct }}</li>@endforeach</ul>
                    @if(!empty($section['competitive_advantage_possible']))
                        <div style="font-size:10px;color:#166534;background:var(--green-light);padding:6px 8px;border-radius:6px;margin-bottom:8px;">
                            <strong>Oportunitate:</strong> {{ $section['competitive_advantage_possible'] }}
                        </div>
                    @endif
                @endif

                {{-- Risks table --}}
                @if(!empty($section['risks']))
                    <table class="risk-table">
                        <thead><tr><th style="width:14%">Categorie</th><th style="width:11%">Severitate</th><th>Descriere</th><th style="width:24%">Mitigare</th></tr></thead>
                        <tbody>
                            @foreach($section['risks'] as $risk)
                                <tr>
                                    <td style="font-weight:600;">{{ ucfirst($risk['category'] ?? '') }}</td>
                                    <td><span class="severity-badge severity-{{ strtolower($risk['severity'] ?? 'mic') }}">{{ $risk['severity'] ?? '' }}</span></td>
                                    <td>{{ $risk['description'] ?? '' }}</td>
                                    <td style="font-size:10px;">{{ $risk['mitigation'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Checklist --}}
                @if(!empty($section['checklist']))
                    <ul class="checklist">
                        @foreach($section['checklist'] as $chk)
                            <li>
                                <div class="checklist-checkbox"></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;">{{ $chk['item'] ?? '' }}</div>
                                    <div style="font-size:9px;color:#64748b;">{{ $chk['why'] ?? '' }}</div>
                                </div>
                                <span class="priority-badge priority-{{ strtolower($chk['priority'] ?? 'recomandat') }}">{{ $chk['priority'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Contract clauses --}}
                @if(!empty($section['clauses_to_check']))
                    <ul class="clauses-list">
                        @foreach($section['clauses_to_check'] as $clause)<li>{{ $clause }}</li>@endforeach
                    </ul>
                @endif

                {{-- Exit conditions --}}
                @if(!empty($section['exit_conditions']))
                    @php $ex = $section['exit_conditions']; @endphp
                    <div class="exit-box">
                        <div class="exit-row"><span class="exit-label">Preaviz reziliere</span><span class="exit-value">{{ $ex['notice_period_months'] ?? '' }} lunÄƒ</span></div>
                        <div class="exit-row"><span class="exit-label">Penalizare estimata</span><span class="exit-value" style="font-size:10px;">{{ $ex['penalty_estimate'] ?? '' }}</span></div>
                        <div class="exit-row"><span class="exit-label">Returnare garantie</span><span class="exit-value" style="font-size:10px;">{{ $ex['deposit_return_conditions'] ?? '' }}</span></div>
                    </div>
                @endif

                {{-- Verdict (summary page) --}}
                @if(!empty($section['verdict']))
                    <div class="final-verdict-card">
                        <div class="final-score">{{ $section['overall_score'] ?? '' }}<span>/10</span></div>
                        <div class="final-verdict-label">Scor general</div>
                        @php
                            $recClass = match(strtoupper($section['verdict'] ?? '')) {
                                'NEGOCIAZÄ‚' => 'negotiate',
                                'CUMPÄ‚RÄ‚', 'ÃŽNCHIRIAZÄ‚' => 'buy',
                                'EVITÄ‚' => 'avoid',
                                default => 'negotiate',
                            };
                        @endphp
                        <div class="final-rec {{ $recClass }}">{{ $section['verdict'] }}</div>
                    </div>
                    @if(!empty($section['score_breakdown']))
                        <div class="score-breakdown">
                            @foreach($section['score_breakdown'] as $sb)
                                <div class="score-dim">
                                    <div class="score-dim-value">{{ $sb['score'] ?? '' }}</div>
                                    <div class="score-dim-label">{{ $sb['dimension'] ?? '' }}</div>
                                    <div class="score-dim-weight">Pondere: {{ $sb['weight_percent'] ?? '' }}%</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                {{-- Next steps --}}
                @if(!empty($section['next_steps']))
                    @foreach($section['next_steps'] as $step)
                        <div class="step-item">
                            <div class="step-number">{{ $step['step'] ?? '' }}</div>
                            <div class="step-content">
                                <div class="step-action">Actiune: {{ $step['action'] ?? '' }}</div>
                                <div class="step-timeline">Orizont: {{ $step['timeline'] ?? '' }} <span class="priority-badge priority-{{ strtolower($step['priority'] ?? 'recomandat') }}" style="margin-left:3px;">{{ $step['priority'] ?? '' }}</span></div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Questions --}}
                @if(!empty($section['questions']))
                    <ol class="question-list">
                        @foreach($section['questions'] as $q)<li>{{ $q }}</li>@endforeach
                    </ol>
                @endif

                {{-- Viewing checklist --}}
                @if(!empty($section['viewing_checklist']))
                    <ul class="viewing-list">
                        @foreach($section['viewing_checklist'] as $vc)<li>{{ $vc }}</li>@endforeach
                    </ul>
                @endif
            </div>
        @endforeach

        {{-- Action items --}}
        @if(!empty($pg['action_items']))
            <div class="action-items">
                <div class="action-items-title">
                    Actiuni recomandate
                </div>
                <ul>@foreach($pg['action_items'] as $ai)<li>{{ $ai }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
@endforeach
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{--  CHART.JS INITIALIZATION                                --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.fonts.ready.then(function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.devicePixelRatio = 3;

    // Consistent color mapping
    function gradeColor(v, invert) {
        if (invert) v = 10 - v;
        if (v >= 8) return '#00A556';
        if (v >= 6) return '#d97706';
        return '#e05252';
    }

    @if($barChart)
    (function() {
        var ctx = document.getElementById('barChart');
        if (!ctx) return;
        var labels = @json($barChart['data']['labels'] ?? []);
        var values = @json($barChart['data']['values'] ?? []);
        var hiIdx = {{ $barChart['data']['highlight_index'] ?? -1 }};
        var colors = values.map(function(v, i) {
            return i === hiIdx ? '#7380d9' : (i === 0 ? '#34306a' : '#94a3b8');
        });
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 4,
                    maxBarThickness: 28,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                layout: { padding: { top: 20 } },
                plugins: {
                    tooltip: { enabled: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: Math.min.apply(null, values) * 0.9,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function(v) { return 'â‚¬' + v; },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                }
            },
            plugins: [{
                id: 'barValueLabels',
                afterDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '700 10px "Inter", sans-serif';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'bottom';
                    chart.data.datasets[0].data.forEach(function(val, i) {
                        var meta = chart.getDatasetMeta(0);
                        var bar = meta.data[i];
                        ctx2.fillStyle = '#34306a';
                        ctx2.fillText('â‚¬' + val, bar.x, bar.y - 4);
                    });
                    ctx2.restore();
                }
            }]
        });
    })();
    @endif

    @if($donutChart)
    (function() {
        var ctx = document.getElementById('donutChart');
        if (!ctx) return;
        var segs = @json($donutChart['data']['segments'] ?? []);
        var labels = segs.map(function(s) { return s.label; });
        var values = segs.map(function(s) { return s.value; });
        var colors = ['#34306a', '#4e59b7', '#7380d9', '#64748b', '#e05252'];
        var total = values.reduce(function(a, b) { return a + b; }, 0);
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, values.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                    spacing: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                cutout: '58%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            boxWidth: 11,
                            boxHeight: 11,
                            padding: 7,
                            font: { size: 10 },
                            generateLabels: function(chart) {
                                var data = chart.data;
                                return data.labels.map(function(label, i) {
                                    return {
                                        text: label + ' â‚¬' + data.datasets[0].data[i],
                                        fillStyle: colors[i],
                                        strokeStyle: '#fff',
                                        lineWidth: 0,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    tooltip: { enabled: false }
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function(chart) {
                    var width = chart.width, height = chart.height, ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '800 16px "Inter", sans-serif';
                    ctx2.fillStyle = '#34306a';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'middle';
                    var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                    ctx2.fillText('â‚¬' + total, centerX, centerY - 6);
                    ctx2.font = '500 8px "Inter", sans-serif';
                    ctx2.fillStyle = '#94a3b8';
                    ctx2.fillText('total/lunÄƒ', centerX, centerY + 10);
                    ctx2.restore();
                }
            }]
        });
    })();
    @endif

    @if($radarChart)
    (function() {
        var ctx = document.getElementById('radarChart');
        if (!ctx) return;
        var axes = @json($radarChart['data']['axes'] ?? []);
        var values = @json($radarChart['data']['values'] ?? []);
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: axes,
                datasets: [{
                    data: values,
                    backgroundColor: 'rgba(0, 115, 240, 0.12)',
                    borderColor: '#7380d9',
                    borderWidth: 2,
                    pointBackgroundColor: values.map(function(v) { return gradeColor(v); }),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    tooltip: { enabled: false }
                },
                scales: {
                    r: {
                        min: 0,
                        max: {{ $radarChart['data']['max'] ?? 10 }},
                        ticks: {
                            stepSize: 2,
                            display: false,
                            backdropColor: 'transparent'
                        },
                        pointLabels: {
                            font: { size: 9, weight: '600' },
                            color: '#34306a',
                            padding: 18
                        },
                        grid: { color: '#e5e7eb' },
                        angleLines: { color: '#e5e7eb' }
                    }
                }
            },
            plugins: [{
                id: 'radarValueLabels',
                afterDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '800 10px "Inter", sans-serif';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'middle';
                    var meta = chart.getDatasetMeta(0);
                    var scale = chart.scales.r;
                    meta.data.forEach(function(point, i) {
                        var color = gradeColor(values[i]);
                        ctx2.fillStyle = color;
                        // Push label outward from center, past the point
                        var angle = scale.getIndexAngle(i) - Math.PI / 2;
                        var cx = scale.xCenter;
                        var cy = scale.yCenter;
                        var dx = point.x - cx;
                        var dy = point.y - cy;
                        var len = Math.sqrt(dx * dx + dy * dy) || 1;
                        var offset = 12;
                        var lx = point.x + (dx / len) * offset;
                        var ly = point.y + (dy / len) * offset;
                        ctx2.fillText(values[i].toString(), lx, ly);
                    });
                    ctx2.restore();
                }
            }]
        });
    })();
    @endif

    @if($hBarChart)
    (function() {
        var ctx = document.getElementById('hBarChart');
        if (!ctx) return;
        var rawLabels = @json($hBarChart['data']['labels'] ?? []);
        var labels = rawLabels.map(function(l) { return l; });
        var values = @json($hBarChart['data']['values'] ?? []);
        var colors = values.map(function(v, i) {
            // "Costuri ascunse estimate" always red
            if (labels[i] === 'Costuri ascunse estimate') return '#dc2626';
            return gradeColor(v);
        });
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 4,
                    maxBarThickness: 14
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                indexAxis: 'y',
                layout: { padding: { right: 28 } },
                plugins: {
                    tooltip: { enabled: false }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 10,
                        grid: { color: '#f0f0f0' },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            autoSkip: false,
                            font: { size: 9, weight: '500' },
                            color: function(context) {
                                if (labels[context.index] === 'Costuri ascunse estimate') return '#dc2626';
                                return '#64748b';
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'hBarValueLabels',
                afterDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '700 9px "Inter", sans-serif';
                    ctx2.textAlign = 'left';
                    ctx2.textBaseline = 'middle';
                    chart.getDatasetMeta(0).data.forEach(function(bar, i) {
                        ctx2.fillStyle = colors[i];
                        ctx2.fillText(values[i].toString(), bar.x + 4, bar.y);
                    });
                    ctx2.restore();
                }
            }]
        });
    })();
    @endif
    }); // fonts.ready
});
</script>

</body>
</html>

