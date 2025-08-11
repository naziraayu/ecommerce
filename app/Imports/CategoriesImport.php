<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Category([
            'code' => $row['code'] ?? 'CAT-' . str_pad(Category::max('id') + 1, 3, '0', STR_PAD_LEFT),
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
        ]);
    }
}
