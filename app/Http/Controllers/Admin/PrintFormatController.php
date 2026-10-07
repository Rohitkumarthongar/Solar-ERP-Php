<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintFormat;
use App\Support\PrintFormatPresets;
use App\Support\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrintFormatController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $formats = PrintFormat::orderBy('document_type')->orderBy('id')->get();
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
            'name'          => 'required|string',
            'document_type' => 'required|in:quotation,sales_order,purchase_order,invoice,salary_slip,discom_application,work_application,dcr_form,installation_certificate,service_report,site_visit_report',
            'header_html'   => 'nullable|string',
            'footer_html'   => 'nullable|string',
            'body_template' => 'required|string',
            'paper_size'    => 'required|in:A4,A5,Letter',
            'orientation'   => 'required|in:portrait,landscape',
            'is_default'    => 'sometimes|boolean',
            'is_active'     => 'sometimes|boolean',
            'images.*'      => 'nullable|image|max:4096',
        ]);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['is_default'] && !$validated['is_active']) {
            return back()->withErrors(['is_default' => 'A default print format must be active.'])->withInput();
        }
        $validated['images']     = $this->handleImageUploads($request, []);
        DB::transaction(function () use ($validated) {
            if ($validated['is_default']) {
                PrintFormat::where('document_type', $validated['document_type'])->lockForUpdate()->get();
                PrintFormat::where('document_type', $validated['document_type'])->update(['is_default' => false]);
            }
            PrintFormat::create($validated);
        });
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format created!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $format  = PrintFormat::findOrFail($id);
        $presets = PrintFormatPresets::all();

        // Pre-populate default printformat images if none uploaded yet
        if (empty($format->images)) {
            $base = 'https://miurrqqeervypgieqxzy.supabase.co/storage/v1/object/public/Solar_Ptc/printformat/';
            $format->images = [
                ['key' => 'quotation_cover_house',  'label' => 'Quotation Cover House',  'url' => $base . 'quotation-cover-house.png',  'path' => 'printformat/quotation-cover-house.png'],
                ['key' => 'quotation_handshake',     'label' => 'Quotation Handshake',     'url' => $base . 'quotation-handshake.png',     'path' => 'printformat/quotation-handshake.png'],
                ['key' => 'quotation_roof_banner',   'label' => 'Quotation Roof Banner',   'url' => $base . 'quotation-roof-banner.png',   'path' => 'printformat/quotation-roof-banner.png'],
            ];
        }

        return view('admin.settings.print-format-edit', compact('format', 'presets'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $format    = PrintFormat::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string',
            'document_type' => 'required|in:quotation,sales_order,purchase_order,invoice,salary_slip,discom_application,work_application,dcr_form,installation_certificate,service_report,site_visit_report',
            'header_html'   => 'nullable|string',
            'footer_html'   => 'nullable|string',
            'body_template' => 'required|string',
            'paper_size'    => 'required|in:A4,A5,Letter',
            'orientation'   => 'required|in:portrait,landscape',
            'images.*'      => 'nullable|image|max:4096',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['is_default'] && !$validated['is_active']) {
            return back()->withErrors(['is_default' => 'A default print format must be active.'])->withInput();
        }
        $validated['images']     = $this->handleImageUploads($request, $format->images ?? []);
        DB::transaction(function () use ($format, $validated) {
            if ($validated['is_default']) {
                PrintFormat::where('document_type', $validated['document_type'])->lockForUpdate()->get();
                PrintFormat::where('document_type', $validated['document_type'])->where('id', '!=', $format->id)->update(['is_default' => false]);
            }
            $format->update($validated);
        });
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        PrintFormat::findOrFail($id)->delete();
        return redirect()->route('admin.settings.print-formats')->with('success', 'Print format deleted!');
    }

    private function handleImageUploads(Request $request, array $existing): array
    {
        // Keep only images not unchecked
        $keep   = $request->input('keep_images', []);
        $kept   = array_filter($existing, fn($img) => in_array($img['key'], array_column($keep, 'key')));
        $images = array_values($kept);

        // Upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $idx => $file) {
                $label    = $request->input("image_labels.$idx", 'image_' . ($idx + 1));
                $path     = SupabaseStorage::store($file, 'printformat');
                $images[] = [
                    'key'   => Str::slug($label) ?: 'image_' . count($images),
                    'label' => $label,
                    'url'   => SupabaseStorage::url($path),
                    'path'  => $path,
                ];
            }
        }

        return $images;
    }
}
