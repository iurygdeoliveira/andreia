<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course;

use App\Filament\Resources\Course\Pages\CreateCourse;
use App\Filament\Resources\Course\Pages\DeleteCourse;
use App\Filament\Resources\Course\Pages\EditCourse;
use App\Filament\Resources\Course\Pages\ListCourses;
use App\Filament\Resources\Course\Pages\ViewCourse;
use App\Filament\Resources\Course\RelationManagers\ModulesRelationManager;
use App\Filament\Resources\Course\Schemas\CourseForm;
use App\Filament\Resources\Course\Schemas\CourseInfolist;
use App\Filament\Resources\Course\Tables\CourseTable;
use App\Models\Course;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class CourseResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Course::class;

    /** @see Course::organization() */
    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static bool $isScopedToTenant = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $title = 'Cursos';

    protected static ?string $recordTitleAttribute = 'title';

    #[Override]
    public static function getModelLabel(): string
    {
        return 'Curso';
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return 'Cursos';
    }

    #[Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'checkout_code';
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return parent::getEloquentQuery()
            ->when($tenant, fn (Builder $query) => $query->where('organization_id', $tenant->getKey()));
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return CourseTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            ModulesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
            'delete' => DeleteCourse::route('/{record}/delete'),
        ];
    }
}
