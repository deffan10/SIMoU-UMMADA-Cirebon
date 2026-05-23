@extends('layouts.admin')
@section('title', 'Log Import')

@section('content')
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3 text-left">File</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Sukses</th><th class="px-4 py-3">Gagal</th><th class="px-4 py-3">Duplikat</th><th class="px-4 py-3">Tanggal</th></tr></thead>
        <tbody class="divide-y">
            @foreach($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">{{ $log->file_name }}<br><span class="text-xs text-gray-400">{{ $log->admin?->name }}</span></td>
                <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs {{ $log->status=='completed'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">{{ $log->status }}</span></td>
                <td class="px-4 py-3 text-center">{{ $log->total_rows }}</td>
                <td class="px-4 py-3 text-center text-green-600">{{ $log->success_count }}</td>
                <td class="px-4 py-3 text-center text-red-600">{{ $log->failed_count }}</td>
                <td class="px-4 py-3 text-center text-yellow-600">{{ $log->duplicate_count }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection
