@extends('layouts.admin')

@section('header_title', 'Audit Trail - Log Aktivitas Sistem')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-50 pb-4">
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Log Aktivitas Keamanan & Otorisasi</h3>
            <p class="text-[10px] text-slate-400 mt-1">Daftar semua perubahan data, mutasi stok, dan aktivitas audit pengguna secara real-time.</p>
        </div>
    </div>

    <!-- Timeline Audit logs Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                    <th class="p-3 text-center">Waktu</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Aksi</th>
                    <th class="p-3">Deskripsi</th>
                    <th class="p-3 text-center">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3 text-slate-500 text-center font-semibold">
                            {{ $log->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                        </td>
                        <td class="p-3">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-650 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold block leading-none">{{ $log->user->name }}</span>
                                        <span class="text-[9px] text-slate-400 uppercase font-semibold">{{ $log->user->role }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 font-medium">Guest / System</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold text-[9px] uppercase tracking-wider rounded-lg border border-indigo-100/50">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-700 max-w-sm truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                        <td class="p-3 text-center text-slate-400 font-mono text-[10px]">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada log aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pt-4 border-t border-slate-50">
        {{ $logs->links() }}
    </div>
</div>
@endsection
