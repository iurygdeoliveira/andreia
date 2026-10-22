<?php

declare(strict_types=1);

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class SignedUrlGenerator extends DefaultUrlGenerator
{
    #[\Override]
    public function getUrl(): string
    {
        try {
            $minutes = (int) config('media-library.temporary_url_default_lifetime', 5);

            return $this->getTemporaryUrl(now()->addMinutes($minutes));
        } catch (\Throwable) {
            return parent::getUrl();
        }
    }

    #[\Override]
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        try {
            return parent::getTemporaryUrl($expiration, $options);
        } catch (\Throwable) {
            return parent::getUrl();
        }
    }
}
