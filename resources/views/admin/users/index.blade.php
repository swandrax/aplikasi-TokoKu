@extends('layouts.admin')

@section('header_title', 'Kelola Pengguna')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-50 pb-4">
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Daftar Akun Pengguna</h3>
            <p class="text-[10px] text-slate-400 mt-1">Daftarkan staff kasir, admin, atau aktifkan/nonaktifkan akun pelanggan online store.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all duration-300">
            + Registrasi Staff / User
        </a>
    </div>

    <!-- User List Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3 text-center">Role</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3 font-bold text-slate-800">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-sm shadow-inner font-extrabold text-slate-700">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="block">{{ $user->name }}</span>
                                    <span class="text-[9px] text-slate-400 leading-none">ID: #{{ $user->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-slate-650 font-medium">{{ $user->email }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : ($user->role === 'kasir' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-center flex items-center justify-center gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg font-bold text-[10px] transition-colors">
                                Edit
                            </a>
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-bold text-[10px] transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-400 rounded-lg font-bold text-[10px] cursor-not-allowed">
                                    Diri Sendiri
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pt-4 border-t border-slate-50">
        {{ $users->links() }}
    </div>
</div>
@endsection
