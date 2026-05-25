{{-- @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Category[] $categories --}}
@extends('layouts.admin')

@section('header_title', 'Kelola Kategori Produk')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-50 pb-4">
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Kategori Barang & Produk</h3>
            <p class="text-[10px] text-slate-400 mt-1">Grup dan kategorikan produk dagangan Anda untuk memudahkan pencarian pembeli.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all duration-300">
            + Tambah Kategori
        </a>
    </div>

    <!-- Category List Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                    <th class="p-3">Nama Kategori</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3">Deskripsi</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3 font-bold text-slate-800">{{ $category->name }}</td>
                        <td class="p-3 font-mono text-[10px] text-slate-500">{{ $category->slug }}</td>
                        <td class="p-3 text-slate-500 max-w-xs truncate" title="{{ $category->description }}">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $category->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-center flex items-center justify-center gap-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg font-bold text-[10px] transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-bold text-[10px] transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">Belum ada kategori terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pt-4 border-t border-slate-50">
        {{ $categories->links() }}
    </div>
</div>
@endsection
