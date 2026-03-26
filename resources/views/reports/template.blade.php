<!DOCTYPE html>
<html lang="{{ $report->locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $trans['pdf_report_title'] ?? 'Property Report' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 40px 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #1e1b4b;
            background: #fff;
            font-size: 13px;
            line-height: 1.6;
        }
        .page { padding: 0 40px; max-width: 800px; margin: 0 auto; }
        .header {
            padding-bottom: 20px;
            margin-bottom: 28px;
            border-bottom: 3px solid #303048;
        }
        .header-type {
            display: inline-block;
            background: #303048;
            color: #fff;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
            margin-top: 12px;
            color: #303048;
        }
        .header-meta {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .header-meta a { color: #0073F0; word-break: break-all; text-decoration: none; }
        .section {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #303048;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e0f0;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .grid { display: flex; flex-wrap: wrap; gap: 0; }
        .grid-item {
            width: 50%;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f0f9;
        }
        .grid-item-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 500;
        }
        .grid-item-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e1b4b;
            margin-top: 1px;
        }
        .score-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
        }
        .score-green { background: #16a34a; }
        .score-yellow { background: #ca8a04; }
        .score-red { background: #dc2626; }
        .badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-buy, .badge-rent, .badge-recommended { background: #dcfce7; color: #166534; }
        .badge-negotiate, .badge-acceptable { background: #fef9c3; color: #854d0e; }
        .badge-avoid, .badge-risky { background: #fee2e2; color: #991b1b; }
        .amenity-list { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 6px; }
        .amenity-item {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        .amenity-yes { background: #dcfce7; color: #166534; }
        .amenity-no { background: #fee2e2; color: #991b1b; }
        .risk-list { list-style: none; padding: 0; }
        .risk-list li {
            margin-bottom: 8px;
            font-size: 13px;
            padding: 8px 12px;
            background: #f8f7fd;
            border-radius: 6px;
            border-left: 3px solid #303048;
        }
        .risk-list li strong { color: #303048; }
        .factors-list { margin: 6px 0; padding-left: 18px; }
        .factors-list li { font-size: 12px; margin-bottom: 3px; color: #334155; }
        .final-score-section {
            background: linear-gradient(135deg, #f8f7fd 0%, #eeecf9 100%);
            border: 2px solid #303048;
            border-radius: 10px;
            padding: 28px;
            text-align: center;
        }
        .final-score-number {
            font-size: 52px;
            font-weight: 800;
            color: #303048;
        }
        .final-score-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            margin-top: 2px;
        }
        .verdict-text { font-size: 20px; font-weight: 700; margin-top: 10px; }
        .suitable-for {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 2px solid #e2e0f0;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">
    @php
        $isRental = in_array($report->report_type, ['rental_living', 'rental_business']);
        $isBuying = in_array($report->report_type, ['buying_living', 'buying_business']);
        $isBusiness = in_array($report->report_type, ['rental_business', 'buying_business']);
        $isLiving = in_array($report->report_type, ['rental_living', 'buying_living']);
    @endphp

    {{-- HEADER --}}
    <div class="header">
        <span class="header-type">
            {{ $trans["type_{$report->report_type}"] ?? $report->report_type }}
        </span>
        <h1>{{ $trans['pdf_report_title'] ?? 'Property Analysis Report' }}</h1>
        <div class="header-meta">
            <div>{{ $trans['pdf_generated'] ?? 'Generated' }}: {{ now()->format('F j, Y') }}</div>
            <div>{{ $trans['pdf_url'] ?? 'URL' }}: <a href="{{ $report->url }}">{{ $report->url }}</a></div>
        </div>
    </div>

    {{-- PROPERTY SUMMARY --}}
    <div class="section">
        <div class="section-title">{{ $trans['pdf_property_summary'] ?? 'Property Summary' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_property_type'] ?? 'Property Type' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['property_type'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_city'] ?? 'City' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['city'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_area'] ?? 'Area' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['area'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_size'] ?? 'Size' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['size_sqm'] ?? 'N/A' }} m²</div>
            </div>
            @if($isLiving)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_rooms'] ?? 'Rooms' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['rooms'] ?? 'N/A' }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_floor'] ?? 'Floor' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['floor'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_year_built'] ?? 'Year Built' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['year_built'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_condition'] ?? 'Condition' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['condition'] ?? 'N/A' }}</div>
            </div>
            @if($isBuying && $isLiving)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_asking_price'] ?? 'Asking Price' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_price'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_price_per_sqm'] ?? 'Price/m²' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['price_per_sqm'] ?? 0) }}</div>
            </div>
            @elseif($isRental)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_monthly_rent'] ?? 'Monthly Rent' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_rent_monthly'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_price_per_sqm_monthly'] ?? 'Price/m²/month' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['price_per_sqm_monthly'] ?? 0, 2) }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_asking_price'] ?? 'Asking Price' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_price'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_monthly_rent'] ?? 'Monthly Rent' }}</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_rent_monthly'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_zoning'] ?? 'Zoning' }}</div>
                <div class="grid-item-value">{{ $data['property_summary']['zoning'] ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- PRICE / RENT EVALUATION --}}
    <div class="section">
        @if($isRental)
        <div class="section-title">{{ $trans['pdf_rent_evaluation'] ?? 'Rent Evaluation' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_estimated_fair_rent'] ?? 'Estimated Fair Rent' }}</div>
                <div class="grid-item-value">€{{ number_format($data['rent_evaluation']['estimated_fair_rent'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_rent_positioning'] ?? 'Rent Positioning' }}</div>
                <div class="grid-item-value">{{ str_replace('_', ' ', ucfirst($data['rent_evaluation']['rent_positioning'] ?? 'N/A')) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_market_difference'] ?? 'Market Difference' }}</div>
                <div class="grid-item-value">{{ $data['rent_evaluation']['market_difference_percent'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_rent_score'] ?? 'Rent Score' }}</div>
                <div class="grid-item-value">
                    @php $rs = $data['rent_evaluation']['rent_score'] ?? 0; @endphp
                    <span class="score-box {{ $rs >= 7 ? 'score-green' : ($rs >= 4 ? 'score-yellow' : 'score-red') }}">{{ $rs }}</span>
                </div>
            </div>
        </div>
        @if(!empty($data['rent_evaluation']['value_increasing_factors']))
        <div style="margin-top:10px"><strong>{{ $trans['pdf_value_increasing'] ?? 'Value Increasing' }}:</strong>
            <ul class="factors-list">
                @foreach($data['rent_evaluation']['value_increasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(!empty($data['rent_evaluation']['value_decreasing_factors']))
        <div><strong>{{ $trans['pdf_value_decreasing'] ?? 'Value Decreasing' }}:</strong>
            <ul class="factors-list">
                @foreach($data['rent_evaluation']['value_decreasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @else
        <div class="section-title">{{ $trans['pdf_price_evaluation'] ?? 'Price Evaluation' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_estimated_market_value'] ?? 'Estimated Market Value' }}</div>
                <div class="grid-item-value">€{{ number_format($data['price_evaluation']['estimated_market_value'] ?? 0) }}</div>
            </div>
            @if($isBusiness)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_estimated_fair_rent'] ?? 'Estimated Fair Rent' }}</div>
                <div class="grid-item-value">€{{ number_format($data['price_evaluation']['estimated_fair_rent'] ?? 0) }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_price_positioning'] ?? 'Price Positioning' }}</div>
                <div class="grid-item-value">{{ str_replace('_', ' ', ucfirst($data['price_evaluation']['price_positioning'] ?? 'N/A')) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_market_difference'] ?? 'Market Difference' }}</div>
                <div class="grid-item-value">{{ $data['price_evaluation']['market_difference_percent'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_price_score'] ?? 'Price Score' }}</div>
                <div class="grid-item-value">
                    @php $ps = $data['price_evaluation']['price_score'] ?? 0; @endphp
                    <span class="score-box {{ $ps >= 7 ? 'score-green' : ($ps >= 4 ? 'score-yellow' : 'score-red') }}">{{ $ps }}</span>
                </div>
            </div>
        </div>
        @if(!empty($data['price_evaluation']['value_increasing_factors']))
        <div style="margin-top:10px"><strong>{{ $trans['pdf_value_increasing'] ?? 'Value Increasing' }}:</strong>
            <ul class="factors-list">
                @foreach($data['price_evaluation']['value_increasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(!empty($data['price_evaluation']['value_decreasing_factors']))
        <div><strong>{{ $trans['pdf_value_decreasing'] ?? 'Value Decreasing' }}:</strong>
            <ul class="factors-list">
                @foreach($data['price_evaluation']['value_decreasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif
    </div>

    {{-- AREA ANALYSIS --}}
    <div class="section">
        <div class="section-title">{{ $trans['pdf_area_analysis'] ?? 'Area Analysis' }}</div>
        <div class="grid">
            @if($isBusiness)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_foot_traffic'] ?? 'Foot Traffic' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['foot_traffic'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_visibility'] ?? 'Visibility' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['visibility'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_accessibility'] ?? 'Accessibility' }}</div>
                <div class="grid-item-value">{{ $data['area_analysis']['accessibility'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_competition'] ?? 'Competition' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['competition_density'] ?? 'N/A') }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_suitable_for_living'] ?? 'Suitable for Living' }}</div>
                <div class="grid-item-value">{{ ($data['area_analysis']['suitable_for_living'] ?? false) ? ($trans['pdf_yes'] ?? 'Yes') : ($trans['pdf_no'] ?? 'No') }}</div>
            </div>
            @if($report->report_type === 'buying_living')
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_suitable_for_investment'] ?? 'Suitable for Investment' }}</div>
                <div class="grid-item-value">{{ ($data['area_analysis']['suitable_for_investment'] ?? false) ? ($trans['pdf_yes'] ?? 'Yes') : ($trans['pdf_no'] ?? 'No') }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_safety'] ?? 'Safety' }}</div>
                <div class="grid-item-value">{{ $data['area_analysis']['safety'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_quietness'] ?? 'Quietness' }}</div>
                <div class="grid-item-value">{{ $data['area_analysis']['quietness'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_pollution'] ?? 'Pollution' }}</div>
                <div class="grid-item-value">{{ $data['area_analysis']['pollution'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_traffic'] ?? 'Traffic' }}</div>
                <div class="grid-item-value">{{ $data['area_analysis']['traffic'] ?? 'N/A' }}/10</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_area_trend'] ?? 'Area Trend' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['area_trend'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_area_score'] ?? 'Area Score' }}</div>
                <div class="grid-item-value">
                    @php $as = $data['area_analysis']['area_score'] ?? 0; @endphp
                    <span class="score-box {{ $as >= 7 ? 'score-green' : ($as >= 4 ? 'score-yellow' : 'score-red') }}">{{ $as }}</span>
                </div>
            </div>
        </div>
        @if($isLiving && !empty($data['area_analysis']['amenities']))
        <div style="margin-top:12px">
            <strong style="font-size:12px">{{ $trans['pdf_amenities'] ?? 'Amenities' }}:</strong>
            <ul class="amenity-list" style="margin-top:6px">
                @foreach($data['area_analysis']['amenities'] as $name => $available)
                <li class="amenity-item {{ $available ? 'amenity-yes' : 'amenity-no' }}">
                    {{ ucfirst(str_replace('_', ' ', $name)) }}: {{ $available ? '✓' : '✗' }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- PARKING --}}
    <div class="section">
        <div class="section-title">{{ $trans['pdf_parking'] ?? 'Parking' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_parking_available'] ?? 'Parking Available' }}</div>
                <div class="grid-item-value">{{ ($data['parking']['exists'] ?? false) ? ($trans['pdf_yes'] ?? 'Yes') : ($trans['pdf_no'] ?? 'No') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_type'] ?? 'Type' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['parking']['type'] ?? 'N/A') }}</div>
            </div>
            @if($isBuying && $isLiving)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_value_impact'] ?? 'Value Impact' }}</div>
                <div class="grid-item-value">{{ $data['parking']['value_impact'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_est_cost_if_missing'] ?? 'Est. Cost if Missing' }}</div>
                <div class="grid-item-value">€{{ number_format($data['parking']['estimated_cost_if_missing'] ?? 0) }}</div>
            </div>
            @elseif($isRental)
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_included_in_rent'] ?? 'Included in Rent' }}</div>
                <div class="grid-item-value">{{ ($data['parking']['included_in_rent'] ?? false) ? ($trans['pdf_yes'] ?? 'Yes') : ($trans['pdf_no'] ?? 'No') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_est_extra_cost'] ?? 'Est. Extra Cost' }}</div>
                <div class="grid-item-value">€{{ number_format($data['parking']['estimated_extra_cost'] ?? 0) }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_spaces'] ?? 'Spaces' }}</div>
                <div class="grid-item-value">{{ $data['parking']['spaces'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_value_impact'] ?? 'Value Impact' }}</div>
                <div class="grid-item-value">{{ $data['parking']['value_impact'] ?? 'N/A' }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_parking_score'] ?? 'Parking Score' }}</div>
                <div class="grid-item-value">
                    @php $pks = $data['parking']['parking_score'] ?? 0; @endphp
                    <span class="score-box {{ $pks >= 7 ? 'score-green' : ($pks >= 4 ? 'score-yellow' : 'score-red') }}">{{ $pks }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- LIVABILITY (rental_living) / BUSINESS SUITABILITY (rental_business) / INVESTMENT (buying) --}}
    @if($report->report_type === 'rental_living')
    <div class="section">
        <div class="section-title">{{ $trans['pdf_livability'] ?? 'Livability' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_natural_light'] ?? 'Natural Light' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['natural_light'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_noise_level'] ?? 'Noise Level' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['noise_level'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_ventilation'] ?? 'Ventilation' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['ventilation'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_storage'] ?? 'Storage' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['storage'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_ideal_tenant'] ?? 'Ideal Tenant' }}</div>
                <div class="grid-item-value">{{ $data['livability']['ideal_tenant_profile'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_livability_score'] ?? 'Livability Score' }}</div>
                <div class="grid-item-value">
                    @php $ls = $data['livability']['livability_score'] ?? 0; @endphp
                    <span class="score-box {{ $ls >= 7 ? 'score-green' : ($ls >= 4 ? 'score-yellow' : 'score-red') }}">{{ $ls }}</span>
                </div>
            </div>
        </div>
    </div>
    @elseif($report->report_type === 'rental_business')
    <div class="section">
        <div class="section-title">{{ $trans['pdf_business_suitability'] ?? 'Business Suitability' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_ideal_business_type'] ?? 'Ideal Business Type' }}</div>
                <div class="grid-item-value">{{ is_array($data['business_suitability']['ideal_business_type'] ?? null) ? implode(', ', $data['business_suitability']['ideal_business_type']) : ($data['business_suitability']['ideal_business_type'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_layout_flexibility'] ?? 'Layout Flexibility' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['business_suitability']['layout_flexibility'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_client_accessibility'] ?? 'Client Accessibility' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['business_suitability']['client_accessibility'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_signage_potential'] ?? 'Signage Potential' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['business_suitability']['signage_potential'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_suitability_score'] ?? 'Suitability Score' }}</div>
                <div class="grid-item-value">
                    @php $ss = $data['business_suitability']['suitability_score'] ?? 0; @endphp
                    <span class="score-box {{ $ss >= 7 ? 'score-green' : ($ss >= 4 ? 'score-yellow' : 'score-red') }}">{{ $ss }}</span>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="section">
        <div class="section-title">{{ $trans['pdf_investment_analysis'] ?? 'Investment Analysis' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_est_monthly_rent'] ?? 'Est. Monthly Rent' }}</div>
                <div class="grid-item-value">€{{ number_format($data['investment']['estimated_monthly_rent'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_demand_level'] ?? 'Demand Level' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['investment']['demand_level'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_gross_yield'] ?? 'Gross Yield' }}</div>
                <div class="grid-item-value">{{ $data['investment']['gross_yield'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_net_yield'] ?? 'Net Yield' }}</div>
                <div class="grid-item-value">{{ $data['investment']['net_yield'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_occupancy_rate'] ?? 'Occupancy Rate' }}</div>
                <div class="grid-item-value">{{ $data['investment']['occupancy_rate'] ?? 0 }}%</div>
            </div>
            @if($report->report_type === 'buying_living')
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_ideal_tenant'] ?? 'Ideal Tenant' }}</div>
                <div class="grid-item-value">{{ $data['investment']['ideal_tenant'] ?? 'N/A' }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_ideal_business_type'] ?? 'Ideal Business Type' }}</div>
                <div class="grid-item-value">{{ is_array($data['investment']['ideal_business_type'] ?? null) ? implode(', ', $data['investment']['ideal_business_type']) : ($data['investment']['ideal_business_type'] ?? 'N/A') }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_investment_score'] ?? 'Investment Score' }}</div>
                <div class="grid-item-value">
                    @php $is = $data['investment']['investment_score'] ?? 0; @endphp
                    <span class="score-box {{ $is >= 7 ? 'score-green' : ($is >= 4 ? 'score-yellow' : 'score-red') }}">{{ $is }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- AIR QUALITY --}}
    <div class="section">
        <div class="section-title">{{ $trans['pdf_air_quality'] ?? 'Air Quality' }}</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_quality'] ?? 'Quality' }}</div>
                <div class="grid-item-value">{{ ucfirst($data['air_quality']['quality'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">{{ $trans['pdf_impact'] ?? 'Impact' }}</div>
                <div class="grid-item-value">{{ $data['air_quality'][($isBusiness ? 'impact_on_business' : 'impact_on_living')] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- RISK ANALYSIS --}}
    <div class="section">
        <div class="section-title">{{ $trans['pdf_risk_analysis'] ?? 'Risk Analysis' }}</div>
        @if($isRental)
        <ul class="risk-list">
            <li><strong>{{ $trans['pdf_landlord_risk'] ?? 'Landlord Risk' }}:</strong> {{ $data['risk_analysis']['landlord_risk'] ?? 'N/A' }}</li>
            <li><strong>{{ $trans['pdf_legal_risk'] ?? 'Legal Risk' }}:</strong> {{ $data['risk_analysis']['legal_risk'] ?? 'N/A' }}</li>
            @if($isBusiness)
            <li><strong>{{ $trans['pdf_zoning_risk'] ?? 'Zoning Risk' }}:</strong> {{ $data['risk_analysis']['zoning_risk'] ?? 'N/A' }}</li>
            @endif
        </ul>
        @else
        <ul class="risk-list">
            <li><strong>{{ $trans['pdf_construction_risk'] ?? 'Construction Risk' }}:</strong> {{ $data['risk_analysis']['construction_risk'] ?? 'N/A' }}</li>
            <li><strong>{{ $trans['pdf_legal_risk'] ?? 'Legal Risk' }}:</strong> {{ $data['risk_analysis']['legal_risk'] ?? 'N/A' }}</li>
            @if($isBusiness)
            <li><strong>{{ $trans['pdf_zoning_risk'] ?? 'Zoning Risk' }}:</strong> {{ $data['risk_analysis']['zoning_risk'] ?? 'N/A' }}</li>
            @endif
        </ul>
        @endif
        @if(!empty($data['risk_analysis']['possible_hidden_costs']))
        <div style="margin-top:8px">
            <strong style="font-size:12px">{{ $trans['pdf_possible_hidden_costs'] ?? 'Possible Hidden Costs' }}:</strong>
            <ul class="factors-list">
                @foreach($data['risk_analysis']['possible_hidden_costs'] as $cost)
                <li>{{ $cost }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- FINAL SCORE & VERDICT --}}
    <div class="section">
        <div class="final-score-section">
            <div class="final-score-number">{{ $data['final_score']['overall_score'] ?? 0 }}<span style="font-size:20px">/10</span></div>
            <div class="final-score-label">{{ $trans['pdf_overall_score'] ?? 'Overall Score' }}</div>

            @php
                $recommendation = $data['final_score']['recommendation'] ?? 'N/A';
                $verdict = $data['final_score']['verdict'] ?? 'N/A';
                $verdictLower = strtolower($verdict);
                $recLower = strtolower($recommendation);
            @endphp

            <div style="margin-top:12px">
                <span class="badge badge-{{ $recLower }}">{{ ucfirst($recommendation) }}</span>
            </div>

            <div class="verdict-text" style="color: {{ $verdictLower === 'avoid' ? '#dc2626' : ($verdictLower === 'negotiate' ? '#ca8a04' : '#16a34a') }}">
                {{ $verdict }}
            </div>

            @if(!empty($data['final_score']['suitable_for']))
            <div class="suitable-for">
                {{ $trans['pdf_suitable_for'] ?? 'Suitable for' }}: {{ implode(', ', $data['final_score']['suitable_for']) }}
            </div>
            @endif
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        @if($report->page_token)
        <div>{{ url("/{$report->locale}/report/{$report->page_token}") }}</div>
        @endif
        <div>{{ $trans['pdf_footer'] ?? 'This report was generated by Get Your Consultant. The information is for guidance purposes only.' }}</div>
    </div>
</div>
</body>
</html>
