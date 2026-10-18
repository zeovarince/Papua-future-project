<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;

final class ApplicationService
{
    public function __construct(private readonly ApplicationFileService $files = new ApplicationFileService())
    {
    }

    public function submit(array $values, ?array $photo, ?array $cv): int
    {
        $token = bin2hex(random_bytes(16));
        $paths = $this->files->store($token, $photo, $cv);
        try {
            return Application::createForOpenBatch($values, $paths, $token);
        } catch (\Throwable $exception) {
            $this->files->deleteToken($token);
            throw $exception;
        }
    }
}
