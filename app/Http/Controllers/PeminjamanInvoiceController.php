<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanAlat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PeminjamanInvoiceController extends Controller
{
    public function __invoke(PeminjamanAlat $peminjaman)
    {
        // Authz: admin/super_admin atau pemilik data
        $user = auth()->user();
        abort_unless(
            $user && ($user->hasAnyRole(['admin', 'super_admin']) || $peminjaman->user_id === $user->id),
            403
        );

        $peminjaman->loadMissing(['user', 'items.peralatan']);

        $filename = 'INV-PJ-' . str_pad((string)$peminjaman->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        $pdf = Pdf::loadView('pdf.peminjaman_invoice', [
            'record' => $peminjaman,
        ])->setPaper('a4', 'portrait');

        // Unduh file (kalau mau tampil di tab, ganti ->download jadi ->stream)
        return $pdf->download($filename);
    }
}
