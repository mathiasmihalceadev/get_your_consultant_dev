<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function show()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => Settings::getAllSettings(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'rental_living_prompt' => ['required', 'string'],
            'rental_living_prompt_ro' => ['required', 'string'],
            'rental_business_prompt' => ['required', 'string'],
            'rental_business_prompt_ro' => ['required', 'string'],
            'buying_living_prompt' => ['required', 'string'],
            'buying_living_prompt_ro' => ['required', 'string'],
            'buying_business_prompt' => ['required', 'string'],
            'buying_business_prompt_ro' => ['required', 'string'],
            'auto_send' => ['boolean'],
        ]);

        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
