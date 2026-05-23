@extends('layouts.admin')
@section('title', 'Activity Log')

@section('content')
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form class="flex flex-wrap gap-3">
        <select name="action" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Aksi</option>
            @foreach(['create','update','delete','restore','renewal','import','upload','login','logout'] as $a)
            <option value="{{ $a }}" {{ request('action')==$a?'selected':'' }}>{{ ucfirst($a) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm">
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3 text-left">Waktu</th><th class="px-4 py-3 text-left">Admin</th><th class="px-4 py-3 text-left">Aksi</th><th class="px-4 py-3 text-left">Deskripsi</th></tr></thead>
        <tbody class="divide-y">
            @foreach($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3">{{ $log->admin?->name ?? 'System' }}</td>
                <td class="px-4 py-3">{!! $log->action_badge !!}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ Str::limit($log->description, 60) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection
