<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Lesson;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Locked;

class ViewLesson extends Page
{
    protected static ?string $slug = 'lesson/{record}';

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public Lesson $lesson;

    public function mount(Lesson $record): void
    {
        $this->lesson = $record;
        $this->authorizeLesson();
        $this->lesson->loadMissing('module.course.modules.lessons');
    }

    public function hydrate(): void
    {
        $this->authorizeLesson();
    }

    private function authorizeLesson(): void
    {
        abort_unless(in_array(Filament::getCurrentPanel()?->getId(), ['admin', 'user', 'student'], true), 403);
        $tenant = Filament::getTenant();
        $this->lesson->loadMissing('module.course');
        $course = $this->lesson->module?->course;
        abort_unless($course !== null, 404);

        if (Filament::getCurrentPanel()->getId() !== 'admin') {
            abort_unless($tenant instanceof Organization
                && $this->lesson->organization_id === $tenant->getKey()
                && $course->organization_id === $tenant->getKey(), 404);
        }

        Gate::authorize('view', $course);
        Gate::authorize('view', $this->lesson);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->lesson->title ?? 'Visualizar Aula';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getView(): string
    {
        // Se a view existir, renderiza diretamente
        if ($this->lesson->view_path && View::exists($this->lesson->view_path)) {
            return $this->lesson->view_path;
        }

        // View de fallback caso a blade ainda não tenha sido criada pelo dev
        return 'panels.pages.view-lesson-fallback';
    }
}
