<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function show()
    {
        return Inertia::render('Public/Contact');
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

        return redirect()->route('contact')->with('success', __('contact_form_success'));
    }
}