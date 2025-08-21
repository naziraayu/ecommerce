<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Ambil hanya user dengan role 'user'
     */
    public function collection()
    {
        return User::where('role', 'user')
            ->with('roleData')
            ->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Role',
            'Status',
            'Alamat',
            'Nomor Telepon',
            'Tanggal Daftar',
            'Terakhir Login'
        ];
    }

    /**
     * Mapping data tiap user ke kolom Excel
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role_name, // accessor dari model User
            $user->email_verified_at ? 'Aktif' : 'Tidak Aktif',
            $user->address ?? '-',
            $user->phone_number ?? '-',
            $user->created_at->format('d/m/Y H:i'),
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Belum pernah login'
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style header (A1 sampai I1 karena ada 9 kolom)
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Auto width semua kolom A–I
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style isi data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:I'.$highestRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Warna selang-seling
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
                ]);
            }
        }

        return [];
    }

    /**
     * Nama sheet
     */
    public function title(): string
    {
        return 'Data Users';
    }
}
