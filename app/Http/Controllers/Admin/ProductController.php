<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Inventory;
use App\Support\SupabaseStorage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $products = Product::withCount('inventories')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category' => 'required|in:solar_panel,inverter,battery,mounting,cable,other',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'warranty_months' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = SupabaseStorage::store($request->file('image'), 'products');
        }
        $validated['is_active'] = $request->has('is_active');
        $product = Product::create($validated);
        Inventory::create(['product_id' => $product->id, 'quantity' => 0, 'min_quantity' => 5]);
        return redirect()->route('admin.products.index')->with('success', 'Product created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $product = Product::with('inventories')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'warranty_months' => 'nullable|integer'
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = SupabaseStorage::store($request->file('image'), 'products');
        }
        $validated['is_active'] = $request->has('is_active');
        $product->update($validated);
        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Product::findOrFail($id)->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted!');
    }
}
