<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function ensureAdminOnly(): void
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat mengelola data user.');
        }
    }

    private function ensureCanEdit(User $user): void
    {
        $authUser = Auth::user();

        if ($authUser->isAdmin()) {
            return;
        }

        if ($authUser->isUserAdmin() && (int) $authUser->id === (int) $user->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki hak akses untuk data user ini.');
    }

    public function index(Request $request)
    {
        $this->ensureAdminOnly();

        $keyword = trim((string) $request->query('q'));

        $user = User::query()
            ->select(['id', 'nama', 'email', 'hp', 'role', 'status', 'foto', 'updated_at'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery->where('nama', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('hp', 'like', '%' . $keyword . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->simplePaginate(10)
            ->withQueryString();

        return view('backend.v_user.index', [
            'judul' => 'Data User',
            'index' => $user,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        $this->ensureAdminOnly();

        return view('backend.v_user.create', [
            'judul' => 'Tambah User',
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdminOnly();

        $validatedData = $request->validate([
            'nama'     => 'required|max:255',
            'email'    => 'required|max:255|email|unique:user',
            'role'     => 'required|in:0,1,2',
            'hp'       => 'required|min:10|max:13',
            'password' => 'required|min:4|confirmed',
            'foto'     => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ], $messages = [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max'   => 'Ukuran file gambar Maksimal adalah 1024 KB.',
        ]);

        $validatedData['status'] = 0;

        if ($request->file('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-user/';
            ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400);
            $validatedData['foto'] = $originalFileName;
        }

        $password = $request->input('password');
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/';

        if (preg_match($pattern, $password)) {
            $validatedData['password'] = Hash::make($validatedData['password']);
            User::create($validatedData);
            return redirect()->route('backend.user.index')->with('alert', $this->modalAlert(
                'success',
                'Berhasil',
                'Data user berhasil tersimpan.'
            ));
        }

        return redirect()->back()->withErrors(['password' => 'Password harus terdiri dari kombinasi huruf besar, huruf kecil, angka, dan simbol karakter.'])->withInput();
    }

    public function show(string $id)
    {
        $this->ensureAdminOnly();

        $user = User::findOrFail($id);
        return redirect()->route('backend.user.edit', $user->id);
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanEdit($user);

        return view('backend.v_user.edit', [
            'judul' => Auth::user()->isAdmin() ? 'Ubah User' : 'Ubah Profil',
            'edit'  => $user,
            'isAdmin' => Auth::user()->isAdmin(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanEdit($user);

        $isAdmin = Auth::user()->isAdmin();

        $rules = [
            'nama'   => 'required|max:255',
            'hp'     => 'required|min:10|max:13',
            'foto'   => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ];

        if ($isAdmin) {
            $rules['role'] = 'required|in:0,1,2';
            $rules['status'] = 'required|boolean';
        }

        $messages = [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max'   => 'Ukuran file gambar Maksimal adalah 1024 KB.',
        ];

        if ($request->email != $user->email) {
            $rules['email'] = 'required|max:255|email|unique:user';
        }

        $validatedData = $request->validate($rules, $messages);

        if (!$isAdmin) {
            $validatedData['role'] = $user->role;
            $validatedData['status'] = $user->status;
        }

        if ($request->file('foto')) {
            if ($user->foto) {
                $oldImagePath = public_path('storage/img-user/') . $user->foto;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-user/';
            ImageHelper::uploadAndResize($file, $directory, $originalFileName, 385, 400);
            $validatedData['foto'] = $originalFileName;
        }

        $user->update($validatedData);
        if ($isAdmin) {
            return redirect()->route('backend.user.index')->with('alert', $this->modalAlert(
                'success',
                'Berhasil',
                'Data user berhasil diperbarui.'
            ));
        }

        return redirect()->route('backend.user.edit', $user->id)->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Profil berhasil diperbarui.'
        ));
    }

    public function destroy(string $id)
    {
        $this->ensureAdminOnly();

        $user = User::findOrFail($id);

        if ((int) Auth::id() === (int) $user->id) {
            return redirect()->route('backend.user.index')->with('alert', $this->modalAlert(
                'warning',
                'Aksi Ditolak',
                'Akun sendiri tidak bisa dihapus.'
            ));
        }

        if ($user->foto) {
            $oldImagePath = public_path('storage/img-user/') . $user->foto;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $user->delete();
        return redirect()->route('backend.user.index')->with('alert', $this->modalAlert(
            'success',
            'Berhasil',
            'Data user berhasil dihapus.'
        ));
    }
}
