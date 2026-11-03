<?php

namespace App\Http\Controllers;

use App\Services\GuideService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function index(Request $request, GuideService $guideService): Response
    {
        $category = $request->query('categoria');
        $search = $request->query('q');

        $allGuides = $guideService->getAll();
        $categories = $guideService->getCategories();

        $filtered = array_values(array_filter($allGuides, function (array $guide) use ($category, $search): bool {
            if ($category && strtolower($guide['category']) !== strtolower($category)) {
                return false;
            }

            if ($search) {
                $term = mb_strtolower(trim($search));
                $inTitle = str_contains(mb_strtolower($guide['title']), $term);
                $inExcerpt = str_contains(mb_strtolower($guide['excerpt']), $term);
                $inCategory = str_contains(mb_strtolower($guide['category']), $term);

                return $inTitle || $inExcerpt || $inCategory;
            }

            return true;
        }));

        return Inertia::render('Guides/Index', [
            'guides' => $filtered,
            'categories' => $categories,
            'activeCategory' => $category,
            'searchQuery' => $search ?? '',
        ]);
    }

    public function show(string $slug, GuideService $guideService): Response
    {
        $guide = $guideService->getBySlug($slug);

        abort_if(! $guide, 404, 'Guia não encontrado');

        $related = $guideService->getRelated($slug, 3);

        return Inertia::render('Guides/Show', [
            'guide' => $guide,
            'related' => $related,
        ]);
    }
}

