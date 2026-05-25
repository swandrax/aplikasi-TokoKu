@extends('layouts.admin')

@section('header_title', 'Manajemen Chatbot Prompts')

@section('content')
<div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-extrabold text-slate-800 text-lg">Daftar Quick Prompts</h3>
            <p class="text-xs text-slate-500 mt-1">Kelola saran pertanyaan cepat yang muncul di widget KikiBot.</p>
        </div>
        <a href="{{ route('admin.chatbot-prompts.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-2">
            <span>➕</span> Tambah Prompt
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider">
                    <th class="p-4 rounded-tl-xl font-bold">ID</th>
                    <th class="p-4 font-bold">Judul (Singkat)</th>
                    <th class="p-4 font-bold">Teks Lengkap (Prompt)</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 rounded-tr-xl font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($prompts as $prompt)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-bold text-slate-400">#{{ $prompt->id }}</td>
                        <td class="p-4 font-bold text-slate-700">{{ $prompt->title }}</td>
                        <td class="p-4 text-xs text-slate-500 max-w-xs truncate">{{ $prompt->prompt_text }}</td>
                        <td class="p-4">
                            @if($prompt->is_active)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-bold uppercase tracking-wider">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-bold uppercase tracking-wider">Nonaktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.chatbot-prompts.edit', $prompt->id) }}" class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Edit">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.chatbot-prompts.destroy', $prompt->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus prompt ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm">Belum ada prompt chatbot yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $prompts->links() }}
    </div>
</div>
@endsection
