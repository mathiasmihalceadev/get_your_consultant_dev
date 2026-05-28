<?php

namespace Tests\Feature;

use Tests\TestCase;

class DomainLocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('locales.supported', ['en', 'ro']);
        config()->set('locales.default', 'en');
        config()->set('locales.domain_urls', [
            'en' => 'http://myapp-com.test:8000',
            'ro' => 'http://myapp-ro.test:8000',
        ]);
        config()->set('locales.host_locale_map', [
            'myapp-com.test' => 'en',
            'myapp-ro.test' => 'ro',
        ]);
        config()->set('locales.x_default_locale', 'en');
        config()->set('seo.x_robots_tag', 'noindex, nofollow, noarchive');
    }

    public function test_romanian_domain_sets_romanian_locale(): void
    {
        config()->set('seo.indexing', false);
        config()->set('app.url', 'http://myapp-ro.test');

        $response = $this->getJson('http://myapp-ro.test/locale-test');

        $response
            ->assertOk()
            ->assertJson([
                'host' => 'myapp-ro.test',
                'locale' => 'ro',
                'message' => 'Obține raport',
            ]);
    }

    public function test_english_domain_sets_english_locale(): void
    {
        config()->set('seo.indexing', false);
        config()->set('app.url', 'http://myapp-com.test');

        $response = $this->getJson('http://myapp-com.test/locale-test');

        $response
            ->assertOk()
            ->assertJson([
                'host' => 'myapp-com.test',
                'locale' => 'en',
                'message' => 'Get Report',
            ]);
    }

    public function test_noindex_header_is_added_when_indexing_is_disabled(): void
    {
        config()->set('seo.indexing', false);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-com.test',
        ])->get('/');

        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_sitemap_is_hidden_when_indexing_is_disabled(): void
    {
        config()->set('seo.indexing', false);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-com.test',
        ])->get('/sitemap.xml');

        $response->assertNotFound();
    }

    public function test_sitemap_is_available_when_indexing_is_enabled(): void
    {
        config()->set('seo.indexing', true);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-com.test',
        ])->get('/sitemap.xml');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('http://myapp-com.test:8000', false)
            ->assertSee('http://myapp-ro.test:8000', false);
    }

    public function test_sitemap_uses_only_public_locales_when_configured(): void
    {
        config()->set('seo.indexing', true);
        config()->set('locales.public', ['ro']);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-ro.test',
        ])->get('/sitemap.xml');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('http://myapp-ro.test:8000', false)
            ->assertDontSee('http://myapp-com.test:8000', false);
    }

    public function test_old_locale_prefix_url_is_not_required(): void
    {
        config()->set('seo.indexing', false);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-com.test',
        ])->get('/en');

        $response->assertNotFound();
    }
}