<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToCollection, WithHeadingRow
{
    // public function collection(Collection $rows)
    // {
    //     $expectedFields = ['first_name', 'last_name', 'email'];
    //     $missingFields = $expectedFields;
    //     $extraFields = [];

    //     foreach ($rows->first() as $key => $value) {
    //         $index = array_search($key, $missingFields);
    //         if ($index !== false) {
    //             unset($missingFields[$index]);
    //         }
    //     }

    //     if (!empty($missingFields)) {
    //         throw new \Exception('Missing fields: ' . implode(', ', $missingFields));
    //     }

    //     foreach ($rows->first() as $key => $value) {
    //         if (!in_array($key, $expectedFields)) {
    //             $extraFields[] = $key;
    //         }
    //     }


    //     if (!empty($extraFields)) {
    //         throw new \Exception('Extra fields found: ' . implode(', ', $extraFields));
    //     }
    // }


    public function collection(Collection $rows)
    {
        $expectedFields = ['first_name', 'last_name', 'email'];
        $missingFields = $extraFields = [];

        // Check for missing fields
        foreach ($expectedFields as $field) {
            if (!$rows->first()->has($field)) {
                $missingFields[] = $field;
            }
        }

        // Throw an exception if missing fields are found
        if (!empty($missingFields)) {
            throw new \Exception('Missing fields: ' . implode(', ', $missingFields));
        }

        // Check for extra fields
        foreach ($rows->first() as $key => $value) {
            if (!in_array($key, $expectedFields)) {
                $extraFields[] = $key;
            }
        }

        // Throw an exception if extra fields are found
        if (!empty($extraFields)) {
            throw new \Exception('Extra fields found: ' . implode(', ', $extraFields));
        }
    }
}