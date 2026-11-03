<?php

namespace Tests\Feature;

use App\Services\GuideService;
use Tests\TestCase;

class GuidesTest extends TestCase
{
    public function test_can_view_guides_index_page(): void
    {
        $response = $this->get('/guias');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Guides/Index')
            ->has('guides', 14)
            ->has('categories')
        );
    }

    public function test_can_filter_guides_by_category(): void
    {
        $response = $this->get('/guias?categoria=Presentes');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Guides/Index')
            ->where('activeCategory', 'Presentes')
        );
    }

    public function test_can_search_guides_by_query(): void
    {
        $response = $this->get('/guias?q=casais');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Guides/Index')
            ->where('searchQuery', 'casais')
        );
    }

    public function test_can_view_individual_guide_page(): void
    {
        $response = $this->get('/guias/como-organizar-amigo-secreto-online');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Guides/Show')
            ->has('guide')
            ->where('guide.slug', 'como-organizar-amigo-secreto-online')
            ->has('related')
        );
    }

    public function test_returns_404_for_nonexistent_guide(): void
    {
        $response = $this->get('/guias/guia-inexistente-xyz');

        $response->assertStatus(404);
    }

    public function test_dicas_route_redirects_to_guias(): void
    {
        $response = $this->get('/dicas');

        $response->assertRedirect('/guias');
    }

    public function test_sitemap_includes_all_guides_and_core_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();

        $this->assertStringContainsString('https://giftmatch.online/guias', $content);
        $this->assertStringContainsString('https://giftmatch.online/guias/como-organizar-amigo-secreto-online', $content);
        $this->assertStringContainsString('https://giftmatch.online/guias/ideias-presentes-amigo-secreto-ate-50-reais', $content);

        $guideService = new GuideService;
        foreach ($guideService->getAll() as $guide) {
            $this->assertStringContainsString('/guias/'.$guide['slug'], $content);
        }
    }
}
