<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mou;
use App\Models\MouRenewal;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
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

        $renewalNumber = $mou->renewal_count + 1;

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

        ActivityLogService::log('renewal', $mou, "MoU '{$mou->title}' diperpanjang (Version {$renewalNumber})");

        return redirect()->route('admin.mou.show', $mou)->with('success', 'MoU berhasil diperpanjang.');
    }

    public function history(Mou $mou)
    {
        $mou->load(['renewals.renewedByAdmin', 'institution']);
        return view('admin.mou.renewal-history', compact('mou'));
    }
}
