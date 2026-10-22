<?php

declare(strict_types=1);

namespace App\Filament\Resources\Module;

use App\Filament\Resources\Course\CourseResource;
use App\Filament\Resources\Module\Pages\EditModule;
use App\Filament\Resources\Module\RelationManagers\LessonsRelationManager;
use App\Models\Module;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Override;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static bool $shouldRegisterNavigation = false;

    #[Override]
    public static function getRelations(): array
    {
        return [
            LessonsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'edit' => EditModule::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return CourseResource::getUrl('index');
    }
}
