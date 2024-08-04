<?php

namespace App\Exports;

use Throwable;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Models\Lead;

use Illuminate\Support\Facades\Schema;

class LeadsExport implements FromQuery, WithHeadings
{
    use Exportable;

    public $query = null;

    public function __construct($query = null)
    {
        if(is_null($query))
        {
            $query = Lead::query();
        }
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function failed(Throwable $exception): void
    {
        // handle failed export
    }

    public function headings(): array
    {
        return Schema::getColumnListing('leads');
    }
}
