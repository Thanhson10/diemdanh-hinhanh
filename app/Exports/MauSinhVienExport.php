<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MauSinhVienExport implements FromArray, WithEvents
{
    public function array(): array
    {
        return [
            ['Danh sách sinh viên lớp D'],
            ['MSSV', 'Họ', 'Tên', 'Lớp'],
            ['DH52200314', 'Trần', 'Huy An', 'D22_TH15'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {
                // Merge dòng 1
                $event->sheet->mergeCells('A1:D1');

                // Bold header
                $event->sheet->getStyle('A2:D2')->getFont()->setBold(true);
            },
        ];
    }
}