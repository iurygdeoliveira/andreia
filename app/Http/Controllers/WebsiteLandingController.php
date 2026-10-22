<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

final class WebsiteLandingController
{
    public function __invoke(): View
    {
        $totalProjects = count(config('landing-projects', []));

        $projectModel = $this->resolveEloquentModelKey('project');
        $developerModel = $this->resolveEloquentModelKey('developer');

        $totalAllProjects = match (true) {
            ($n = $this->countEloquentIfExists($projectModel)) !== null => $n,
            is_numeric(config('landing.stats.projects_in_evaluation')) => (int) config('landing.stats.projects_in_evaluation'),
            default => max($totalProjects, 0),
        };

        $totalAllDevelopers = match (true) {
            ($n = $this->countEloquentIfExists($developerModel)) !== null => $n,
            is_numeric(config('landing.stats.developers_involved')) => (int) config('landing.stats.developers_involved'),
            default => 0,
        };

        $courses = Course::query()
            ->where('status', CourseStatus::Published)
            ->with(['media', 'tags'])
            ->latest()
            ->get();

        return view('public.pages.home', [
            'totalProjects' => $totalProjects,
            'totalAllProjects' => $totalAllProjects,
            'totalAllDevelopers' => $totalAllDevelopers,
            'courses' => $courses,
        ]);
    }

    private function resolveEloquentModelKey(string $key): string
    {
        $class = config("landing.eloquent_models.{$key}");

        return is_string($class) ? $class : '';
    }

    private function countEloquentIfExists(string $class): ?int
    {
        if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            return null;
        }

        return $class::query()->count();
    }
}
