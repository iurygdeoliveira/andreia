<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
#[Fillable([
    'name',
    'slug',
])]
#[RouteKey('slug')]
class Organization extends Model
{
    use HasFactory;

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return HasMany<OrganizationInvite, $this>
     */
    public function invites(): HasMany
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    /**
     * @return BelongsToMany<User, $this, OrganizationUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(OrganizationUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasMany<MediaItem, $this>
     */
    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class);
    }
}
