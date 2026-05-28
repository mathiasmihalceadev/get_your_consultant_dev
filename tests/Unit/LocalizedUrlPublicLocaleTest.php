<?php

namespace Tests\Unit;

use App\Support\LocalizedUrl;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocalizedUrlPublicLocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('locales.supported', ['en', 'ro']);
        config()->set('locales.public', ['ro']);
        config()->set('locales.default', 'en');
        config()->set('locales.x_default_locale', 'en');
        config()->set('locales.domain_urls', [
            'en' => 'https://getyourconsultant.com',
            'ro' => 'https://getyourconsultant.ro',
        ]);
        config()->set('locales.translated_paths', [
            '/contact' => [
                'en' => '/contact',
                'ro' => '/contact',
            ],
        ]);
    }

    public function test_it_exposes_only_public_locales_for_public_urls(): void
    {
        $request = Request::create('https://getyourconsultant.ro/contact', 'GET');

        $this->assertSame(['ro'], LocalizedUrl::publicLocales());
        $this->assertSame([
            'ro' => 'https://getyourconsultant.ro/contact',
        ], LocalizedUrl::publicLocalizedUrlsForRequest($request));
    }

    public function test_it_falls_back_to_the_public_locale_when_building_a_non_public_locale_url(): void
    {
        $this->assertSame(
            'https://getyourconsultant.ro/contact',
            LocalizedUrl::publicUrlForLocale('en', '/contact'),
        );
        $this->assertSame('ro', LocalizedUrl::publicXDefaultLocale());
    }
}