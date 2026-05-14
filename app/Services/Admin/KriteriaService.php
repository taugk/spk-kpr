<?php

namespace App\Services\Admin;

use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KriteriaService
{
    public function create(array $data): Kriteria
    {
        return DB::transaction(function () use ($data) {
            $this->validateTotalBobot((float) $data['bobot']);
            $data['aktif'] = $data['aktif'] ?? 0;
            return Kriteria::create($data);
        });
    }

    public function update(Kriteria $kriteria, array $data): Kriteria
    {
        return DB::transaction(function () use ($kriteria, $data) {
            $this->validateTotalBobot((float) $data['bobot'], $kriteria->id);
            $data['aktif'] = $data['aktif'] ?? 0;
            $kriteria->update($data);
            return $kriteria;
        });
    }

    private function validateTotalBobot(float $bobotBaru, ?int $ignoreId = null): void
    {
        $query = Kriteria::query();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $total = (float) $query->sum('bobot') + $bobotBaru;

        if ($total > 1.0000) {
            throw ValidationException::withMessages([
                'bobot' => 'Total bobot semua kriteria tidak boleh lebih dari 1.0000 atau 100%. Total saat ini: ' . number_format($total, 4),
            ]);
        }
    }
}
