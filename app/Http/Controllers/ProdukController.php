<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\StoreProdukFotoRequest;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\FotoProduk;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class ProdukController extends Controller
{
    private const PRODUCT_IMAGE_DIRECTORY = 'storage/img-produk/';
    private const PRODUCT_VARIANT_PREFIXES = ['', 'thumb_lg_', 'thumb_md_', 'thumb_sm_'];

    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q'));

        $produk = Produk::query()
            ->select(['id', 'kategori_id', 'status', 'nama_produk', 'harga', 'stok', 'updated_at'])
            ->with(['kategori:id,nama_kategori'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery->where('nama_produk', 'like', '%' . $keyword . '%')
                        ->orWhere('detail', 'like', '%' . $keyword . '%')
                        ->orWhereHas('kategori', function ($kategoriQuery) use ($keyword) {
                            $kategoriQuery->where('nama_kategori', 'like', '%' . $keyword . '%');
                        });
                });
            })
            ->orderByDesc('updated_at')
            ->simplePaginate(10)
            ->withQueryString();

        return view('backend.v_produk.index', [
            'judul' => 'Data Produk',
            'index' => $produk,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        $kategori = Kategori::query()
            ->select(['id', 'nama_kategori'])
            ->orderBy('nama_kategori')
            ->get();

        return view('backend.v_produk.create', [
            'judul' => 'Tambah Produk',
            'kategori' => $kategori,
            'usesCkeditor' => true,
        ]);
    }

    public function store(StoreProdukRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = 0;

        $uploadedMainPhoto = null;

        try {
            if ($request->hasFile('foto')) {
                $uploadedMainPhoto = $this->storePrimaryImage($request->file('foto'));
                $validatedData['foto'] = $uploadedMainPhoto;
            }

            DB::transaction(function () use ($validatedData) {
                Produk::create($validatedData);
            });
        } catch (Throwable $exception) {
            if ($uploadedMainPhoto) {
                $this->deleteProductImageVariants($uploadedMainPhoto);
            }

            throw $exception;
        }

        return redirect()->route('backend.produk.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data produk berhasil tersimpan.'
        ));
    }

    public function show(Produk $produk)
    {
        $produk->loadMissing('kategori:id,nama_kategori');

        $kategori = Kategori::query()
            ->select(['id', 'nama_kategori'])
            ->orderBy('nama_kategori')
            ->get();

        return view('backend.v_produk.show', [
            'judul' => 'Detail Produk',
            'show' => $produk,
            'kategori' => $kategori,
            'galleryUrl' => route('backend.produk.gallery', $produk),
        ]);
    }

    public function edit(Produk $produk)
    {
        $kategori = Kategori::query()
            ->select(['id', 'nama_kategori'])
            ->orderBy('nama_kategori')
            ->get();

        return view('backend.v_produk.edit', [
            'judul' => 'Ubah Produk',
            'edit' => $produk,
            'kategori' => $kategori,
            'usesCkeditor' => true,
        ]);
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();

        $oldMainPhoto = $produk->foto;
        $uploadedMainPhoto = null;

        try {
            if ($request->hasFile('foto')) {
                $uploadedMainPhoto = $this->storePrimaryImage($request->file('foto'));
                $validatedData['foto'] = $uploadedMainPhoto;
            }

            DB::transaction(function () use ($produk, $validatedData) {
                $produk->update($validatedData);
            });

            if ($uploadedMainPhoto && $oldMainPhoto) {
                $this->deleteProductImageVariants($oldMainPhoto);
            }
        } catch (Throwable $exception) {
            if ($uploadedMainPhoto) {
                $this->deleteProductImageVariants($uploadedMainPhoto);
            }

            throw $exception;
        }

        return redirect()->route('backend.produk.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data produk berhasil diperbarui.'
        ));
    }

    public function destroy(Produk $produk)
    {
        $mainPhoto = $produk->foto;
        $galleryPhotos = $produk->fotoProduk()->pluck('foto')->all();

        $produk->delete();

        if ($mainPhoto) {
            $this->deleteProductImageVariants($mainPhoto);
        }

        $this->deleteProductGalleryImages($galleryPhotos);

        return redirect()->route('backend.produk.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data produk berhasil dihapus.'
        ));
    }

    public function storeFoto(StoreProdukFotoRequest $request)
    {
        $uploadedGalleryPhotos = [];
        $produkId = (int) $request->input('produk_id');

        DB::beginTransaction();

        try {
            foreach ($request->file('foto_produk', []) as $file) {
                $filename = $this->storeGalleryImage($file);
                $uploadedGalleryPhotos[] = $filename;

                FotoProduk::create([
                    'produk_id' => $produkId,
                    'foto' => $filename,
                ]);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->deleteProductGalleryImages($uploadedGalleryPhotos);

            throw $exception;
        }

        return redirect()->route('backend.produk.show', $produkId)
            ->with('alert', $this->modalAlert(
                'success',
                'Berhasil',
                'Foto produk berhasil ditambahkan.'
            ));
    }

    public function destroyFoto(int $id)
    {
        $foto = FotoProduk::findOrFail($id);
        $produkId = $foto->produk_id;
        $filename = $foto->foto;

        DB::transaction(function () use ($foto) {
            $foto->delete();
        });

        $this->deleteProductGalleryImages([$filename]);

        return redirect()->route('backend.produk.show', $produkId)
            ->with('alert', $this->modalAlert(
                'success',
                'Berhasil',
                'Foto produk berhasil dihapus.'
            ));
    }

    public function gallery(Produk $produk)
    {
        $fotoProduk = $produk->fotoProduk()
            ->select(['id', 'produk_id', 'foto'])
            ->latest('id')
            ->get();

        return view('backend.v_produk.partials.gallery', [
            'fotoProduk' => $fotoProduk,
        ]);
    }

    private function storePrimaryImage(UploadedFile $file): string
    {
        $fileName = $this->generateImageName($file);

        ImageHelper::uploadAndResize($file, self::PRODUCT_IMAGE_DIRECTORY, $fileName);
        ImageHelper::uploadAndResize($file, self::PRODUCT_IMAGE_DIRECTORY, 'thumb_lg_' . $fileName, 800, null);
        ImageHelper::uploadAndResize($file, self::PRODUCT_IMAGE_DIRECTORY, 'thumb_md_' . $fileName, 500, 519);
        ImageHelper::uploadAndResize($file, self::PRODUCT_IMAGE_DIRECTORY, 'thumb_sm_' . $fileName, 100, 110);

        return $fileName;
    }

    private function storeGalleryImage(UploadedFile $file): string
    {
        $fileName = $this->generateImageName($file);

        ImageHelper::uploadAndResize($file, self::PRODUCT_IMAGE_DIRECTORY, $fileName, 800, null);

        return $fileName;
    }

    private function generateImageName(UploadedFile $file): string
    {
        return now()->format('YmdHis') . '_' . str()->lower(str()->random(10)) . '.' . strtolower($file->getClientOriginalExtension());
    }

    private function deleteProductImageVariants(string $fileName): void
    {
        foreach (self::PRODUCT_VARIANT_PREFIXES as $prefix) {
            $this->deletePublicFile(self::PRODUCT_IMAGE_DIRECTORY . $prefix . $fileName);
        }
    }

    /**
     * @param  array<int, string|null>  $fileNames
     */
    private function deleteProductGalleryImages(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            if (!$fileName) {
                continue;
            }

            $this->deletePublicFile(self::PRODUCT_IMAGE_DIRECTORY . $fileName);
        }
    }

    private function deletePublicFile(string $relativePath): void
    {
        $absolutePath = public_path($relativePath);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
