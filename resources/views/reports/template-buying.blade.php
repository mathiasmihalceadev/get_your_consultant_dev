<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport AnalizÄƒ Proprietate â€” CumpÄƒrare</title>
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
        .hero-card::before { display: none; }
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
        .hero-deviation {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            margin-top: 4px;
        }
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
        .kpi-card::before { display: none; }
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
        .chart-box::before { display: none; }
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
        .badge-neutral { background: var(--amber-light); color: #854d0e; border: 1px solid var(--amber-border); }
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
        .cost-table .cost-note { font-size: 9px; color: var(--text-muted); font-style: italic; display: block; margin-top: 1px; }
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
        .severity-ridicat { background: var(--red-light); color: #991b1b; }

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
        .priority-critic { background: #fee2e2; color: #7f1d1d; border: 1px solid var(--red-border); }
        .priority-important { background: var(--amber-light); color: #854d0e; }
        .priority-recomandat { background: #dbeafe; color: #1e40af; }
        .priority-mediu { background: #f0f9ff; color: #075985; }

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

        /* Scenario cards (mortgage, investment, appreciation) */
        .scenario-card {
            border: 1px solid var(--border); border-radius: 10px;
            padding: 10px 14px; margin-bottom: 8px;
            background: linear-gradient(135deg, #f8f7fd 0%, #fff 100%);
            page-break-inside: avoid;
        }
        .scenario-title { font-size: 12px; font-weight: 700; color: var(--primary); margin-bottom: 3px; }
        .scenario-details { font-size: 10px; color: #334155; line-height: 1.45; }
        .scenario-grid {
            display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;
        }
        .scenario-metric {
            flex: 1; min-width: 80px; text-align: center;
            background: #fff; border: 1px solid var(--border); border-radius: 6px;
            padding: 5px 6px;
        }
        .scenario-metric-value { font-size: 13px; font-weight: 800; color: var(--primary); }
        .scenario-metric-label { font-size: 8px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }

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
            font-size: 10px; color: #854d0e; background: var(--amber-light);
            padding: 7px 10px; border-radius: 6px; margin-bottom: 10px;
        }

        /* Red flags */
        .red-flag-card {
            border: 1px solid var(--red-border); border-radius: 8px;
            padding: 8px 12px; margin-bottom: 6px;
            background: var(--red-light);
            page-break-inside: avoid;
        }
        .red-flag-signal { font-size: 11px; font-weight: 700; color: #991b1b; margin-bottom: 2px; }
        .red-flag-action { font-size: 10px; color: #7f1d1d; line-height: 1.4; }

        /* Legal checks */
        .legal-check-item {
            border: 1px solid var(--border); border-radius: 8px;
            padding: 8px 12px; margin-bottom: 6px;
            background: #f8f9fb;
            page-break-inside: avoid;
        }
        .legal-check-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px; }
        .legal-check-title { font-size: 11px; font-weight: 700; color: var(--primary); }
        .legal-check-body { font-size: 10px; color: #334155; line-height: 1.45; }
        .legal-check-how { font-size: 9px; color: var(--text-muted); font-style: italic; margin-top: 2px; }

        /* Structural assessment */
        .structural-box {
            border: 1px solid var(--border); border-radius: 10px;
            padding: 12px 14px; margin-bottom: 12px;
            background: linear-gradient(135deg, #fefce8 0%, #fff 100%);
        }
        .structural-grid {
            display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;
        }
        .structural-metric {
            flex: 1; min-width: 100px; text-align: center;
            padding: 6px; border: 1px solid var(--border); border-radius: 6px; background: #fff;
        }
        .structural-metric-value { font-size: 13px; font-weight: 800; color: var(--primary); }
        .structural-metric-label { font-size: 8px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }

        /* Fiscal cards */
        .fiscal-card {
            border: 1px solid var(--border); border-radius: 8px;
            padding: 8px 12px; margin-bottom: 6px;
            background: #f8f9fb;
            page-break-inside: avoid;
        }
        .fiscal-card-topic { font-size: 11px; font-weight: 700; color: var(--primary); margin-bottom: 2px; }
        .fiscal-card-detail { font-size: 10px; color: #334155; line-height: 1.45; }
        .fiscal-card-payer { font-size: 9px; color: var(--text-muted); font-style: italic; margin-top: 2px; }

        /* Improvement table */
        .improvement-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10.5px; border-radius: 8px; overflow: hidden; }
        .improvement-table th {
            background: var(--primary); color: #fff;
            padding: 6px 10px; text-align: left;
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 700;
        }
        .improvement-table td { padding: 6px 10px; border-bottom: 1px solid var(--border); }
        .improvement-table tr:nth-child(even) td { background: #f8f9fb; }
        .improvement-table .roi-value { font-weight: 700; color: var(--green); }

        /* â”€â”€ PRINT PAGE-BREAK RULES â”€â”€ */
        .verdict-card,
        .hero-card,
        .kpi-row,
        .charts-grid,
        .badges-row,
        .data-grid,
        .cost-table,
        .risk-table,
        .checklist li,
        .negotiation-box,
        .final-verdict-card,
        .score-breakdown,
        .step-item,
        .summary-box,
        .item-list,
        .viewing-list,
        .scenario-card,
        .red-flag-card,
        .legal-check-item,
        .structural-box,
        .fiscal-card,
        .improvement-table {
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

    $barChart = $donutChart = $hBarChart = $radarChart = $pollutionChart = $lineChart = null;
    foreach ($charts as $c) {
        if ($c['type'] === 'bar_comparison') $barChart = $c;
        if ($c['type'] === 'donut') $donutChart = $c;
        if ($c['type'] === 'bar_horizontal') $hBarChart = $c;
        if ($c['type'] === 'radar') $radarChart = $c;
        if ($c['type'] === 'pollution') $pollutionChart = $c;
        if ($c['type'] === 'line_chart' || $c['type'] === 'line_history') $lineChart = $c;
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
                        <div class="hero-price" style="font-size: 18px;">â‚¬{{ number_format($hero['asking_price'] ?? 0) }}</div>
                        <div class="hero-price-label">PreÈ› cerut</div>
                        @if(!empty($hero['price_deviation_pct']))
                            <div class="hero-deviation">
                                {{ $hero['price_deviation_pct'] > 0 ? '+' : '' }}{{ $hero['price_deviation_pct'] }}% faÈ›Äƒ de piaÈ›Äƒ
                            </div>
                        @endif
                    </div>
                    @if(!empty($hero['fair_value_estimate']))
                    <div class="hero-price-box" style="background: linear-gradient(135deg, var(--green), #047857); min-width: 160px; padding: 12px 18px;">
                        <div class="hero-price" style="font-size: 32px;">â‚¬{{ number_format($hero['fair_value_estimate']) }}</div>
                        <div class="hero-price-label">Valoare corectÄƒ estimatÄƒ</div>
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
                    <div class="hero-stat-label">Cls. energie</div>
                    <div class="hero-stat-value">{{ $hero['energy_class'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="kpi-row">
            @foreach($kpis as $kpi)
                @php
                    $isInverted = in_array($kpi['id'] ?? '', ['risk_score', 'legal_score', 'structural_score']);
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

        {{-- Charts Row 1: Horizontal Bar + Line Chart (price history) --}}
        <div class="charts-grid">
            @if($hBarChart)
            <div class="chart-box">
                <div class="chart-title">{{ $hBarChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap">
                    <canvas id="hBarChart" height="120"></canvas>
                </div>
            </div>
            @endif

            @if($lineChart)
            <div class="chart-box">
                <div class="chart-title">{{ $lineChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap">
                    <canvas id="lineChart" height="120"></canvas>
                </div>
            </div>
            @endif
        </div>

        {{-- Charts Row 2: Pollution + Radar + Total Acquisition Cost --}}
        <div class="charts-grid">
            @if($pollutionChart)
            @php
                $pd = $pollutionChart['data'] ?? [];
                $aqiVal = $pd['aqi'] ?? 0;
                $aqiClass = $aqiVal <= 50 ? 'grade-green' : ($aqiVal <= 100 ? 'grade-amber' : 'grade-red');
                $aqiColor = $aqiVal <= 50 ? '#00A556' : ($aqiVal <= 100 ? '#d97706' : '#e05252');
            @endphp
            <div class="chart-box pollution-widget" style="flex: 0.8;">
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
            <div class="chart-box" style="display:flex;flex-direction:column;flex:0.8;">
                <div class="chart-title">{{ $radarChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap" style="flex:1;position:relative;">
                    <canvas id="radarChart" style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;"></canvas>
                </div>
            </div>
            @endif

            @if($donutChart)
            <div class="chart-box" style="display:flex;flex-direction:column;flex:0.8;">
                <div class="chart-title">{{ $donutChart['title'] ?? '' }}</div>
                <div class="chart-canvas-wrap">
                    <canvas id="donutChart" height="132"></canvas>
                </div>
            </div>
            @endif
        </div>

        {{-- Badges --}}
        @if(!empty($badges))
        <div class="charts-grid" style="margin-bottom: 2px;">
            <div style="flex:1;display:flex;flex-wrap:wrap;align-content:flex-start;gap:4px;padding:0;">
                @foreach($badges as $badge)
                    @php
                        $badgeClass = match(true) {
                            ($badge['positive'] ?? null) === true => 'badge-positive',
                            ($badge['positive'] ?? null) === false => 'badge-negative',
                            default => 'badge-neutral',
                        };
                    @endphp
                    <div class="badge-item {{ $badgeClass }}">
                        <span class="badge-icon">
                            @switch($badge['icon'] ?? '')
                                @case('parking') P @break
                                @case('bus') T @break
                                @case('eye') V @break
                                @case('wind') A @break
                                @case('arrow-up') U @break
                                @case('home') C @break
                                @case('zap') E @break
                                @case('shield') S @break
                                @case('file-text') R @break
                                @case('alert-triangle') ! @break
                                @case('trending-up') U @break
                                @case('tool') T @break
                                @default - @break
                            @endswitch
                        </span>
                        <span>{{ $badge['label'] ?? '' }}: <strong>{{ $badge['value'] ?? '' }}</strong></span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Verdict --}}
        @if(!empty($verdict))
        @php
            $recUpper = strtoupper($verdict['recommendation'] ?? '');
            $recClass = match(true) {
                str_contains($recUpper, 'EVITÄ‚') => 'avoid',
                str_contains($recUpper, 'CUMPÄ‚RÄ‚') || str_contains($recUpper, 'ÃŽNCHIRIAZÄ‚') => 'buy',
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
                @case('shield')
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    @break
                @case('star')
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @break
                @case('trending-up')
                    <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-linecap="round" stroke-linejoin="round"/><polyline points="17 6 23 6 23 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                                $isHighlight = str_contains($dpLabel, 'estimat') || str_contains($dpLabel, 'valoare corectÄƒ') || str_contains($dpLabel, 'preÈ› / mÂ²') || str_contains($dpLabel, 'preÈ› mÂ²');
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

                {{-- Cost items table (buying version) --}}
                @if(!empty($section['cost_items']))
                    <table class="cost-table">
                        <thead><tr><th>Cost</th><th style="text-align:right">Suma</th><th style="text-align:center">Obligatoriu</th></tr></thead>
                        <tbody>
                            @foreach($section['cost_items'] as $ci)
                                <tr>
                                    <td>
                                        {{ $ci['label'] ?? '' }}
                                        @if(!empty($ci['note']))
                                            <span class="cost-note">{{ $ci['note'] }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;font-weight:600;">â‚¬{{ number_format($ci['value'] ?? 0) }}</td>
                                    <td style="text-align:center;" class="{{ ($ci['mandatory'] ?? false) ? 'included' : 'not-included' }}">
                                        {{ ($ci['mandatory'] ?? false) ? 'Da' : 'Optional' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if(!empty($section['total_acquisition_cost']))
                        <tfoot>
                            <tr><td>Total estimat achiziÈ›ie</td><td style="text-align:right;">â‚¬{{ number_format($section['total_acquisition_cost']) }}</td><td></td></tr>
                        </tfoot>
                        @endif
                    </table>
                @endif

                {{-- Negotiation --}}
                @if(!empty($section['negotiation_arguments']))
                    <div class="negotiation-box">
                        @if(!empty($section['target_price']))
                            <div class="negotiation-target">Pret tinta: <span>â‚¬{{ number_format($section['target_price']) }}</span></div>
                        @endif
                        <ul class="negotiation-list">
                            @foreach($section['negotiation_arguments'] as $arg)<li>{{ $arg }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                {{-- Investment scenarios (legacy format) --}}
                @if(!empty($section['investment_scenarios']))
                    @foreach($section['investment_scenarios'] as $sc)
                        <div class="scenario-card">
                            <div class="scenario-title">{{ $sc['scenario'] ?? '' }}</div>
                            <div class="scenario-details">{{ $sc['details'] ?? '' }}</div>
                        </div>
                    @endforeach
                @endif

                {{-- Mortgage scenarios --}}
                @if(!empty($section['scenarios']))
                    @if(!empty($section['mortgage_note']))
                        <div class="warning-box">{{ $section['mortgage_note'] }}</div>
                    @endif
                    @foreach($section['scenarios'] as $ms)
                        <div class="scenario-card">
                            <div class="scenario-title">{{ $ms['label'] ?? '' }}</div>
                            <div class="scenario-grid">
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">â‚¬{{ number_format($ms['down_payment_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">Avans</div>
                                </div>
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">â‚¬{{ number_format($ms['monthly_rate_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">RatÄƒ/lunÄƒ</div>
                                </div>
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">â‚¬{{ number_format($ms['loan_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">Credit</div>
                                </div>
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">â‚¬{{ number_format($ms['total_cost_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">Cost total</div>
                                </div>
                            </div>
                            @if(!empty($ms['dsti_recommendation']))
                                <div style="font-size: 9px; color: var(--text-muted); margin-top: 4px; font-style: italic;">{{ $ms['dsti_recommendation'] }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Rental data --}}
                @if(!empty($section['rental_data']))
                    @php $rd = $section['rental_data']; @endphp
                    <div class="data-grid">
                        <div class="data-cell highlight-yellow"><span class="data-label">Chirie est. (mobilat)</span><span class="data-value">â‚¬{{ number_format($rd['estimated_monthly_rent_furnished_eur'] ?? 0) }} <span class="unit">/lunÄƒ</span></span></div>
                        <div class="data-cell"><span class="data-label">Chirie est. (nemobilat)</span><span class="data-value">â‚¬{{ number_format($rd['estimated_monthly_rent_unfurnished_eur'] ?? 0) }} <span class="unit">/lunÄƒ</span></span></div>
                        <div class="data-cell"><span class="data-label">Randament brut</span><span class="data-value">{{ $rd['gross_yield_pct'] ?? 0 }}%</span></div>
                        <div class="data-cell highlight-yellow"><span class="data-label">Randament net</span><span class="data-value">{{ $rd['net_yield_pct'] ?? 0 }}%</span></div>
                        <div class="data-cell"><span class="data-label">Venit net anual</span><span class="data-value">â‚¬{{ number_format($rd['annual_net_rental_income_eur'] ?? 0) }}</span></div>
                        <div class="data-cell"><span class="data-label">Zile pÃ¢nÄƒ la Ã®nchiriere</span><span class="data-value">{{ $rd['avg_days_to_rent'] ?? '-' }} zile</span></div>
                        <div class="data-cell"><span class="data-label">Breakeven</span><span class="data-value">{{ $rd['breakeven_years'] ?? '-' }} ani</span></div>
                        <div class="data-cell"><span class="data-label">Cerere chiriaÈ™i</span><span class="data-value">{{ ucfirst($rd['tenant_demand'] ?? '-') }}</span></div>
                    </div>
                @endif

                {{-- Appreciation scenarios --}}
                @if(!empty($section['appreciation_scenarios']))
                    @foreach($section['appreciation_scenarios'] as $as)
                        @php
                            $asColor = match(strtolower($as['name'] ?? '')) {
                                'optimist' => 'var(--green)',
                                'pesimist' => 'var(--red)',
                                default => 'var(--tertiary)',
                            };
                        @endphp
                        <div class="scenario-card" style="border-left: 3px solid {{ $asColor }};">
                            <div class="scenario-title">{{ $as['name'] ?? '' }} (+{{ $as['annual_growth_pct'] ?? 0 }}%/an)</div>
                            <div class="scenario-grid">
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">â‚¬{{ number_format($as['value_2036_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">Valoare 2036</div>
                                </div>
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value" style="color: var(--green);">+â‚¬{{ number_format($as['total_gain_eur'] ?? 0) }}</div>
                                    <div class="scenario-metric-label">CÃ¢È™tig total</div>
                                </div>
                                <div class="scenario-metric">
                                    <div class="scenario-metric-value">+{{ $as['total_gain_pct'] ?? 0 }}%</div>
                                    <div class="scenario-metric-label">CreÈ™tere</div>
                                </div>
                            </div>
                            @if(!empty($as['driver']))
                                <div style="font-size: 9px; color: var(--text-muted); margin-top: 4px;">{{ $as['driver'] }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Buy vs Rent comparison --}}
                @if(!empty($section['buy_vs_rent']))
                    @php $bvr = $section['buy_vs_rent']; @endphp
                    <div class="data-grid">
                        <div class="data-cell highlight-yellow"><span class="data-label">Cost total cumpÄƒrare (10 ani)</span><span class="data-value">â‚¬{{ number_format($bvr['buy_total_cost_10yr_eur'] ?? 0) }}</span></div>
                        <div class="data-cell"><span class="data-label">Chirie totalÄƒ (10 ani)</span><span class="data-value">â‚¬{{ number_format($bvr['rent_total_10yr_eur'] ?? 0) }}</span></div>
                        <div class="data-cell"><span class="data-label">Valoare proprietate 2036</span><span class="data-value">â‚¬{{ number_format($bvr['buy_property_value_moderat_eur'] ?? 0) }}</span></div>
                        <div class="data-cell"><span class="data-label">PoziÈ›ie netÄƒ cumpÄƒrare</span><span class="data-value" style="color: var(--green);">+â‚¬{{ number_format($bvr['buy_net_position_eur'] ?? 0) }}</span></div>
                        <div class="data-cell highlight-yellow full-width"><span class="data-label">Avantaj cumpÄƒrare vs. chirie (10 ani)</span><span class="data-value" style="color: var(--green); font-size: 14px;">+â‚¬{{ number_format($bvr['advantage_buying_eur'] ?? 0) }}</span></div>
                    </div>
                    @if(!empty($bvr['note']))
                        <div class="warning-box">{{ $bvr['note'] }}</div>
                    @endif
                @endif

                {{-- Value-add improvements table --}}
                @if(!empty($section['value_add_improvements']))
                    <table class="improvement-table">
                        <thead>
                            <tr>
                                <th>ÃŽmbunÄƒtÄƒÈ›ire</th>
                                <th style="text-align:right">Cost</th>
                                <th style="text-align:right">+ Valoare</th>
                                <th style="text-align:center">ROI</th>
                                <th style="text-align:right">+ Chirie/lunÄƒ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section['value_add_improvements'] as $imp)
                                <tr>
                                    <td>
                                        {{ $imp['improvement'] ?? '' }}
                                        @if(!empty($imp['note']))
                                            <span class="cost-note">{{ $imp['note'] }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">â‚¬{{ number_format($imp['cost_eur'] ?? 0) }}</td>
                                    <td style="text-align:right;font-weight:600;">â‚¬{{ number_format($imp['value_increase_eur'] ?? 0) }}</td>
                                    <td style="text-align:center;" class="roi-value">+{{ $imp['roi_pct'] ?? 0 }}%</td>
                                    <td style="text-align:right;">+â‚¬{{ $imp['rental_increase_monthly_eur'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Legal checks --}}
                @if(!empty($section['legal_checks']))
                    @foreach($section['legal_checks'] as $lc)
                        <div class="legal-check-item">
                            <div class="legal-check-header">
                                <div class="legal-check-title">{{ $lc['check'] ?? '' }}</div>
                                <span class="priority-badge priority-{{ strtolower($lc['priority'] ?? 'mediu') }}">{{ $lc['priority'] ?? '' }}</span>
                            </div>
                            @if(!empty($lc['what_to_look_for']))
                                <div class="legal-check-body">{{ $lc['what_to_look_for'] }}</div>
                            @endif
                            @if(!empty($lc['how_to']))
                                <div class="legal-check-how">{{ $lc['how_to'] }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Structural assessment --}}
                @if(!empty($section['structural_assessment']))
                    @php $sa = $section['structural_assessment']; @endphp
                    <div class="structural-box">
                        <div class="structural-grid">
                            <div class="structural-metric">
                                <div class="structural-metric-value">{{ $sa['year_built'] ?? '-' }}</div>
                                <div class="structural-metric-label">An construcÈ›ie</div>
                            </div>
                            <div class="structural-metric">
                                <div class="structural-metric-value">{{ $sa['building_type'] ?? '-' }}</div>
                                <div class="structural-metric-label">Tip construcÈ›ie</div>
                            </div>
                            <div class="structural-metric">
                                <div class="structural-metric-value" style="color: {{ ($sa['seismic_class'] ?? '') === 'I' ? 'var(--red)' : 'var(--amber)' }};">{{ $sa['seismic_class'] ?? '-' }}</div>
                                <div class="structural-metric-label">ClasÄƒ seismicÄƒ</div>
                            </div>
                            <div class="structural-metric">
                                <div class="structural-metric-value">{{ $sa['energy_class'] ?? '-' }}</div>
                                <div class="structural-metric-label">ClasÄƒ energeticÄƒ</div>
                            </div>
                        </div>
                        @if(!empty($sa['on_red_list']) && $sa['on_red_list'])
                            <div style="background: var(--red-light); color: #991b1b; padding: 6px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; margin-bottom: 6px;">ClÄƒdirea se aflÄƒ pe LISTA ROÈ˜IE a imobilelor cu risc seismic</div>
                        @endif
                        @if(!empty($sa['known_issues']))
                            <ul class="item-list red-border">
                                @foreach($sa['known_issues'] as $ki)<li>{{ $ki }}</li>@endforeach
                            </ul>
                        @endif
                        @if(!empty($sa['recommended_inspection']))
                            <div class="warning-box">{{ $sa['recommended_inspection'] }}</div>
                        @endif
                    </div>
                @endif

                {{-- Inspection checklist --}}
                @if(!empty($section['inspection_checklist']))
                    <ul class="checklist">
                        @foreach($section['inspection_checklist'] as $ic)
                            <li>
                                <div class="checklist-checkbox"></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;">{{ $ic['item'] ?? '' }}</div>
                                    <div style="font-size:9px;color:#64748b;">{{ $ic['what_to_check'] ?? '' }}</div>
                                </div>
                                <span class="severity-badge severity-{{ strtolower($ic['severity'] ?? 'mediu') }}">{{ $ic['severity'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Fiscal info --}}
                @if(!empty($section['fiscal_info']))
                    @foreach($section['fiscal_info'] as $fi)
                        <div class="fiscal-card">
                            <div class="fiscal-card-topic">{{ $fi['topic'] ?? '' }}</div>
                            <div class="fiscal-card-detail">{{ $fi['detail'] ?? '' }}</div>
                            @if(!empty($fi['payer']))
                                <div class="fiscal-card-payer">PlÄƒtitor: {{ $fi['payer'] }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Transport --}}
                @if(!empty($section['transport']))
                    @php $tr = $section['transport']; @endphp
                    <div class="data-grid">
                        <div class="data-cell"><span class="data-label">Transport public</span><span class="data-value">{{ ($tr['public_transport'] ?? false) ? 'Da' : 'Nu' }}</span></div>
                        <div class="data-cell"><span class="data-label">Frecventa</span><span class="data-value">{{ $tr['frequency_minutes'] ?? '' }} min</span></div>
                        <div class="data-cell"><span class="data-label">Centrul cu busul</span><span class="data-value">{{ $tr['city_center_minutes'] ?? '' }} min</span></div>
                        <div class="data-cell"><span class="data-label">Centrul pe jos</span><span class="data-value">{{ $tr['city_center_minutes_walking'] ?? $tr['city_center_minutes'] ?? '' }} min</span></div>
                        <div class="data-cell"><span class="data-label">Parcare</span><span class="data-value">{{ $tr['parking_type'] ?? ($tr['parking_nearby'] ? 'Da' : 'Nu') }}</span></div>
                        <div class="data-cell"><span class="data-label">Bicicleta</span><span class="data-value">{{ ($tr['bike_friendly'] ?? false) ? 'Da â€” ' . ($tr['bike_lanes'] ?? '') : 'Nu' }}</span></div>
                    </div>
                    @if(!empty($tr['transport_lines']))
                        <div class="transport-lines">
                            @foreach($tr['transport_lines'] as $line)<span class="transport-line">{{ $line }}</span>@endforeach
                        </div>
                    @endif
                @endif

                {{-- Investment location data --}}
                @if(!empty($section['investment_location_data']))
                    @php $ild = $section['investment_location_data']; @endphp
                    <div class="data-grid">
                        <div class="data-cell highlight-yellow"><span class="data-label">Crestere 5 ani</span><span class="data-value">+{{ $ild['5yr_price_growth_pct'] ?? 0 }}%</span></div>
                        <div class="data-cell"><span class="data-label">Crestere anuala medie</span><span class="data-value">{{ $ild['avg_annual_growth_pct'] ?? 0 }}%</span></div>
                        <div class="data-cell"><span class="data-label">Zile pe piata (medie)</span><span class="data-value">{{ $ild['avg_days_on_market'] ?? '-' }} zile</span></div>
                        <div class="data-cell"><span class="data-label">Rata neocupare</span><span class="data-value">{{ $ild['vacancy_rate_pct'] ?? '-' }}%</span></div>
                        <div class="data-cell full-width"><span class="data-label">Cerere/oferta</span><span class="data-value">{{ $ild['demand_supply_ratio'] ?? '-' }}</span></div>
                    </div>
                    @if(!empty($ild['future_infrastructure']))
                        <div class="warning-box">{{ $ild['future_infrastructure'] }}</div>
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

                {{-- Red flags --}}
                @if(!empty($section['red_flags']))
                    @foreach($section['red_flags'] as $rf)
                        <div class="red-flag-card">
                            <div class="red-flag-signal">Semnal: {{ $rf['signal'] ?? '' }}</div>
                            <div class="red-flag-action">Actiune: {{ $rf['action'] ?? '' }}</div>
                        </div>
                    @endforeach
                @endif

                {{-- Verdict (summary page) --}}
                @if(!empty($section['verdict']))
                    <div class="final-verdict-card">
                        <div class="final-score">{{ $section['overall_score'] ?? '' }}<span>/10</span></div>
                            <div class="final-verdict-label">Scor general</div>
                        @php
                            $recUpper = strtoupper($section['verdict'] ?? '');
                            $recClass = match(true) {
                                str_contains($recUpper, 'EVITÄ‚') => 'avoid',
                                str_contains($recUpper, 'CUMPÄ‚RÄ‚') || str_contains($recUpper, 'ÃŽNCHIRIAZÄ‚') => 'buy',
                                default => 'negotiate',
                            };
                        @endphp
                        <div class="final-rec {{ $recClass }}">{{ $section['verdict'] }}</div>
                    </div>
                    @if(!empty($section['score_breakdown']))
                        <div class="score-breakdown">
                            @foreach($section['score_breakdown'] as $sb)
                                <div class="score-dim">
                                    <div class="score-dim-value" style="color: {{ gradeColor($sb['score'] ?? 0) }};">{{ $sb['score'] ?? '' }}</div>
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

    function gradeColor(v, invert) {
        if (invert) v = 10 - v;
        if (v >= 8) return '#00A556';
        if (v >= 6) return '#d97706';
        return '#e05252';
    }

    @if($lineChart)
    (function() {
        var ctx = document.getElementById('lineChart');
        if (!ctx) return;
        var labels = @json($lineChart['data']['labels'] ?? []);
        var values = @json($lineChart['data']['values'] ?? []);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: '#7380d9',
                    backgroundColor: 'rgba(0, 115, 240, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#7380d9',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
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
                        min: Math.min.apply(null, values) * 0.92,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function(v) { return 'â‚¬' + v; },
                            font: { size: 10 }
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
                id: 'lineValueLabels',
                afterDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '700 9px "Inter", sans-serif';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'bottom';
                    ctx2.fillStyle = '#34306a';
                    var meta = chart.getDatasetMeta(0);
                    meta.data.forEach(function(point, i) {
                        ctx2.fillText('â‚¬' + values[i], point.x, point.y - 6);
                    });
                    ctx2.restore();
                }
            }]
        });
    })();
    @endif

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
                            callback: function(v) { return 'â‚¬' + v.toLocaleString(); },
                            font: { size: 10 }
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
                    ctx2.font = '700 9px "Inter", sans-serif';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'bottom';
                    chart.data.datasets[0].data.forEach(function(val, i) {
                        var meta = chart.getDatasetMeta(0);
                        var bar = meta.data[i];
                        ctx2.fillStyle = '#34306a';
                        ctx2.fillText('â‚¬' + val.toLocaleString(), bar.x, bar.y - 4);
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
        var colors = ['#34306a', '#4e59b7', '#7380d9', '#64748b', '#e05252', '#5c67c7', '#8b91d9', '#a7afea'];
        var total = values.reduce(function(a, b) { return a + b; }, 0);

        function compactLabel(label) {
            var map = {
                'PreÈ› proprietate': 'PreÈ› proprietate',
                'Taxe notariale + autentificare': 'Taxe notariale',
                'TaxÄƒ de intabulare CF': 'Intabulare CF',
                'Comision agenÈ›ie imobiliarÄƒ': 'Comision agenÈ›ie',
                'Evaluare bancarÄƒ (dacÄƒ credit)': 'Evaluare bancarÄƒ',
                'Renovare estimatÄƒ': 'Renovare',
                'RezervÄƒ neprevÄƒzute': 'RezervÄƒ'
            };
            return map[label] || label;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, values.length),
                    borderWidth: 0,
                    borderColor: '#fff',
                    spacing: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                layout: { padding: 0 },
                cutout: '68%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        align: 'center',
                        maxWidth: 110,
                        labels: {
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 5,
                            font: { size: 8 },
                            generateLabels: function(chart) {
                                var data = chart.data;
                                return data.labels.map(function(label, i) {
                                    return {
                                        text: compactLabel(label) + '  â‚¬' + data.datasets[0].data[i].toLocaleString(),
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
                    var ctx2 = chart.ctx;
                    ctx2.save();
                    ctx2.font = '800 12px "Inter", sans-serif';
                    ctx2.fillStyle = '#34306a';
                    ctx2.textAlign = 'center';
                    ctx2.textBaseline = 'middle';
                    var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                    ctx2.fillText('â‚¬' + total.toLocaleString(), centerX, centerY - 2);
                    ctx2.font = '500 7px "Inter", sans-serif';
                    ctx2.fillStyle = '#94a3b8';
                    ctx2.fillText('cost total', centerX, centerY + 10);
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
        var labels = @json($hBarChart['data']['labels'] ?? []);
        var values = @json($hBarChart['data']['values'] ?? []);
        var colors = values.map(function(v) {
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
                            color: '#64748b'
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

