<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mou;
use App\Models\MouRenewal;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RenewalController extends Controller
{
    public function create(Mou $mou)
    {
        $mou->load(['institution', 'renewals']);
        return view('admin.mou.renewal', compact('mou'));
    }

    public function store(Request $request, Mou $mou)
    {
        $validated = $request->validate([
            'new_start_date' => 'required|date',
            'new_end_date' => 'required|date|after:new_start_date',
            'renewal_note' => 'nullable|string|max:1000',
            'new_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        // Proteksi: cegah input duplikat dengan tanggal periode yang sama persis
        $alreadyExists = $mou->renewals()
            ->where('new_start_date', $validated['new_start_date'])
            ->where('new_end_date', $validated['new_end_date'])
            ->exists();

        if ($alreadyExists) {
            return back()->withInput()->with('error', 'Perpanjangan dengan periode tanggal tersebut sudah pernah dicatat.');
        }

        return DB::transaction(function () use ($request, $validated, $mou) {
            $renewalNumber = ($mou->renewals()->max('renewal_number') ?? 0) + 1;

            $renewalData = [
                'mou_id' => $mou->id,
                'renewal_number' => $renewalNumber,
                'old_start_date' => $mou->start_date,
                'old_end_date' => $mou->end_date,
                'new_start_date' => $validated['new_start_date'],
                'new_end_date' => $validated['new_end_date'],
                'duration_months' => Carbon::parse($validated['new_start_date'])->diffInMonths(Carbon::parse($validated['new_end_date'])),
                'renewal_note' => $validated['renewal_note'] ?? null,
                'old_file' => $mou->main_document,
                'renewed_by' => auth()->guard('admin')->id(),
            ];

            if ($request->hasFile('new_file')) {
                $file = $request->file('new_file');
                $fileName = 'mou_renewal_' . Str::slug($mou->mou_number) . '_v' . $renewalNumber . '_' . time() . '.' . $file->getClientOriginalExtension();
                $renewalData['new_file'] = $file->storeAs('mou-documents/renewals', $fileName, 'public');

                // Update main document
                $mou->main_document = $renewalData['new_file'];
            }

            MouRenewal::create($renewalData);

            // Update MoU
            $mou->update([
                'start_date' => $validated['new_start_date'],
                'end_date' => $validated['new_end_date'],
                'duration_months' => $renewalData['duration_months'],
                'renewal_count' => $renewalNumber,
                'status' => 'aktif',
                'updated_by' => auth()->guard('admin')->id(),
            ]);

            ActivityLogService::log('renewal', $mou, "MoU '{$mou->title}' diperpanjang (Perpanjangan ke-{$renewalNumber})");

            return redirect()->route('admin.mou.show', $mou)->with('success', 'MoU berhasil diperpanjang (Perpanjangan ke-' . $renewalNumber . ').');
        });
    }

    public function history(Mou $mou)
    {
        $mou->load(['renewals.renewedByAdmin', 'institution']);
        return view('admin.mou.renewal-history', compact('mou'));
    }

    public function destroy(Mou $mou, MouRenewal $renewal)
    {
        if ($renewal->mou_id !== $mou->id) {
            abort(404);
        }

        DB::transaction(function () use ($mou, $renewal) {
            if ($renewal->new_file && Storage::disk('public')->exists($renewal->new_file)) {
                Storage::disk('public')->delete($renewal->new_file);
            }

            $renewalNum = $renewal->renewal_number;
            $renewal->delete();

            // Kembalikan status MoU ke perpanjangan terbaru yang tersisa atau ke awal
            $latestRemaining = $mou->renewals()->orderBy('renewal_number', 'desc')->first();

            if ($latestRemaining) {
                $mou->update([
                    'start_date' => $latestRemaining->new_start_date,
                    'end_date' => $latestRemaining->new_end_date,
                    'duration_months' => $latestRemaining->duration_months,
                    'renewal_count' => $mou->renewals()->count(),
                    'main_document' => $latestRemaining->new_file ?? $latestRemaining->old_file ?? $mou->main_document,
                    'status' => now()->gt($latestRemaining->new_end_date) ? 'expire' : (now()->diffInDays($latestRemaining->new_end_date, false) <= 90 ? 'akan_expire' : 'aktif'),
                    'updated_by' => auth()->guard('admin')->id(),
                ]);
            } else {
                $mou->update([
                    'start_date' => $renewal->old_start_date,
                    'end_date' => $renewal->old_end_date,
                    'duration_months' => Carbon::parse($renewal->old_start_date)->diffInMonths(Carbon::parse($renewal->old_end_date)),
                    'renewal_count' => 0,
                    'main_document' => $renewal->old_file ?? $mou->main_document,
                    'status' => now()->gt($renewal->old_end_date) ? 'expire' : (now()->diffInDays($renewal->old_end_date, false) <= 90 ? 'akan_expire' : 'aktif'),
                    'updated_by' => auth()->guard('admin')->id(),
                ]);
            }

            ActivityLogService::log('delete', $mou, "Histori perpanjangan ke-{$renewalNum} dihapus untuk MoU '{$mou->title}'");
        });

        return back()->with('success', 'Histori perpanjangan berhasil dihapus.');
    }
}
