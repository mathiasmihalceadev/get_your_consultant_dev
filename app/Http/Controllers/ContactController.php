<?php

namespace App\Http\Controllers;

use App\Support\LocalizedUrl;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        $locale = app()->getLocale();
        $path = '/contact';
        $alternates = collect(LocalizedUrl::publicLocales())
            ->mapWithKeys(fn (string $publicLocale) => [
                $publicLocale === 'ro' ? 'ro-RO' : 'en-US' => LocalizedUrl::publicUrlForLocale($publicLocale, $path),
            ])
            ->all();

        return response()->view('public.contact', [
            'canonical' => LocalizedUrl::publicUrlForLocale($locale, $path),
            'alternates' => $alternates,
            'xDefault' => LocalizedUrl::publicUrlForLocale(LocalizedUrl::publicXDefaultLocale(), $path),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:5000'],
            ],
            [
                'name.required' => __('contact_validation_name_required'),
                'email.required' => __('contact_validation_email_required'),
                'email.email' => __('contact_validation_email_invalid'),
                'subject.required' => __('contact_validation_subject_required'),
                'message.required' => __('contact_validation_message_required'),
            ],
        );

        ContactInquiry::create([
            ...$validated,
            'locale' => app()->getLocale(),
        ]);

        Log::channel('report')->info('Contact inquiry submitted', [
            'email' => $validated['email'],
            'locale' => app()->getLocale(),
            'subject' => $validated['subject'],
        ]);

        return redirect()
            ->route('contact')
            ->with('success', __('contact_form_success'))
            ->with('dataLayerEvents', [[
                'event' => 'contact_submitted',
                'event_id' => 'contact_submitted_'.now()->timestamp,
            ]]);
    }
}