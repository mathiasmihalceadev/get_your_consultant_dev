<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Inertia\Inertia;

class AdminInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ContactInquiry::query()
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Inquiries', [
            'inquiries' => $inquiries,
            'counts' => [
                'total' => ContactInquiry::count(),
                'today' => ContactInquiry::where('created_at', '>=', now()->startOfDay())->count(),
                'thisWeek' => ContactInquiry::where('created_at', '>=', now()->subDays(7))->count(),
            ],
        ]);
    }
}