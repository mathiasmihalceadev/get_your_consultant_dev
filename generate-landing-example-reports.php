<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Support\ReportPdfFooter;
use Spatie\LaravelPdf\Facades\Pdf;

/**
 * @param mixed $value
 * @param array<string, string> $replacements
 */
function replaceStringsRecursively(mixed $value, array $replacements): mixed
{
    if (is_array($value)) {
        $result = [];

        foreach ($value as $key => $nestedValue) {
            $result[$key] = replaceStringsRecursively($nestedValue, $replacements);
        }

        return $result;
    }

    if (! is_string($value)) {
        return $value;
    }

    return strtr($value, $replacements);
}

/**
 * @param array<int, array<string, mixed>> $charts
 * @param array<string, mixed> $overrides
 */
function updateChartById(array &$charts, string $chartId, array $overrides): void
{
    foreach ($charts as &$chart) {
        if (($chart['id'] ?? null) !== $chartId) {
            continue;
        }

        $chart = array_replace_recursive($chart, $overrides);
        return;
    }
}

$variants = [
    'en' => [
        'source' => storage_path('app/buying_eng.json'),
        'target_json' => storage_path('app/landing_example_buying_en.json'),
        'target_pdf' => public_path('images/report-example-en.pdf'),
        'locale' => 'en',
        'replacements' => [
            'Cluj-Napoca' => 'Northbridge',
            'cluj-napoca' => 'northbridge',
            'Cluj' => 'Northbridge',
            'Andrei Mureșanu' => 'Cedar Quarter',
            'andrei-muresanu' => 'cedar-quarter',
            'Gh. Dima' => 'Aurora Lane',
            'CJ8812047' => 'DEMO-024',
            'imobiliare.ro' => 'demo-listings.example',
            'ANCPI' => 'Demo Market Registry',
        ],
        'hero' => [
            'property_name' => '3-room apartment — Cedar Quarter, Northbridge',
            'property_type' => '3-room apartment',
            'city' => 'Northbridge',
            'neighborhood' => 'Cedar Quarter',
            'address' => '18 Aurora Lane, Cedar Quarter, Northbridge',
            'size_sqm' => 82,
            'size_usable_sqm' => 75,
            'floor' => '4',
            'total_floors' => 6,
            'year_built' => '2014',
            'energy_class' => 'B',
            'condition' => 'ready to move in',
            'asking_price' => 162000,
            'price_per_sqm' => 1976,
            'fair_value_estimate' => 156500,
            'price_deviation_pct' => 3.5,
            'cadastral_area_sqm' => 82,
            'land_share_sqm' => 21,
            'tagline' => 'Fictional showcase property for the public demo report',
        ],
        'kpis' => [
            [
                'value' => 8.1,
                'note' => 'The list price sits close to the demo fair value estimate.',
            ],
            [
                'value' => 8.7,
                'note' => 'Balanced amenities and stable buyer demand in this fictional district.',
            ],
            [
                'value' => 7.4,
                'note' => 'Healthy resale outlook for a synthetic mid-market scenario.',
            ],
            [
                'value' => 1.8,
                'note' => 'No demo legal issues flagged in the fictional paperwork review.',
            ],
            [
                'value' => 2.1,
                'note' => 'Modern structure and low structural concern in the sample case.',
            ],
            [
                'value' => 8.3,
            ],
        ],
        'chart_overrides' => [
            'score_radar' => [
                'data' => [
                    'values' => [8.1, 8.7, 7.4, 9.2, 8.6, 8.1, 8.4],
                ],
            ],
            'total_acquisition_cost' => [
                'data' => [
                    'segments' => [
                        ['label' => 'Price', 'value' => 162000, 'unit' => 'EUR'],
                        ['label' => 'Notary', 'value' => 3240, 'unit' => 'EUR'],
                        ['label' => 'Registry', 'value' => 320, 'unit' => 'EUR'],
                        ['label' => 'Agency', 'value' => 2430, 'unit' => 'EUR'],
                        ['label' => 'Bank', 'value' => 450, 'unit' => 'EUR'],
                        ['label' => 'Renovation', 'value' => 6500, 'unit' => 'EUR'],
                        ['label' => 'Reserve', 'value' => 4200, 'unit' => 'EUR'],
                    ],
                    'total_value' => 179140,
                ],
            ],
            'price_trend' => [
                'title' => 'Price per sqm trend in Cedar Quarter (last 5 years)',
                'data' => [
                    'values' => [1560, 1715, 1860, 1975, 2050, 2018],
                    'note' => 'Illustrative demo dataset prepared for the public sample report.',
                ],
            ],
            'mortgage_simulation' => [
                'title' => 'Mortgage simulation (at 162,000 EUR)',
                'data' => [
                    'scenarios' => [
                        [
                            'label' => '15% down payment',
                            'down_payment_eur' => 24300,
                            'loan_eur' => 137700,
                            'monthly_rate_eur' => 905,
                            'total_cost_eur' => 295800,
                            'period_years' => 25,
                        ],
                        [
                            'label' => '25% down payment',
                            'down_payment_eur' => 40500,
                            'loan_eur' => 121500,
                            'monthly_rate_eur' => 798,
                            'total_cost_eur' => 279900,
                            'period_years' => 25,
                        ],
                        [
                            'label' => '35% down payment',
                            'down_payment_eur' => 56700,
                            'loan_eur' => 105300,
                            'monthly_rate_eur' => 691,
                            'total_cost_eur' => 264000,
                            'period_years' => 25,
                        ],
                    ],
                    'interest_rate_pct' => 6.4,
                ],
            ],
            'neighborhood_scores' => [
                'data' => [
                    'values' => [8.4, 7.8, 8.9, 8.1, 8.5, 7.6, 7.9],
                ],
            ],
            'ten_year_projection' => [
                'data' => [
                    'scenario_optimist' => [
                        162000, 169290, 176908, 184869, 193188, 201881, 210966,
                        220460, 230381, 240748, 251582,
                    ],
                    'scenario_moderat' => [
                        162000, 166860, 171866, 177022, 182333, 187803, 193437,
                        199240, 205217, 211373, 217714,
                    ],
                ],
            ],
        ],
        'source_url' => 'https://demo-listings.example/en/properties/northbridge/cedar-quarter/apartment-demo-024',
        'property_id' => 'DEMO-024-EN',
    ],
    'ro' => [
        'source' => storage_path('app/buying_ro.json'),
        'target_json' => storage_path('app/landing_example_buying_ro.json'),
        'target_pdf' => public_path('images/report-example-ro.pdf'),
        'locale' => 'ro',
        'replacements' => [
            'Cluj-Napoca' => 'Northbridge',
            'cluj-napoca' => 'northbridge',
            'Cluj' => 'Northbridge',
            'Andrei Mureșanu' => 'Cartier Cedar',
            'andrei-muresanu' => 'cartier-cedar',
            'Gh. Dima' => 'Aurora',
            'CJ8812047' => 'DEMO-024',
            'imobiliare.ro' => 'demo-listings.example',
            'ANCPI' => 'Registrul Demo de Piață',
        ],
        'hero' => [
            'property_name' => 'Apartament 3 camere — Cartier Cedar, Northbridge',
            'property_type' => 'apartament 3 camere',
            'city' => 'Northbridge',
            'neighborhood' => 'Cartier Cedar',
            'address' => 'Str. Aurora nr. 18, Cartier Cedar, Northbridge',
            'size_sqm' => 82,
            'size_usable_sqm' => 75,
            'floor' => '4',
            'total_floors' => 6,
            'year_built' => '2014',
            'energy_class' => 'B',
            'condition' => 'gata de mutare',
            'asking_price' => 162000,
            'price_per_sqm' => 1976,
            'fair_value_estimate' => 156500,
            'price_deviation_pct' => 3.5,
            'cadastral_area_sqm' => 82,
            'land_share_sqm' => 21,
            'tagline' => 'Proprietate fictivă folosită ca demo public pentru raportul de prezentare',
        ],
        'kpis' => [
            [
                'value' => 8.1,
                'note' => 'Prețul listat este apropiat de valoarea corectă estimată pentru scenariul demo.',
            ],
            [
                'value' => 8.7,
                'note' => 'Facilități echilibrate și cerere stabilă în cartierul fictiv analizat.',
            ],
            [
                'value' => 7.4,
                'note' => 'Profil bun de revânzare într-un scenariu demonstrativ de piață medie.',
            ],
            [
                'value' => 1.8,
                'note' => 'Nu au fost identificate probleme juridice în setul demo de documente.',
            ],
            [
                'value' => 2.1,
                'note' => 'Structură modernă și risc tehnic redus în exemplul sintetic.',
            ],
            [
                'value' => 8.3,
            ],
        ],
        'chart_overrides' => [
            'score_radar' => [
                'data' => [
                    'values' => [8.1, 8.7, 7.4, 9.2, 8.6, 8.1, 8.4],
                ],
            ],
            'total_acquisition_cost' => [
                'data' => [
                    'segments' => [
                        ['label' => 'Preț', 'value' => 162000, 'unit' => 'EUR'],
                        ['label' => 'Notar', 'value' => 3240, 'unit' => 'EUR'],
                        ['label' => 'Carte', 'value' => 320, 'unit' => 'EUR'],
                        ['label' => 'Comision', 'value' => 2430, 'unit' => 'EUR'],
                        ['label' => 'Bancă', 'value' => 450, 'unit' => 'EUR'],
                        ['label' => 'Renovare', 'value' => 6500, 'unit' => 'EUR'],
                        ['label' => 'Rezervă', 'value' => 4200, 'unit' => 'EUR'],
                    ],
                    'total_value' => 179140,
                ],
            ],
            'price_trend' => [
                'title' => 'Evoluția prețului/mp în Cartier Cedar (ultimii 5 ani)',
                'data' => [
                    'values' => [1560, 1715, 1860, 1975, 2050, 2018],
                    'note' => 'Set demonstrativ de date pregătit pentru raportul public de prezentare.',
                ],
            ],
            'mortgage_simulation' => [
                'title' => 'Simulare credit ipotecar (la 162.000 EUR)',
                'data' => [
                    'scenarios' => [
                        [
                            'label' => 'Avans 15%',
                            'down_payment_eur' => 24300,
                            'loan_eur' => 137700,
                            'monthly_rate_eur' => 905,
                            'total_cost_eur' => 295800,
                            'period_years' => 25,
                        ],
                        [
                            'label' => 'Avans 25%',
                            'down_payment_eur' => 40500,
                            'loan_eur' => 121500,
                            'monthly_rate_eur' => 798,
                            'total_cost_eur' => 279900,
                            'period_years' => 25,
                        ],
                        [
                            'label' => 'Avans 35%',
                            'down_payment_eur' => 56700,
                            'loan_eur' => 105300,
                            'monthly_rate_eur' => 691,
                            'total_cost_eur' => 264000,
                            'period_years' => 25,
                        ],
                    ],
                    'interest_rate_pct' => 6.4,
                ],
            ],
            'neighborhood_scores' => [
                'data' => [
                    'values' => [8.4, 7.8, 8.9, 8.1, 8.5, 7.6, 7.9],
                ],
            ],
            'ten_year_projection' => [
                'data' => [
                    'scenario_optimist' => [
                        162000, 169290, 176908, 184869, 193188, 201881, 210966,
                        220460, 230381, 240748, 251582,
                    ],
                    'scenario_moderat' => [
                        162000, 166860, 171866, 177022, 182333, 187803, 193437,
                        199240, 205217, 211373, 217714,
                    ],
                ],
            ],
        ],
        'source_url' => 'https://demo-listings.example/ro/proprietati/northbridge/cartier-cedar/apartament-demo-024',
        'property_id' => 'DEMO-024-RO',
    ],
];

