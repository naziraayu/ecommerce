<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::select(
            'code',
            'category_id',
            'name',
            'description',
            'price',
            'stock', // Sesuaikan dengan field di database (quantity -> stock)
        )->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Category ID',
            'Name',
            'Description',
            'Price',
            'Stock', // Sesuaikan dengan field di database
        ];
    }
}