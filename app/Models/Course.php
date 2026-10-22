<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\CourseStatus;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

/**
 * @property int $organization_id
 */
#[Fillable([
    'organization_id',
    'title',
    'slug',
    'description',
    'cover_image_path',
    'price_in_cents',
    'checkout_code',
    'status',
])]
#[RouteKey('checkout_code')]
class Course extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia, SoftDeletes;

    public function registerMediaCollections(): void
    {
        $disk = config('media-library.disk_name', 's3');

        if ($disk === 's3' && empty(config('filesystems.disks.s3.key'))) {
            $disk = 'public';
        }

        $this->addMediaCollection('cover')
            ->useDisk($disk)
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void {}

    #[\Override]
    protected function casts(): array
    {
        return [
            'price_in_cents' => MoneyCast::class,
            'status' => CourseStatus::class,
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        static::creating(function (Course $course): void {
            if (empty($course->organization_id)) {
                $course->organization_id = Filament::getTenant()?->getKey()
                    ?: Filament::auth()->user()?->organizations()->first()?->getKey()
                    ?: Organization::query()->value('id');
            }

            if (empty($course->checkout_code)) {
                $course->checkout_code = (string) Str::uuid();
            }

            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['status', 'enrolled_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * @return list<string>
     */
    public function getBenefits(): array
    {
        return [
            'Certificado reconhecido pelo MEC',
            'Acesso imediato por 1 ano',
            'Material didático em PDF para download',
            'Aulas gravadas em alta resolução',
            '7 dias de garantia incondicional',
        ];
    }

    /**
     * @return list<string>
     */
    public function getBenefitsAttribute(): array
    {
        return $this->getBenefits();
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('cover', 'thumb')
            ?: ($this->getFirstMediaUrl('cover')
            ?: ($this->cover_image_path
                ? Storage::url($this->cover_image_path)
                : asset('images/og-cover.jpg')));
    }

    public function getFormattedPriceAttribute(): string
    {
        return Number::currency((float) $this->price_in_cents, 'BRL', locale: 'pt_BR');
    }
}
