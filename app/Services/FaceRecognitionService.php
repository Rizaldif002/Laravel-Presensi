<?php

namespace App\Services;

class FaceRecognitionService
{
    const THRESHOLD = 0.80;

    public function verify(bool $faceMatch, float $confidence): bool
    {
        return $faceMatch && $confidence >= self::THRESHOLD;
    }
}
