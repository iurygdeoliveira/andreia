<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Pages;

use App\Filament\Resources\Course\CourseResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Override;

class EditCourse extends EditRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;
    use NotificationsTrait;

    protected static string $resource = CourseResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview_student')
                ->label('Visão do Aluno')
                ->icon(Heroicon::Eye)
                ->color('info')
                ->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getRecord()])),
            Actions\DeleteAction::make(),
        ];
    }

    #[Override]
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->notifySuccess('Curso atualizado com sucesso.');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
