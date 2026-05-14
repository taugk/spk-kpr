<?php

use Illuminate\Support\Facades\DB;

if (! function_exists('format_bobot')) {
    function format_bobot($bobot): string
    {
        return number_format((float) $bobot * 100, 2) . '%';
    }
}

if (! function_exists('total_bobot_kriteria')) {
    function total_bobot_kriteria(): string
    {
        $total = DB::table('kriteria')->sum('bobot');

        return format_bobot($total);
    }
}

if (! function_exists('label_tipe_kriteria')) {
    function label_tipe_kriteria(string $tipe): string
    {
        return $tipe === 'benefit'
            ? '<span class="badge badge-success">Benefit</span>'
            : '<span class="badge badge-danger">Cost</span>';
    }
}