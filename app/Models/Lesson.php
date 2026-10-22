<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'organization_id',
    'module_id',
    'title',
    'sort',
    'view_path',
])]
class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public function registerMediaCollections(): void
    {
        $disk = config('media-library.disk_name', 's3');

        if ($disk === 's3' && empty(config('filesystems.disks.s3.key'))) {
            $disk = 'public';
        }

        $this->addMediaCollection('pdf')
            ->singleFile()
            ->useDisk($disk);
    }

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
