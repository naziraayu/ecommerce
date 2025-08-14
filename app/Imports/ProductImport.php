<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi kategori
            $category = Category::find($row['category_id']);
            if (!$category) {
                continue;
            }

            // Generate code jika tidak ada
            $code = $row['code'] ?? 'PRD-' . Str::random(6);

            $product = Product::create([
                'code' => $code,
                'name' => $row['name'],
                'description' => $row['description'] ?? '',
                'price' => $row['price'],
                'stock' => $row['stock'] ?? 0, // Sesuaikan dengan field di database
                'category_id' => $row['category_id'],
                'status' => $row['status'] ?? 'active',
            ]);

            // Tambahkan gambar dummy
            $this->addDummyImage($product);
        }
    }

    protected function addDummyImage($product)
    {
        $dummyImagePath = 'dummy-product.jpg';
        
        if (Storage::disk('public')->exists($dummyImagePath)) {
            $newFilename = 'products/' . Str::random(20) . '.jpg';
            Storage::disk('public')->copy($dummyImagePath, $newFilename);
            
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $newFilename
            ]);
        }
    }
}