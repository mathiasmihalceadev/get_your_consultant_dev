<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicMarketingPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('locales.supported', ['en', 'ro']);
        config()->set('locales.public', ['ro']);
        config()->set('locales.default', 'en');
        config()->set('locales.domain_urls', [
            'en' => 'http://myapp-com.test:8000',
            'ro' => 'http://myapp-ro.test:8000',
        ]);
        config()->set('locales.host_locale_map', [
            'myapp-com.test' => 'en',
            'myapp-ro.test' => 'ro',
        ]);
        config()->set('locales.x_default_locale', 'ro');
        config()->set('seo.indexing', true);
        config()->set('app.url', 'http://myapp-ro.test:8000');
    }

    public function test_contact_page_renders_server_side_markup(): void
    {
        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-ro.test',
        ])->get('/contact');

        $response
            ->assertOk()
            ->assertSeeText('Ai întrebări? Suntem aici să te ajutăm.')
            ->assertSee('id="contact-form"', false)
            ->assertSee('>Tornimae 5', false)
            ->assertSee('Tallinn, Estonia 10145</p>', false)
            ->assertDontSee('data-page=', false);
    }

    public function test_legal_pages_render_server_side_markup(): void
    {
        foreach ([
            ['/politica-de-confidentialitate', 'Politica de confidențialitate'],
            ['/termeni-si-conditii', 'Termeni și condiții'],
            ['/politica-de-cookie-uri', 'Politica de cookie-uri'],
        ] as [$path, $title]) {
            $response = $this->withServerVariables([
                'HTTP_HOST' => 'myapp-ro.test',
            ])->get($path);

            $response
                ->assertOk()
                ->assertSeeText($title)
                ->assertSee('<article', false)
                ->assertDontSee('data-page=', false);
        }
    }
}
