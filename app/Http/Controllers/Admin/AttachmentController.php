<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Mou;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request, Mou $mou)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx|max:20480',
            'description' => 'nullable|string|max:255',
        ]);

        $uploaded = 0;

        foreach ($request->file('files') as $file) {
            $fileName = Str::slug($mou->mou_number) . '_attachment_' . time() . '_' . ($uploaded + 1) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mou-attachments/' . $mou->id, $fileName, 'public');

            Attachment::create([
                'mou_id' => $mou->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $request->description,
                'uploaded_by' => auth()->guard('admin')->id(),
            ]);

            $uploaded++;
        }

        ActivityLogService::log('upload', $mou, "{$uploaded} file attachment diupload untuk MoU '{$mou->title}'");

        return back()->with('success', "{$uploaded} file berhasil diupload.");
    }

    public function download(Attachment $attachment)
    {
        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);

        ActivityLogService::log('delete', $attachment->mou, "Attachment '{$attachment->original_name}' dihapus");

        $attachment->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}
