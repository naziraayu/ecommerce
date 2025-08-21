<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Yajra\DataTables\DataTables;
use App\Notifications\NewProduct;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $code = 'PRD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $product = Product::create([
            'code' => $code,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'image' => $path
                ]);
            }
        }

        // Load relasi yang diperlukan
        $product->load('category');

        // Kirim notifikasi ke semua admin & superadmin
        $admins = User::whereHas('roleData', function($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewProduct($product));
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product->update($validatedData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images as $image) {
            Storage::delete($image->image);
            $image->delete();
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function getProductListApi(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Filter berdasarkan kategori
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Sort by harga
        if ($request->has('sort')) {
            $sort = $request->sort === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $sort);
        }

        $products = $query->get();

        // Format response untuk mobile
        $data = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'description' => $product->description,
                'category' => $product->category->name ?? null,
                'images' => $product->images->map(function ($img) {
                    return asset('storage/' . $img->image);
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $data
        ]);
    }

    public function export() 
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        
        Excel::import(new ProductImport, $request->file('file'));
        
        return redirect()->back()->with('success', 'Products imported successfully.');
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/product-template.xlsx');
        return response()->download($path);
    }
}