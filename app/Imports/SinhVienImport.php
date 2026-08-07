<?php

namespace App\Imports;

use App\Models\SinhVien;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SinhVienImport implements
    ToModel,
    WithStartRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public function startRow(): int
    {
        return 3;
    }

    public function prepareForValidation($data, $index)
    {
        $data[0] = strtoupper(trim($data[0] ?? ''));
        $data[3] = trim($data[3] ?? '');

        return $data;
    }

    public function model(array $row)
    {
        $maSv = $row[0];
        $lop  = strtoupper($row[3]);

        return new SinhVien([
            'ma_sv'  => $maSv,
            'ho_ten' => trim($row[1] . ' ' . $row[2]),
            'lop'    => $lop,
            'email'  => strtolower($maSv) . '@student.stu.edu.vn',
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => [
                'required',
                Rule::unique('sinh_viens', 'ma_sv'),
            ],
            '3' => 'required',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '0' => 'MSSV',
            '3' => 'Lớp',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'MSSV không được để trống',
            '0.unique'   => 'MSSV đã tồn tại trong hệ thống',
            '3.required' => 'Lớp không được để trống',
        ];
    }
}
