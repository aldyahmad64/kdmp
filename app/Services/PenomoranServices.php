<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Penomoran;
use App\Models\SettingPenomoran;

class PenomoranServices
{
    public static function generate(Penomoran $penomoran): string
    {
        $components = $penomoran->components;
        $result = [];

        foreach ($components as $component) {
            $result[] = match ($component['type']) {
                'static' => $component['value'],
                'counter' => str_pad(
                    $penomoran->counter,
                    $component['padding'] ?? 3,
                    '0',
                    STR_PAD_LEFT
                ),
                'monthRoman' => self::romanNumerals(now()->month),
                'date' => now()->format('d'),
                'month' => now()->format('m'),
                'year' => now()->format('y'),
                default => null, // optional: kalau ada type lain yang tidak dikenal
            };
        }

        return implode('', $result);
    }

    public static function preview(Penomoran $penomoran): string
    {

        $components = $penomoran->components;
        $result = [];

        foreach ($components as $component) {
            $result[] = match ($component['type']) {
                'static' => $component['value'],
                'counter' => str_pad(
                    $penomoran->counter,
                    $component['padding'] ?? 3,
                    '0',
                    STR_PAD_LEFT
                ),
                'monthRoman' => self::romanNumerals(now()->month),
                'date' => now()->format('d'),
                'month' => now()->format('m'),
                'year' => now()->format('y'),
                default => null, // optional: kalau ada type lain yang tidak dikenal
            };
        }

        return implode('', $result);
    }

    public static function updateCounter(Penomoran $penomoran): int
    {
        $now = now();

        // Jika belum pernah reset atau bulan terakhir reset beda dengan bulan sekarang
        if (
            !$penomoran->last_reset ||
            Carbon::parse($penomoran->last_reset)->format('Ym') !== $now->format('Ym')
        ) {
            $penomoran->counter = 1;
            $penomoran->last_reset = $now->copy()->startOfMonth(); // pakai copy() biar $now tidak berubah
            $penomoran->save();

            return 1;
        }

        return $penomoran->counter;
    }

    protected static function romanNumerals($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$month] ?? '';
    }
}
