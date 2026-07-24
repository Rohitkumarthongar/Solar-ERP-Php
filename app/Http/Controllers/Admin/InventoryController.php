<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $inventories = Inventory::with('product')->orderBy('quantity')->paginate(15);
        $lowStock = Inventory::with('product')->whereRaw('quantity <= min_quantity')->get();
        return view('admin.inventory.index', compact('inventories', 'lowStock'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $products = Product::where('is_active', true)->whereDoesntHave('inventories')->get();
        return view('admin.inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id|unique:inventories,product_id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:100'
        ]);
        Inventory::create($validated);
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $inventory = Inventory::with('product')->findOrFail($id);
        return view('admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $inventory = Inventory::findOrFail($id);
        $validated = $request->validate([
            'min_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:100'
        ]);
        $inventory->update($validated);
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory updated!');
    }

    public function adjust()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $products = Product::whereHas('inventories')->with('inventories')->get();
        return view('admin.inventory.adjust', compact('products'));
    }

    public function adjustStore(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        $inventory = Inventory::where('product_id', $validated['product_id'])->firstOrFail();
        $before = $inventory->quantity;

        if ($validated['adjustment_type'] === 'add') {
            $inventory->increment('quantity', $validated['quantity']);
        } elseif ($validated['adjustment_type'] === 'remove') {
            $inventory->decrement('quantity', min($validated['quantity'], $inventory->quantity));
        } else {
            $inventory->update(['quantity' => $validated['quantity']]);
        }

        InventoryAdjustment::create([
            'inventory_id' => $inventory->id,
            'product_id' => $validated['product_id'],
            'adjustment_type' => $validated['adjustment_type'],
            'quantity_before' => $before,
            'quantity_adjusted' => $validated['quantity'],
            'quantity_after' => $inventory->fresh()->quantity,
            'reason' => $validated['reason'],
            'adjusted_by' => session('admin_user')
        ]);

        if ($inventory->fresh()->quantity <= $inventory->min_quantity) {
            Notification::create([
                'title' => 'Low Stock Alert',
                'message' => $inventory->product->name . ' stock is below minimum level. Current: ' . $inventory->fresh()->quantity,
                'type' => 'inventory', 'related_id' => $inventory->id, 'related_type' => 'Inventory'
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory adjusted!');
    }
}