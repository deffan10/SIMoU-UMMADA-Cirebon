@extends('layouts.admin')
@section('title', 'Notifikasi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Notifikasi & reminder kerjasama</p>
    <form action="{{ route('admin.notifications.readAll') }}" method="POST">@csrf<button class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50">Tandai Semua Dibaca</button></form>
</div>
<div class="space-y-3">
    @forelse($notifications as $notif)
    <div class="bg-white rounded-xl shadow-sm border p-4 flex items-start justify-between {{ !$notif->is_read ? 'border-l-4 border-l-blue-500' : '' }}">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ !$notif->is_read ? 'bg-blue-100' : 'bg-gray-100' }}">
                <i class="fas {{ $notif->type == 'expired' ? 'fa-times-circle text-red-500' : ($notif->type == 'renewal' ? 'fa-sync text-purple-500' : 'fa-exclamation-triangle text-yellow-500') }}"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $notif->title }}</p>
                <p class="text-sm text-gray-600">{{ $notif->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @if(!$notif->is_read)
        <form action="{{ route('admin.notifications.read', $notif) }}" method="POST">@csrf<button class="text-xs text-blue-600 hover:underline">Dibaca</button></form>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-gray-400"><i class="fas fa-bell-slash text-4xl mb-3"></i><p>Tidak ada notifikasi.</p></div>
    @endforelse
</div>
<div class="mt-6">{{ $notifications->links() }}</div>
@endsection
