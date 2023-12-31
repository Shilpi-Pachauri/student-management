<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Collection;

class StudentsExport implements FromCollection
{
    use Exportable;
    
    public function __construct(public Collection $records)
    {
        //
    }

    public function collection()
    {
       return $this->records;
    }
}
