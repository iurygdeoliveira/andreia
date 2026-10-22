<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Pages;

use App\Filament\Resources\Course\CourseResource;
use App\Models\Organization;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateCourse extends CreateRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;
    use NotificationsTrait;

    protected static string $resource = CourseResource::class;

    #[Override]
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Curso criado com sucesso.');
    }

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] ??= Filament::getTenant()?->getKey()
            ?? Filament::auth()->user()?->organizations()->first()?->getKey()
            ?? Organization::query()->value('id');

        return $data;
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
