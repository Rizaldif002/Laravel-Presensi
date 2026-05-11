<?php

namespace App\Helpers;

class GpsHelper
{
    public static function hitungJarak(
        float $latMahasiswa, float $lonMahasiswa,
        float $latRuangan,   float $lonRuangan
    ): float {
        $r    = 6371000;
        $dLat = deg2rad($latRuangan - $latMahasiswa);
        $dLon = deg2rad($lonRuangan - $lonMahasiswa);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($latMahasiswa))
              * cos(deg2rad($latRuangan))
              * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
