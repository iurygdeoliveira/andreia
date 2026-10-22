<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\Course\Pages;

use App\Filament\Student\Resources\Course\StudentCourseResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentCourses extends ListRecords
{
    protected static string $resource = StudentCourseResource::class;

    #[\Override]
    public function getTitle(): string
    {
        return 'Meus Cursos';
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [];
    }
}
