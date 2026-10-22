<?php

declare(strict_types=1);

namespace App\Support\MediaLibrary;

use App\Models\Organization;
use Filament\Facades\Filament;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);
        $prefix = $this->getTenantPrefix($media);

        return "{$prefix}{$folder}/{$media->id}/";
    }

    public function getPathForConversions(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);
        $prefix = $this->getTenantPrefix($media);

        return "{$prefix}{$folder}/{$media->id}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);
        $prefix = $this->getTenantPrefix($media);

        return "{$prefix}{$folder}/{$media->id}/responsive-images/";
    }

    private function getTenantPrefix(Media $media): string
    {
        $model = $media->model;
        $tenantId = null;

        if ($model instanceof Organization) {
            $tenantId = $model->getKey();
        } elseif ($model && isset($model->organization_id)) {
            $tenantId = $model->organization_id;
        } else {
            $tenant = Filament::getTenant();
            $tenantId = $tenant ? $tenant->getKey() : null;
        }

        return $tenantId ? "tenants/{$tenantId}/" : '';
    }

    private function getFolderByMimeType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'cover';
        }

        return 'documents';
    }
}
