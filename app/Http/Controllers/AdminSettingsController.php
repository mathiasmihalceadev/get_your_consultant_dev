<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function show()
    {
        $settings = Settings::first();

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'purchase_prompt' => ['required', 'string'],
            'rental_prompt' => ['required', 'string'],
            'commercial_prompt' => ['required', 'string'],
            'auto_send' => ['boolean'],
        ]);

        $settings = Settings::first();
        $settings->update($validated);

        return back()->with('success', 'Settings saved successfully.');
    }
}
