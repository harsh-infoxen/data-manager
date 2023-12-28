<?php

namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $expectedFields = ['first_name', 'last_name', 'email'];
        $missingFields = $expectedFields;

        foreach ($rows->first() as $key => $value) {
            $index = array_search($key, $missingFields);
            if ($index !== false) {
                unset($missingFields[$index]);
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception('Missing fields: ' . implode(', ', $missingFields));
        }
    }
}