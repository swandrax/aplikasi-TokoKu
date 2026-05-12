<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q'));

        $kategori = Kategori::query()
            ->select(['id', 'nama_kategori'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('nama_kategori', 'like', '%' . $keyword . '%');
            })
            ->orderBy('nama_kategori')
            ->simplePaginate(10)
            ->withQueryString();

        return view('backend.v_kategori.index', [
            'judul' => 'Kategori',
            'index' => $kategori,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('backend.v_kategori.create', ['judul' => 'Kategori']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_kategori' => 'required|max:255|unique:kategori',
        ]);

        Kategori::create($validatedData);
        return redirect()->route('backend.kategori.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data kategori berhasil tersimpan.'
        ));
    }

    public function show(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return redirect()->route('backend.kategori.edit', $kategori->id);
    }

    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('backend.v_kategori.edit', [
            'judul' => 'Kategori',
            'edit'  => $kategori,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $rules = [
            'nama_kategori' => 'required|max:255|unique:kategori,nama_kategori,' . $id,
        ];

        $validatedData = $request->validate($rules);
        Kategori::where('id', $id)->update($validatedData);
        return redirect()->route('backend.kategori.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data kategori berhasil diperbarui.'
        ));
    }

    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return redirect()->route('backend.kategori.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data kategori berhasil dihapus.'
        ));
    }
}
