<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InviteesImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        // Normalize the column names to handle different cases
        return collect($array)->map(function ($row) {
            return [
                'name' => $row['name'] ?? $row['Name'] ?? $row['NAME'] ?? null,
                'email' => $row['email'] ?? $row['Email'] ?? $row['EMAIL'] ?? null,
                'phone' => $row['phone'] ?? $row['Phone'] ?? $row['PHONE'] ?? null,
                'company' => $row['company'] ?? $row['Company'] ?? $row['COMPANY'] ?? null,
            ];
        })->toArray();
    }

    public function headingRow(): int
    {
        return 1; // Assumes first row is headers
    }
}