<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerDiscom;
use App\Models\Installation;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Storage;

class PrivateAttachmentController extends Controller
{
    private const PROOF_FIELDS = [
        'proof_before_photo', 'proof_during_photo', 'proof_after_photo', 'proof_meter_photo',
        'proof_panel_photo', 'proof_inverter_photo', 'structure_panel_photo', 'ground_setup_photo',
        'roof_setup_photo', 'panel_angle_photo', 'site_location_photo', 'wiring_photo',
        'meter_setup_photo', 'el_test_report', 'commissioning_report',
    ];

    public function purchaseInvoice(int $id, int $index)
    {
        $files = PurchaseOrder::findOrFail($id)->invoice_attachments ?? [];
        return $this->serve($files[$index] ?? null, 'purchase-invoices');
    }

    public function discomReport(int $id)
    {
        return $this->serve(CustomerDiscom::findOrFail($id)->dcr_report_path, 'discom-reports');
    }

    public function installationProof(int $id, string $field)
    {
        abort_unless(in_array($field, self::PROOF_FIELDS, true), 404);
        return $this->serve(Installation::findOrFail($id)->$field, 'installation-proofs');
    }

    public function installationPhoto(int $id, int $index)
    {
        $photos = Installation::findOrFail($id)->proof_photos ?? [];
        return $this->serve($photos[$index] ?? null, 'installation-proofs');
    }

    public function checklistPhoto(int $id, string $task)
    {
        $checklist = Installation::findOrFail($id)->installation_checklist ?? [];
        return $this->serve($checklist[$task]['photo'] ?? null, 'installation-proofs');
    }

    private function serve(?string $path, string $directory)
    {
        // Only server-created paths in the expected module directory can be read.
        abort_unless($path && preg_match('~^'.preg_quote($directory, '~').'/[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp|pdf)$~iD', $path), 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);
        return $disk->response($path, null, ['Content-Security-Policy' => "default-src 'none'; sandbox", 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }
}
