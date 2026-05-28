<?php

namespace Tests\Unit;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Tests\TestCase;

class HandleInertiaRequestsTest extends TestCase
{
    public function test_it_shares_the_public_wizard_maintenance_flag(): void
    {
        config()->set('app.public_wizard_maintenance', true);

        $middleware = app(HandleInertiaRequests::class);
        $request = Request::create('https://customer.example.com/get-report', 'GET');
        $shared = $middleware->share($request);
        $appFlags = value($shared['appFlags']);

        $this->assertTrue($appFlags['publicWizardMaintenance']);
    }
}