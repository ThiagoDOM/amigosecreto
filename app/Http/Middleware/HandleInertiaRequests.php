<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        if ($vercelDeploy = env('VERCEL_DEPLOYMENT_ID') ?? env('VERCEL_GIT_COMMIT_SHA')) {
            return (string) $vercelDeploy;
        }

        if (file_exists($manifest = public_path('build/manifest.json'))) {
            return hash_file('xxh128', $manifest);
        }

        if (file_exists($manifest = base_path('public/build/manifest.json'))) {
            return hash_file('xxh128', $manifest);
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'just_registered' => fn () => (bool) $request->session()->get('just_registered'),
            ],
        ];
    }
}
