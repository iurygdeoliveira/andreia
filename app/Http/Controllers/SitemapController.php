<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Http\Response;

final class SitemapController
{
    public function __invoke(): Response
    {
        $lastCourseUpdate = Course::query()
            ->where('status', CourseStatus::Published)
            ->latest('updated_at')
            ->value('updated_at');

        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => ($lastCourseUpdate ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('privacidade'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        return response()
            ->view('public.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
