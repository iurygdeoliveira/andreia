<?php

declare(strict_types=1);

namespace App\Filament\Resources\Module\Pages;

use App\Filament\Resources\Course\CourseResource;
use App\Filament\Resources\Module\ModuleResource;
use App\Models\Module;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModule extends EditRecord
{
    use HasFeedbackAction;

    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Module) {
            return parent::getBreadcrumbs();
        }

        return [
            CourseResource::getUrl('index') => 'Cursos',
            CourseResource::getUrl('edit', ['record' => $record->course]) => 'Editar Curso',
            '#' => 'Gerenciar Aulas',
        ];
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof Module) {
            return parent::getRedirectUrl();
        }

        return CourseResource::getUrl('edit', ['record' => $record->course]);
    }
}
