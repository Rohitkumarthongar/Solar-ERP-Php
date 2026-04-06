<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintFormat;
use App\Support\PrintFormatPresets;
use Illuminate\Http\Request;

class PrintFormatController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $formats = PrintFormat::orderBy('document_type')->get();
        return view('admin.settings.print-formats', compact('formats'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $presets = PrintFormatPresets::all();

        return view('admin.settings.print-format-create', compact('presets'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string',
            'document_type' => 'required|in:quotation,sales_order,purchase_order,invoice,salary_slip,discom_application,work_application,dcr_form',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            'body_template' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'paper_size' => 'required|in:A4,A5,Letter',
            'orientation' => 'required|in:portrait,landscape'
        ]);
        if ($request->has('is_default')) {
            PrintFormat::where('document_type', $validated['document_type'])->update(['is_default' => false]);
        }
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');
        PrintFormat::create($validated);
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format created!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $format = PrintFormat::findOrFail($id);
        $presets = PrintFormatPresets::all();

        return view('admin.settings.print-format-edit', compact('format', 'presets'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $format = PrintFormat::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'document_type' => 'required|in:quotation,sales_order,purchase_order,invoice,salary_slip,discom_application,work_application,dcr_form',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            'body_template' => 'required|string',
            'paper_size' => 'required|in:A4,A5,Letter',
            'orientation' => 'required|in:portrait,landscape'
        ]);
        if ($request->has('is_default')) {
            PrintFormat::where('document_type', $validated['document_type'])->where('id', '!=', $id)->update(['is_default' => false]);
        }
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');
        $format->update($validated);
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        PrintFormat::findOrFail($id)->delete();
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format deleted!');
    }
}
