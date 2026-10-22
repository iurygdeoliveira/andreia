<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Pages;

use App\Filament\Resources\Course\CourseResource;
use App\Models\Course;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;
use Override;

/**
 * @property-read Course|null $record
 * @property-read array $courseStats
 */
class ViewCourse extends ViewRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = CourseResource::class;

    protected string $view = 'panels.student.pages.view-course';

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()?->title ?? 'Detalhes do Curso';
    }

    #[Computed]
    public function courseStats(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Course) {
            return [
                'has_cover' => false,
                'tag_count' => 0,
                'formatted_price' => 'R$ 0,00',
            ];
        }

        return [
            'has_cover' => $record->hasMedia('cover'),
            'tag_count' => $record->tags()->count(),
            'formatted_price' => $record->price_in_cents ? 'R$ '.number_format($record->price_in_cents / 100, 2, ',', '.') : 'R$ 0,00',
        ];
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Curso')
                ->icon(Heroicon::PencilSquare),
            $this->getBackButtonAction(),
        ];
    }
}
