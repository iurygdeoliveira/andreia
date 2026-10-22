<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\Course;

use App\Filament\Student\Resources\Course\Pages\ListStudentCourses;
use App\Filament\Student\Resources\Course\Pages\ViewStudentCourse;
use App\Models\Course;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Override;

class StudentCourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static bool $isScopedToTenant = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Meus Cursos';

    protected static ?string $title = 'Meus Cursos';

    protected static ?string $recordTitleAttribute = 'title';

    #[Override]
    public static function getModelLabel(): string
    {
        return 'Curso';
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return 'Meus Cursos';
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
        $userId = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->when($tenant, fn (Builder $query) => $query->where('organization_id', $tenant->getKey()))
            ->whereIn('courses.id', Enrollment::query()
                ->select('course_id')
                ->where('user_id', $userId)
                ->active()
                ->when($tenant, fn (Builder $q) => $q->where('organization_id', $tenant->getKey())));
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->collection('cover')
                    ->label('Capa')
                    ->size(60),
                TextColumn::make('title')
                    ->label('Título do Curso')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Course $record): string => Str::limit((string) $record->description, 80)),
                TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label('Módulos')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('enrollment_plan')
                    ->label('Seu Plano')
                    ->badge()
                    ->state(function (Course $record): string {
                        $enrollment = Enrollment::query()
                            ->where('course_id', $record->id)
                            ->where('user_id', Filament::auth()->id())
                            ->first();

                        return $enrollment?->purchase_type?->getLabel() ?? 'Plano Premium';
                    })
                    ->color(function (Course $record): string {
                        $enrollment = Enrollment::query()
                            ->where('course_id', $record->id)
                            ->where('user_id', Filament::auth()->id())
                            ->first();

                        return $enrollment?->purchase_type?->getColor() ?? 'warning';
                    }),
            ])
            ->filters([
                Filter::make('has_modules')
                    ->label('Com módulos')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->has('modules')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Acessar')
                    ->icon(Heroicon::ArrowRightCircle)
                    ->color('primary'),
            ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListStudentCourses::route('/'),
            'view' => ViewStudentCourse::route('/{record}'),
        ];
    }
}
