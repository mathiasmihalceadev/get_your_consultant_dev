<?php

namespace Tests\Feature;

use App\Http\Controllers\PublicReportController;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use Tests\TestCase;

class SubmitEmailTermsAcceptanceTest extends TestCase
{
    public function test_submit_email_requires_terms_acceptance(): void
    {
        $controller = app(PublicReportController::class);
        $rulesMethod = new ReflectionMethod($controller, 'submitEmailValidationRules');
        $messagesMethod = new ReflectionMethod($controller, 'submitEmailValidationMessages');

        $rulesMethod->setAccessible(true);
        $messagesMethod->setAccessible(true);

        $rules = $rulesMethod->invoke($controller);
        $messages = $messagesMethod->invoke($controller);

        $validator = Validator::make(
            ['accept_terms' => false],
            ['accept_terms' => $rules['accept_terms']],
            ['accept_terms.accepted' => $messages['accept_terms.accepted']],
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            __('wizard_accept_terms_required'),
            $validator->errors()->first('accept_terms'),
        );
    }
}