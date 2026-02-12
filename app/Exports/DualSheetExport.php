<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DualSheetExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new MeasurementsExport($this->filters),
            new SummaryExport($this->filters),
        ];
    }
}
