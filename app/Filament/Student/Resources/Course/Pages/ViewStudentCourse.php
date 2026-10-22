<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\Course\Pages;

use App\Filament\Student\Resources\Course\StudentCourseResource;
use App\Models\Course;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

/**
 * @property-read Course $record
 */
class ViewStudentCourse extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = StudentCourseResource::class;

    protected string $view = 'panels.student.pages.view-course';

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title ?? 'Detalhes do Curso';
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
        ];
    }
}
