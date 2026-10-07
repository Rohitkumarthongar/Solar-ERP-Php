<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UniqueDocumentNumber
{
    public static function next(string $table, string $column, string $prefix): string
    {
        // Large random suffix plus the database unique constraint; retry at the caller on a collision.
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $number = $prefix . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(6)));
            if (!DB::table($table)->where($column, $number)->exists()) return $number;
        }
        throw new \RuntimeException('Could not allocate a unique document number.');
    }
}
