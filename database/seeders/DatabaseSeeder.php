<?php

namespace Database\Seeders;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Settings::create([
            'purchase_prompt' => $this->purchasePrompt(),
            'rental_prompt' => $this->rentalPrompt(),
            'commercial_prompt' => $this->commercialPrompt(),
            'auto_send' => false,
        ]);
    }

    private function purchasePrompt(): string
    {
        return 'You are an AI consultant and professional real estate market analyst. Your task is to analyze a property listing from a provided URL. Extract all relevant information from the listing and evaluate the property using realistic market logic. The property may be located in any country. Adapt your evaluation to the local market conditions, city characteristics, and economic context. If some information is missing from the listing, estimate reasonable values using typical market patterns for the location.

Return strictly valid JSON. Do not include explanations, comments, or additional text.

Output structure:
{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "rooms": 0, "floor": "", "year_built": "", "condition": "", "asking_price": 0, "price_per_sqm": 0 }, "price_evaluation": { "estimated_market_value": 0, "price_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "price_score": 1 }, "area_analysis": { "suitable_for_living": true, "suitable_for_investment": true, "safety": 1, "quietness": 1, "pollution": 1, "traffic": 1, "amenities": { "schools": true, "kindergartens": true, "hospitals": true, "public_transport": true, "shopping_centers": true }, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "value_impact": "", "estimated_cost_if_missing": 0, "parking_score": 1 }, "investment": { "estimated_monthly_rent": 0, "demand_level": "low | medium | high", "gross_yield": 0, "net_yield": 0, "occupancy_rate": 0, "ideal_tenant": "", "investment_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_living": "" }, "risk_analysis": { "construction_risk": "", "legal_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "BUY | NEGOTIATE | AVOID" } }';
    }

    private function rentalPrompt(): string
    {
        return 'You are an AI consultant and professional rental market analyst. Your task is to analyze a rental listing from a provided URL. Extract all relevant information and evaluate the property as a rental opportunity using realistic market logic. Adapt your evaluation to the local rental market, city characteristics, and economic context. Estimate reasonable values when data is missing.

Return strictly valid JSON. Do not include explanations, comments, or additional text.

Output structure:
{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "rooms": 0, "floor": "", "year_built": "", "condition": "", "asking_rent_monthly": 0, "price_per_sqm_monthly": 0 }, "rent_evaluation": { "estimated_fair_rent": 0, "rent_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "rent_score": 1 }, "area_analysis": { "suitable_for_living": true, "safety": 1, "quietness": 1, "pollution": 1, "traffic": 1, "amenities": { "schools": true, "kindergartens": true, "hospitals": true, "public_transport": true, "shopping_centers": true }, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "included_in_rent": true, "estimated_extra_cost": 0, "parking_score": 1 }, "livability": { "natural_light": "good | medium | poor", "noise_level": "low | medium | high", "ventilation": "good | medium | poor", "storage": "adequate | limited | none", "ideal_tenant_profile": "", "livability_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_living": "" }, "risk_analysis": { "landlord_risk": "", "legal_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "RENT | NEGOTIATE | AVOID" } }';
    }

    private function commercialPrompt(): string
    {
        return 'You are an AI consultant and professional commercial real estate analyst. Your task is to analyze a commercial property listing from a provided URL. Evaluate the space for business use, investment potential, and market positioning using realistic commercial real estate logic. Adapt to the local market. Estimate reasonable values when data is missing.

Return strictly valid JSON. Do not include explanations, comments, or additional text.

Output structure:
{ "property_summary": { "property_type": "", "city": "", "area": "", "size_sqm": 0, "floor": "", "year_built": "", "condition": "", "asking_price": 0, "asking_rent_monthly": 0, "price_per_sqm": 0, "zoning": "" }, "price_evaluation": { "estimated_market_value": 0, "estimated_fair_rent": 0, "price_positioning": "below_market | fair | overpriced", "market_difference_percent": 0, "value_increasing_factors": [], "value_decreasing_factors": [], "price_score": 1 }, "area_analysis": { "foot_traffic": "low | medium | high", "visibility": "low | medium | high", "accessibility": 1, "competition_density": "low | medium | high", "public_transport": true, "parking_nearby": true, "area_trend": "growth | stagnation | decline", "area_score": 1 }, "parking": { "exists": true, "type": "underground | outdoor | street | none", "spaces": 0, "value_impact": "", "parking_score": 1 }, "investment": { "estimated_monthly_rent": 0, "demand_level": "low | medium | high", "gross_yield": 0, "net_yield": 0, "occupancy_rate": 0, "ideal_business_type": [], "investment_score": 1 }, "air_quality": { "quality": "good | medium | poor", "impact_on_business": "" }, "risk_analysis": { "construction_risk": "", "legal_risk": "", "zoning_risk": "", "possible_hidden_costs": [] }, "final_score": { "overall_score": 0, "recommendation": "recommended | acceptable | risky", "suitable_for": [], "verdict": "BUY | NEGOTIATE | AVOID" } }';
    }
}
