<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #111;
            background: #fff;
            font-size: 13px;
            line-height: 1.5;
        }
        .page { padding: 40px; max-width: 800px; margin: 0 auto; }
        .header {
            border-bottom: 3px solid #1a56db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-type {
            display: inline-block;
            background: #1a56db;
            color: #fff;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h1 {
            font-size: 24px;
            margin-top: 12px;
            color: #111;
        }
        .header-meta {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }
        .header-meta a { color: #1a56db; word-break: break-all; }
        .section {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a56db;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .grid { display: flex; flex-wrap: wrap; gap: 0; }
        .grid-item {
            width: 50%;
            padding: 6px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .grid-item-label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .grid-item-value {
            font-size: 14px;
            font-weight: 600;
            color: #111;
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
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
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
        .risk-list { list-style: disc; padding-left: 20px; }
        .risk-list li { margin-bottom: 4px; font-size: 13px; }
        .factors-list { margin: 6px 0; padding-left: 18px; }
        .factors-list li { font-size: 12px; margin-bottom: 2px; }
        .final-score-section {
            background: #f8fafc;
            border: 2px solid #1a56db;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
        }
        .final-score-number {
            font-size: 48px;
            font-weight: 800;
            color: #1a56db;
        }
        .final-score-label { font-size: 12px; color: #666; margin-top: 4px; }
        .verdict-text { font-size: 20px; font-weight: 700; margin-top: 10px; }
        .suitable-for {
            margin-top: 8px;
            font-size: 12px;
            color: #555;
        }
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- HEADER --}}
    <div class="header">
        <span class="header-type">
            @if($report->report_type === 'purchase') Purchase Report
            @elseif($report->report_type === 'rental') Rental Report
            @else Commercial Report
            @endif
        </span>
        <h1>Property Analysis Report</h1>
        <div class="header-meta">
            <div>Generated: {{ now()->format('F j, Y') }}</div>
            <div>URL: <a href="{{ $report->url }}">{{ $report->url }}</a></div>
        </div>
    </div>

    {{-- PROPERTY SUMMARY --}}
    <div class="section">
        <div class="section-title">Property Summary</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Property Type</div>
                <div class="grid-item-value">{{ $data['property_summary']['property_type'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">City</div>
                <div class="grid-item-value">{{ $data['property_summary']['city'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Area</div>
                <div class="grid-item-value">{{ $data['property_summary']['area'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Size</div>
                <div class="grid-item-value">{{ $data['property_summary']['size_sqm'] ?? 'N/A' }} m²</div>
            </div>
            @if($report->report_type !== 'commercial')
            <div class="grid-item">
                <div class="grid-item-label">Rooms</div>
                <div class="grid-item-value">{{ $data['property_summary']['rooms'] ?? 'N/A' }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Floor</div>
                <div class="grid-item-value">{{ $data['property_summary']['floor'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Year Built</div>
                <div class="grid-item-value">{{ $data['property_summary']['year_built'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Condition</div>
                <div class="grid-item-value">{{ $data['property_summary']['condition'] ?? 'N/A' }}</div>
            </div>
            @if($report->report_type === 'purchase')
            <div class="grid-item">
                <div class="grid-item-label">Asking Price</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_price'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Price/m²</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['price_per_sqm'] ?? 0) }}</div>
            </div>
            @elseif($report->report_type === 'rental')
            <div class="grid-item">
                <div class="grid-item-label">Monthly Rent</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_rent_monthly'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Price/m²/month</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['price_per_sqm_monthly'] ?? 0, 2) }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">Asking Price</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_price'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Monthly Rent</div>
                <div class="grid-item-value">€{{ number_format($data['property_summary']['asking_rent_monthly'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Zoning</div>
                <div class="grid-item-value">{{ $data['property_summary']['zoning'] ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- PRICE / RENT EVALUATION --}}
    <div class="section">
        @if($report->report_type === 'rental')
        <div class="section-title">Rent Evaluation</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Estimated Fair Rent</div>
                <div class="grid-item-value">€{{ number_format($data['rent_evaluation']['estimated_fair_rent'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Rent Positioning</div>
                <div class="grid-item-value">{{ str_replace('_', ' ', ucfirst($data['rent_evaluation']['rent_positioning'] ?? 'N/A')) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Market Difference</div>
                <div class="grid-item-value">{{ $data['rent_evaluation']['market_difference_percent'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Rent Score</div>
                <div class="grid-item-value">
                    @php $rs = $data['rent_evaluation']['rent_score'] ?? 0; @endphp
                    <span class="score-box {{ $rs >= 7 ? 'score-green' : ($rs >= 4 ? 'score-yellow' : 'score-red') }}">{{ $rs }}</span>
                </div>
            </div>
        </div>
        @if(!empty($data['rent_evaluation']['value_increasing_factors']))
        <div style="margin-top:10px"><strong>Value Increasing:</strong>
            <ul class="factors-list">
                @foreach($data['rent_evaluation']['value_increasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(!empty($data['rent_evaluation']['value_decreasing_factors']))
        <div><strong>Value Decreasing:</strong>
            <ul class="factors-list">
                @foreach($data['rent_evaluation']['value_decreasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @else
        <div class="section-title">Price Evaluation</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Estimated Market Value</div>
                <div class="grid-item-value">€{{ number_format($data['price_evaluation']['estimated_market_value'] ?? 0) }}</div>
            </div>
            @if($report->report_type === 'commercial')
            <div class="grid-item">
                <div class="grid-item-label">Estimated Fair Rent</div>
                <div class="grid-item-value">€{{ number_format($data['price_evaluation']['estimated_fair_rent'] ?? 0) }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Price Positioning</div>
                <div class="grid-item-value">{{ str_replace('_', ' ', ucfirst($data['price_evaluation']['price_positioning'] ?? 'N/A')) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Market Difference</div>
                <div class="grid-item-value">{{ $data['price_evaluation']['market_difference_percent'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Price Score</div>
                <div class="grid-item-value">
                    @php $ps = $data['price_evaluation']['price_score'] ?? 0; @endphp
                    <span class="score-box {{ $ps >= 7 ? 'score-green' : ($ps >= 4 ? 'score-yellow' : 'score-red') }}">{{ $ps }}</span>
                </div>
            </div>
        </div>
        @if(!empty($data['price_evaluation']['value_increasing_factors']))
        <div style="margin-top:10px"><strong>Value Increasing:</strong>
            <ul class="factors-list">
                @foreach($data['price_evaluation']['value_increasing_factors'] as $f)
                <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(!empty($data['price_evaluation']['value_decreasing_factors']))
        <div><strong>Value Decreasing:</strong>
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
        <div class="section-title">Area Analysis</div>
        <div class="grid">
            @if($report->report_type === 'commercial')
            <div class="grid-item">
                <div class="grid-item-label">Foot Traffic</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['foot_traffic'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Visibility</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['visibility'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Accessibility</div>
                <div class="grid-item-value">{{ $data['area_analysis']['accessibility'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Competition</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['competition_density'] ?? 'N/A') }}</div>
            </div>
            @else
            @if($report->report_type === 'purchase')
            <div class="grid-item">
                <div class="grid-item-label">Suitable for Living</div>
                <div class="grid-item-value">{{ ($data['area_analysis']['suitable_for_living'] ?? false) ? 'Yes' : 'No' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Suitable for Investment</div>
                <div class="grid-item-value">{{ ($data['area_analysis']['suitable_for_investment'] ?? false) ? 'Yes' : 'No' }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">Suitable for Living</div>
                <div class="grid-item-value">{{ ($data['area_analysis']['suitable_for_living'] ?? false) ? 'Yes' : 'No' }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Safety</div>
                <div class="grid-item-value">{{ $data['area_analysis']['safety'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Quietness</div>
                <div class="grid-item-value">{{ $data['area_analysis']['quietness'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Pollution</div>
                <div class="grid-item-value">{{ $data['area_analysis']['pollution'] ?? 'N/A' }}/10</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Traffic</div>
                <div class="grid-item-value">{{ $data['area_analysis']['traffic'] ?? 'N/A' }}/10</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Area Trend</div>
                <div class="grid-item-value">{{ ucfirst($data['area_analysis']['area_trend'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Area Score</div>
                <div class="grid-item-value">
                    @php $as = $data['area_analysis']['area_score'] ?? 0; @endphp
                    <span class="score-box {{ $as >= 7 ? 'score-green' : ($as >= 4 ? 'score-yellow' : 'score-red') }}">{{ $as }}</span>
                </div>
            </div>
        </div>
        @if($report->report_type !== 'commercial' && !empty($data['area_analysis']['amenities']))
        <div style="margin-top:12px">
            <strong style="font-size:12px">Amenities:</strong>
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
        <div class="section-title">Parking</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Parking Available</div>
                <div class="grid-item-value">{{ ($data['parking']['exists'] ?? false) ? 'Yes' : 'No' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Type</div>
                <div class="grid-item-value">{{ ucfirst($data['parking']['type'] ?? 'N/A') }}</div>
            </div>
            @if($report->report_type === 'purchase')
            <div class="grid-item">
                <div class="grid-item-label">Value Impact</div>
                <div class="grid-item-value">{{ $data['parking']['value_impact'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Est. Cost if Missing</div>
                <div class="grid-item-value">€{{ number_format($data['parking']['estimated_cost_if_missing'] ?? 0) }}</div>
            </div>
            @elseif($report->report_type === 'rental')
            <div class="grid-item">
                <div class="grid-item-label">Included in Rent</div>
                <div class="grid-item-value">{{ ($data['parking']['included_in_rent'] ?? false) ? 'Yes' : 'No' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Est. Extra Cost</div>
                <div class="grid-item-value">€{{ number_format($data['parking']['estimated_extra_cost'] ?? 0) }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">Spaces</div>
                <div class="grid-item-value">{{ $data['parking']['spaces'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Value Impact</div>
                <div class="grid-item-value">{{ $data['parking']['value_impact'] ?? 'N/A' }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Parking Score</div>
                <div class="grid-item-value">
                    @php $pks = $data['parking']['parking_score'] ?? 0; @endphp
                    <span class="score-box {{ $pks >= 7 ? 'score-green' : ($pks >= 4 ? 'score-yellow' : 'score-red') }}">{{ $pks }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- INVESTMENT (purchase & commercial) / LIVABILITY (rental) --}}
    @if($report->report_type === 'rental')
    <div class="section">
        <div class="section-title">Livability</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Natural Light</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['natural_light'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Noise Level</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['noise_level'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Ventilation</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['ventilation'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Storage</div>
                <div class="grid-item-value">{{ ucfirst($data['livability']['storage'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Ideal Tenant</div>
                <div class="grid-item-value">{{ $data['livability']['ideal_tenant_profile'] ?? 'N/A' }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Livability Score</div>
                <div class="grid-item-value">
                    @php $ls = $data['livability']['livability_score'] ?? 0; @endphp
                    <span class="score-box {{ $ls >= 7 ? 'score-green' : ($ls >= 4 ? 'score-yellow' : 'score-red') }}">{{ $ls }}</span>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="section">
        <div class="section-title">Investment Analysis</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Est. Monthly Rent</div>
                <div class="grid-item-value">€{{ number_format($data['investment']['estimated_monthly_rent'] ?? 0) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Demand Level</div>
                <div class="grid-item-value">{{ ucfirst($data['investment']['demand_level'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Gross Yield</div>
                <div class="grid-item-value">{{ $data['investment']['gross_yield'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Net Yield</div>
                <div class="grid-item-value">{{ $data['investment']['net_yield'] ?? 0 }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Occupancy Rate</div>
                <div class="grid-item-value">{{ $data['investment']['occupancy_rate'] ?? 0 }}%</div>
            </div>
            @if($report->report_type === 'purchase')
            <div class="grid-item">
                <div class="grid-item-label">Ideal Tenant</div>
                <div class="grid-item-value">{{ $data['investment']['ideal_tenant'] ?? 'N/A' }}</div>
            </div>
            @else
            <div class="grid-item">
                <div class="grid-item-label">Ideal Business Type</div>
                <div class="grid-item-value">{{ is_array($data['investment']['ideal_business_type'] ?? null) ? implode(', ', $data['investment']['ideal_business_type']) : ($data['investment']['ideal_business_type'] ?? 'N/A') }}</div>
            </div>
            @endif
            <div class="grid-item">
                <div class="grid-item-label">Investment Score</div>
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
        <div class="section-title">Air Quality</div>
        <div class="grid">
            <div class="grid-item">
                <div class="grid-item-label">Quality</div>
                <div class="grid-item-value">{{ ucfirst($data['air_quality']['quality'] ?? 'N/A') }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-item-label">Impact</div>
                <div class="grid-item-value">{{ $data['air_quality'][($report->report_type === 'commercial' ? 'impact_on_business' : 'impact_on_living')] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- RISK ANALYSIS --}}
    <div class="section">
        <div class="section-title">Risk Analysis</div>
        @if($report->report_type === 'rental')
        <ul class="risk-list">
            <li><strong>Landlord Risk:</strong> {{ $data['risk_analysis']['landlord_risk'] ?? 'N/A' }}</li>
            <li><strong>Legal Risk:</strong> {{ $data['risk_analysis']['legal_risk'] ?? 'N/A' }}</li>
        </ul>
        @else
        <ul class="risk-list">
            <li><strong>Construction Risk:</strong> {{ $data['risk_analysis']['construction_risk'] ?? 'N/A' }}</li>
            <li><strong>Legal Risk:</strong> {{ $data['risk_analysis']['legal_risk'] ?? 'N/A' }}</li>
            @if($report->report_type === 'commercial')
            <li><strong>Zoning Risk:</strong> {{ $data['risk_analysis']['zoning_risk'] ?? 'N/A' }}</li>
            @endif
        </ul>
        @endif
        @if(!empty($data['risk_analysis']['possible_hidden_costs']))
        <div style="margin-top:8px">
            <strong style="font-size:12px">Possible Hidden Costs:</strong>
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
            <div class="final-score-label">Overall Score</div>

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
                Suitable for: {{ implode(', ', $data['final_score']['suitable_for']) }}
            </div>
            @endif
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        @if($report->page_token)
        <div>Status page: {{ url("/report/{$report->page_token}") }}</div>
        @endif
        <div>Generated by Property Report System</div>
    </div>
</div>
</body>
</html>
