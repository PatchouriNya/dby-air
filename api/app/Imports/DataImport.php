<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DataImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    private $rows;
    private $skip;

    public function __construct($skip = 0)
    {
        $this->skip = $skip;
    }

    public function collection(Collection $collection)
    {

        $this->rows = $collection->slice($this->skip);
        //        return $collection;
    }

    public function getRows()
    {
        return $this->rows;
    }
}
