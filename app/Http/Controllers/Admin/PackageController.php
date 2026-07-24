<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $packages = Package::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $products = Product::where('is_active', true)->get();
        return view('admin.packages.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'system_size_kw' => 'required|numeric',
            'price' => 'required|numeric|min:0',
            'suitable_for' => 'required|in:residential,commercial,industrial',
            'includes' => 'nullable|string',
            'warranty_years' => 'nullable|integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        if ($request->items) {
            $validated['items'] = $request->items;
        }
        Package::create($validated);
        return redirect()->route('admin.packages.index')->with('success', 'Package created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $package = Package::findOrFail($id);
        return view('admin.packages.show', compact('package'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $package = Package::findOrFail($id);
        $products = Product::where('is_active', true)->get();
        return view('admin.packages.edit', compact('package', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $package = Package::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'system_size_kw' => 'required|numeric',
            'price' => 'required|numeric|min:0',
            'suitable_for' => 'required|string',
            'includes' => 'nullable|string',
            'warranty_years' => 'nullable|integer'
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        if ($request->items) {
            $validated['items'] = $request->items;
        } else {
            $validated['items'] = null; // Clear items if none provided
        }
        $package->update($validated);
        return redirect()->route('admin.packages.index')->with('success', 'Package updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Package::findOrFail($id)->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted!');
    }
}