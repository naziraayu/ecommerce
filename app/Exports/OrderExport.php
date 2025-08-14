<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Order::with(['user', 'items.product'])->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer',
            'Email',
            'Total Price',
            'Status',
            'Payment Type',
            'Payment Status',
            'Midtrans Transaction ID',
            'Items',
            'Order Date',
            'Updated Date'
        ];
    }

    public function map($order): array
    {
        // Format items: product_name (quantity), product_name (quantity)
        $items = $order->items->map(function ($item) {
            return $item->product->name . ' (' . $item->quantity . ')';
        })->implode(', ');

        return [
            $order->id,
            $order->user->name ?? 'N/A',
            $order->user->email ?? 'N/A',
            'Rp ' . number_format($order->total_price, 0, ',', '.'),
            ucfirst($order->status),
            $order->payment_type ?? 'N/A',
            ucfirst($order->payment_status ?? 'N/A'),
            $order->midtrans_transaction_id ?? 'N/A',
            $items,
            $order->created_at->format('d/m/Y H:i'),
            $order->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']]
            ],
            // Style untuk kolom amount
            'D' => ['alignment' => ['horizontal' => 'right']],
            // Style untuk tanggal
            'J:K' => ['numberFormat' => ['formatCode' => 'dd/mm/yyyy hh:mm']]
        ];
    }
}