$footerHtml = ReportPdfFooter::render(now());

foreach ($variants as $variant) {
    $raw = file_get_contents($variant['source']);

    if ($raw === false) {
        throw new RuntimeException('Could not read source JSON: ' . $variant['source']);
    }

    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    $data = replaceStringsRecursively($data, $variant['replacements']);

    $data['report_meta']['generated_at'] = now()->toIso8601String();
    $data['report_meta']['source_url'] = $variant['source_url'];
    $data['report_meta']['property_id'] = $variant['property_id'];
    $data['report_meta']['report_version'] = '2.0-demo';
    $data['report_meta']['analyst_confidence'] = 'demo';

    $data['page_one']['hero'] = array_replace(
        $data['page_one']['hero'],
        $variant['hero'],
    );

    foreach ($variant['kpis'] as $index => $kpiOverrides) {
        if (! isset($data['page_one']['kpis'][$index])) {
            continue;
        }

        $data['page_one']['kpis'][$index] = array_replace(
            $data['page_one']['kpis'][$index],
            $kpiOverrides,
        );
    }

    foreach ($variant['chart_overrides'] as $chartId => $chartOverrides) {
        updateChartById($data['page_one']['charts'], $chartId, $chartOverrides);
    }

    file_put_contents(
        $variant['target_json'],
        json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) . PHP_EOL,
    );

    $pdfDirectory = dirname($variant['target_pdf']);

    if (! is_dir($pdfDirectory)) {
        mkdir($pdfDirectory, 0755, true);
    }

    Pdf::view('reports.template-buying', [
        'data' => $data,
        'locale' => $variant['locale'],
    ])
        ->format('a4')
        ->withBrowsershot(function ($browsershot) use ($footerHtml) {
            $browsershot
                ->waitUntilNetworkIdle()
                ->showBrowserHeaderAndFooter()
                ->headerHtml('<div></div>')
                ->footerHtml($footerHtml);
        })
        ->save($variant['target_pdf']);

    echo 'JSON saved to: ' . $variant['target_json'] . PHP_EOL;
    echo 'PDF saved to: ' . $variant['target_pdf'] . PHP_EOL;
}