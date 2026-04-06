<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $categories = ProductCategory::withCount('products')->orderBy('sort_order')->paginate(15);
        return view('admin.product-categories.index', compact('categories'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.product-categories.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }
        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        ProductCategory::create($validated);
        return redirect()->route('admin.product-categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $category = ProductCategory::findOrFail($id);
        return view('admin.product-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $category = ProductCategory::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }
        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $category->update($validated);
        return redirect()->route('admin.product-categories.index')->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        ProductCategory::findOrFail($id)->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Category deleted!');
    }
}
