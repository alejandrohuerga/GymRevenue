<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_successfully(): void
    {
        $routes = [
            'home',
            'calculator',
            'thanks',
            'how-it-works',
            'pricing',
            'legal',
            'privacy',
            'cookies',
        ];

        foreach ($routes as $route) {
            $this->get(route($route))->assertOk();
        }
    }
}