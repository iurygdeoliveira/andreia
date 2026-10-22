<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Schemas;

use App\Enums\CourseStatus;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Leandrocfe\FilamentPtbrFormFields\Money;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informações Gerais')
                ->description('Dados principais e precificação do curso')
                ->columns(3)
                ->components([
                    Select::make('organization_id')
                        ->label('Organização')
                        ->relationship('organization', 'name')
                        ->searchable()
                        ->default(fn (): ?int => Filament::getTenant()?->getKey() ?? Filament::auth()->user()?->organizations()->first()?->getKey() ?? Organization::query()->value('id'))
                        ->hidden(fn (): bool => Filament::getTenant() !== null)
                        ->required()
                        ->columnSpan(3),

                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),

                    Money::make('price_in_cents')
                        ->label('Preço')
                        ->required()
                        ->formatStateUsing(fn ($state): string => $state !== null ? number_format((float) $state, 2, ',', '.') : '0,00')
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status')
                        ->options(CourseStatus::class)
                        ->default(CourseStatus::Draft)
                        ->native(false)
                        ->required()
                        ->columnSpan(1),

                    SpatieTagsInput::make('tags')
                        ->label('Tags')
                        ->columnSpan(2),
                ]),

            Section::make('Mídia e Conteúdo')
                ->description('Imagem de capa e descrição resumida do curso')
                ->columns(3)
                ->components([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label('Imagem de Capa')
                        ->collection('cover')
                        ->image()
                        ->maxSize(10240)
                        ->nullable()
                        ->columnSpan(2)
                        ->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
                            if (! $record instanceof Model || ! method_exists($record, 'addMedia')) {
                                return null;
                            }

                            $extension = $file->getClientOriginalExtension() ?: 'png';
                            $inputTmpPath = sys_get_temp_dir().'/'.Str::uuid().'_input.'.$extension;
                            $tmpPath = sys_get_temp_dir().'/'.Str::uuid().'.webp';

                            try {
                                file_put_contents($inputTmpPath, $file->get());

                                Image::load($inputTmpPath)
                                    ->fit(Fit::Crop, 1200, 675)
                                    ->format('webp')
                                    ->save($tmpPath);

                                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $filename = Str::slug($originalName) ?: 'cover';
                                $filename .= '.webp';

                                $targetDisk = $component->getDiskName();
                                if ($targetDisk === 's3' && empty(config('filesystems.disks.s3.key'))) {
                                    $targetDisk = 'public';
                                }

                                $media = $record->addMedia($tmpPath)
                                    ->usingFileName($filename)
                                    ->usingName($originalName)
                                    ->toMediaCollection($component->getCollection() ?? 'cover', $targetDisk);

                                return $media->getAttributeValue('uuid');
                            } finally {
                                if (file_exists($inputTmpPath)) {
                                    unlink($inputTmpPath);
                                }
                                if (file_exists($tmpPath)) {
                                    unlink($tmpPath);
                                }
                            }
                        }),

                    Textarea::make('description')
                        ->label('Descrição')
                        ->maxLength(100)
                        ->rows(2)
                        ->nullable()
                        ->columnSpan(1),
                ]),
        ]);
    }
}
