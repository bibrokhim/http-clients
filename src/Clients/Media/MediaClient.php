<?php

namespace Bibrokhim\HttpClients\Clients\Media;

use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Http\UploadedFile;
use LogicException;

class MediaClient extends BaseClient
{
    public function uploadImages(array $images, string $folderName): array
    {
        $response = $this
            ->attach($images, 'images')
            ->post('/images', [
                'folder' => $folderName
            ]);

        return $response->json('images');
    }

    public function uploadImage(UploadedFile $image, string $folderName): array
    {
        $response = $this
            ->attach([$image], 'images')
            ->post('/images', [
                'folder' => $folderName
            ]);

        return $response->json('images')[0];
    }

    public function updateImage(int $imageId, UploadedFile $imageFile): array
    {
        $response = $this
            ->attach($imageFile, 'image')
            ->post("/images/$imageId?_method=PUT");

        return $response->json();
    }

    public function deleteImage(int $imageId): array
    {
        $response = $this
            ->delete("/images/$imageId");

        return $response->json();
    }

    public function deleteManyImages(array $imageIds): array
    {
        foreach ($imageIds as $imageId) {
            if (! is_numeric($imageId)) {
                throw new LogicException('Image IDs should be numeric');
            }
        }

        $ids = implode(',', $imageIds);

        $response = $this
            ->delete("/images-many?ids=$ids");

        return $response->json();
    }

    public function uploadFiles(array $files): array
    {
        $response = $this
            ->attach($files, 'files')
            ->post('/files');

        return $response->json();
    }

    public function deleteFile(int $fileId)
    {
        $response = $this
            ->delete("/files/$fileId");

        return $response->json();
    }
}