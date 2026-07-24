<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $expenses = Expense::orderBy('expense_date', 'desc')->get();
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Expense::create($request->all());
        return redirect()->route('admin.expenses.index')->with('success', 'Direct Expense created successfully.');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $expense = Expense::findOrFail($id);
        return view('admin.expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $expense = Expense::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expense->update($request->all());
        return redirect()->route('admin.expenses.index')->with('success', 'Direct Expense updated successfully.');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Direct Expense deleted successfully.');
    }
}
