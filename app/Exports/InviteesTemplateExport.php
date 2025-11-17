<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InviteesTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['John Smith', 'john@example.com', '+1234567890', 'ABC Corp'],
            ['Sarah Johnson', 'sarah@example.com', '+1234567891', 'XYZ Inc'],
            ['Michael Brown', 'michael@example.com', '+1234567892', 'Tech Solutions'],
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Company'
        ];
    }
}