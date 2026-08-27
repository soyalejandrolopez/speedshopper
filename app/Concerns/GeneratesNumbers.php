<?php

namespace App\Concerns;

use Illuminate\Support\Facades\DB;

trait GeneratesNumbers
{
    public static function nextNumber(string $prefix): string
    {
        $table = (new static)->getTable();

        return DB::transaction(function () use ($table, $prefix) {
            $last = DB::table($table)
                ->where('number', 'like', $prefix.'-%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value('number');

            $next = $last ? (int) substr($last, strlen($prefix) + 1) + 1 : 1;

            return sprintf('%s-%04d', $prefix, $next);
        });
    }
}
