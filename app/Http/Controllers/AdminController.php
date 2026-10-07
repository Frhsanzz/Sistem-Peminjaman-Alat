<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ==========================================
    // DASHBOARD ADMIN
    // ==========================================
    public function index()
    {
        // ==========================================
        // MINI STATISTIK
        // ==========================================

        // Jumlah peminjaman yang dilakukan hari ini
        $peminjamanHariIni = Peminjaman::whereDate(
            'tgl_pinjam',
            today()
        )->count();


        // Jumlah user baru yang dibuat hari ini
        $userBaru = User::whereDate(
            'created_at',
            today()
        )->count();


        // Jumlah alat yang sedang dipinjam
        $alatDipinjam = DetailPinjam::whereHas(
            'peminjaman',
            function ($query) {
                $query->where('status', 'dipinjam');
            }
        )->sum('jumlah');


        // Total denda
        $dendaTerkumpul = DB::table('pengembalian')
            ->sum('denda');


        // ==========================================
        // TOTAL DATA
        // ==========================================

        $totalUser = User::count();

        $totalAlat = Alat::count();

        $totalPeminjaman = Peminjaman::count();

        $totalKategori = Kategori::count();


        // ==========================================
        // LOG AKTIVITAS
        // ==========================================

        $logs = LogAktivitas::with('user')
            ->latest()
            ->take(10)
            ->get();


        // ==========================================
        // KIRIM DATA KE DASHBOARD
        // ==========================================

        return view('admin.dashboard', compact(
            'peminjamanHariIni',
            'userBaru',
            'alatDipinjam',
            'dendaTerkumpul',
            'totalUser',
            'totalAlat',
            'totalPeminjaman',
            'totalKategori',
            'logs'
        ));
    }


    // ==========================================
    // CRUD USER
    // ==========================================

    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,petugas,peminjam',
        'no_hp' => 'nullable|string|max:20',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'no_hp' => $request->no_hp,
    ];

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');

        $filename = time() . '-' . $file->getClientOriginalName();

        $file->move(
            public_path('storage/users'),
            $filename
        );

        $data['foto_profil'] = 'storage/users/' . $filename;
    }

    User::create($data);

    return redirect()
        ->route('admin.user.index')
        ->with('success', 'User berhasil ditambahkan.');
}

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'role' => 'required|in:admin,petugas,peminjam',
        'no_hp' => 'nullable|string|max:20',
        'password' => 'nullable|string|min:6',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'no_hp' => $request->no_hp,
    ];

    // Password
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Foto profil
    if ($request->hasFile('photo')) {

        $file = $request->file('photo');

        // Pastikan folder tersedia
        $folder = public_path('storage/users');

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        // Hapus foto lama jika ada
        if (
            $user->foto_profil &&
            file_exists(public_path($user->foto_profil))
        ) {
            unlink(public_path($user->foto_profil));
        }

        // Buat nama file baru
        $filename = time() . '-' . $file->getClientOriginalName();

        // Pindahkan file
        $file->move($folder, $filename);

        // Simpan path ke database
        $data['foto_profil'] = 'storage/users/' . $filename;
    }

    // Update user
    $user->update($data);

    return redirect()
        ->route('admin.user.index')
        ->with('success', 'Data user berhasil diperbarui.');
}

    public function destroyUser($id)
{
    $user = User::findOrFail($id);

    // Admin tidak boleh menghapus akun yang sedang digunakan
    if (auth()->id() === $user->id) {
        return redirect()
            ->route('admin.user.index')
            ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
    }

    // Cek apakah user masih memiliki proses peminjaman
    $sedangMeminjam = $user->peminjaman()
        ->whereIn('status', [
            'diajukan',
            'dipinjamkan',
            'telat',
        ])
        ->exists();

    if ($sedangMeminjam) {
        return redirect()
            ->route('admin.user.index')
            ->with('error', 'User sedang proses peminjaman dan tidak dapat dihapus.');
    }

    $namaUser = $user->name;

    // Hapus foto profil jika ada
    if (
        $user->foto_profil &&
        file_exists(public_path($user->foto_profil))
    ) {
        unlink(public_path($user->foto_profil));
    }

    $user->delete();

    // Catat aktivitas
    \DB::table('log_aktivitas')->insert([
        'user_id' => auth()->id(),
        'aktivitas' => 'Menghapus user: ' . $namaUser,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()
        ->route('admin.user.index')
        ->with('success', 'User berhasil dihapus.');
}
public function toggleStatusUser($id)
{
    $user = User::findOrFail($id);

    // Tidak boleh menonaktifkan akun sendiri
    if (auth()->id() === $user->id) {
        return redirect()
            ->route('admin.user.index')
            ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
    }

    $user->is_active = !$user->is_active;
    $user->save();

    $aktivitas = $user->is_active
        ? 'Mengaktifkan user: ' . $user->name
        : 'Menonaktifkan user: ' . $user->name;

    \DB::table('log_aktivitas')->insert([
        'user_id' => auth()->id(),
        'aktivitas' => $aktivitas,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()
        ->route('admin.user.index')
        ->with(
            'success',
            $user->is_active
                ? 'User berhasil diaktifkan.'
                : 'User berhasil dinonaktifkan.'
        );
}


    // ==========================================
    // CRUD KATEGORI
    // ==========================================

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategori = Kategori::when($search, function ($query, $search) {
                return $query->where(
                    'nama_kategori',
                    'like',
                    "%{$search}%"
                );
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategori', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alat()->count() > 0) {
            return redirect()
                ->route('admin.kategori.index')
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.'
                );
        }

        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }


    // ==========================================
    // CRUD ALAT
    // ==========================================

    public function indexAlat(Request $request)
{
    $search = $request->query('search');
 
    // Filter pencarian: nama alat atau nama kategori
    $cari = fn ($query) => $query->when($search, fn ($q) =>
        $q->where(function ($w) use ($search) {
            $w->where('nama_alat', 'like', "%{$search}%")
              ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%{$search}%"));
        })
    );
 
    // Tabel "Baik": alat yang masih punya unit baik (stok - jumlah_rusak > 0)
    $alatBaik = Alat::with('kategori')
        ->whereRaw('stok - jumlah_rusak > 0')
        ->tap($cari)
        ->latest('id')
        ->paginate(5)
        ->withQueryString();
 
    // Tabel "Rusak": alat yang punya unit rusak
    $alatRusak = Alat::with('kategori')
        ->where('jumlah_rusak', '>', 0)
        ->tap($cari)
        ->latest('id')
        ->get();
 
    $totalRusak = (int) Alat::sum('jumlah_rusak');
    $totalBaik  = (int) Alat::sum('stok') - $totalRusak;
 
    return view('admin.alat.index', compact('alatBaik', 'alatRusak', 'totalBaik', 'totalRusak'));
}

public function perbaikiAlat(Request $request, $id)
{
    $alat = Alat::findOrFail($id);

    if ($alat->jumlah_rusak < 1) {
        return back()->with('error', 'Alat ini tidak memiliki unit rusak.');
    }

    $data = $request->validate([
        'jumlah' => ['required', 'integer', 'min:1', 'max:' . $alat->jumlah_rusak],
    ], [
        'jumlah.required' => 'Jumlah wajib diisi.',
        'jumlah.min'      => 'Jumlah minimal 1 unit.',
        'jumlah.max'      => 'Jumlah tidak boleh lebih dari ' . $alat->jumlah_rusak . ' unit rusak.',
    ]);

    DB::transaction(function () use ($alat, $data) {
        $alat->decrement('jumlah_rusak', $data['jumlah']);
        $alat->refresh();

        // semua unit sudah baik: bersihkan info kerusakan
        if ($alat->jumlah_rusak === 0) {
            $alat->tanggal_rusak = null;
            $alat->keterangan_rusak = null;
        }

        $alat->syncKondisi(); // update status_kondisi + save()
    });

    return redirect()
        ->route('admin.alat.index')
        ->with('success', "{$data['jumlah']} unit {$alat->nama_alat} berhasil diperbaiki.");
}
public function getKondisiLabelAttribute(): array
{
    return match (true) {
        $this->jumlah_rusak <= 0            => ['Baik', 'badge-baik'],
        $this->jumlah_rusak >= $this->stok  => ['Rusak semua', 'badge-rusak'],
        default                             => ['Sebagian rusak', 'badge-sebagian'],
    };
}
 
public function showAlat($id)
{
    $alat = Alat::with('kategori')->findOrFail($id);
 
    return view('admin.alat.show', compact('alat'));
}
    public function createAlat()
    {
        $kategori = Kategori::all();

        return view('admin.alat.create', compact('kategori'));
    }

    
public function storeAlat(Request $request)
{
    // Cek nama alat yang sudah terdaftar
    $namaAlat = trim($request->nama_alat ?? '');

    $sudahAda = Alat::whereRaw(
        'LOWER(nama_alat) = ?',
        [strtolower($namaAlat)]
    )->exists();

    if ($sudahAda) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', "Alat {$namaAlat} sudah ada.");
    }

    // Validasi data
    $request->validate([
        'nama_alat' => 'required|string|max:255',
        'kategori_id' => 'required|exists:kategori,id',
        'stok' => 'required|integer|min:0',
        'status_kondisi' => 'required|string|max:100',
        'deskripsi' => 'nullable|string',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = $request->all();

    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');

        $filename = time() . '-' . $file->getClientOriginalName();

        $file->move(
            public_path('storage/alat'),
            $filename
        );

        $data['gambar'] = 'storage/alat/' . $filename;
    }

    Alat::create($data);

    return redirect()
        ->route('admin.alat.index')
        ->with('success', 'Data alat berhasil ditambahkan.');
}

    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategori = Kategori::all();

        return view(
            'admin.alat.edit',
            compact('alat', 'kategori')
        );
    }

    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'nama_alat' => $request->nama_alat,
            'kategori_id' => $request->kategori_id,
            'stok' => $request->stok,
            'status_kondisi' => $request->status_kondisi,
            'deskripsi' => $request->deskripsi,
        ];

        if ($request->hasFile('gambar')) {

            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');

            $filename = time() . '-' . $file->getClientOriginalName();

            $file->move(
                public_path('storage/alat'),
                $filename
            );

            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil dihapus.');
    }


    // ==========================================
    // PEMINJAMAN
    // ==========================================

    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with([
                'user',
                'detailPinjam.alat'
            ])
            ->when($search, function ($query, $search) {
                return $query->where(
                        'status',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.peminjaman.index',
            compact('peminjaman', 'search')
        );
    }

    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();

        $alat = Alat::where('stok', '>', 0)->get();

        return view(
            'admin.peminjaman.create',
            compact('users', 'alat')
        );
    }

    public function editPeminjaman($id)
{
    $peminjaman = Peminjaman::with([
        'user',
        'detailPinjam.alat'
    ])->findOrFail($id);

    $users = User::where('role', 'peminjam')
        ->orderBy('name')
        ->get();

    $alat = Alat::orderBy('nama_alat')
        ->get();

    return view('admin.peminjaman.edit', compact(
        'peminjaman',
        'users',
        'alat'
    ));
}

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);
        

        DB::beginTransaction();

        try {

            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            foreach ($request->alat_id as $index => $alatId) {

                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception(
                        "Stok alat '{$alat->nama_alat}' tidak mencukupi."
                    );
                }

                $peminjaman->detailPinjam()->create([
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);

                $alat->decrement('stok', $jumlahPinjam);
            }

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'success',
                    'Data peminjaman berhasil disimpan.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
    public function updatePeminjaman(Request $request, $id)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'tgl_pinjam' => 'required|date',
        'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
        'alat_id' => 'required|array|min:1',
        'alat_id.*' => 'required|exists:alat,id',
        'jumlah' => 'required|array|min:1',
        'jumlah.*' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

        /*
         * 1. Kembalikan stok dari detail lama
         */
        foreach ($peminjaman->detailPinjam as $detail) {
            $alatLama = Alat::find($detail->alat_id);

            if ($alatLama) {
                $alatLama->increment('stok', $detail->jumlah);
            }
        }

        /*
         * 2. Hapus detail lama
         */
        $peminjaman->detailPinjam()->delete();

        /*
         * 3. Update data utama peminjaman
         */
        $peminjaman->update([
            'user_id' => $request->user_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali_plan' => $request->tgl_kembali_plan,
        ]);

        /*
         * 4. Simpan detail baru dan kurangi stok
         */
        foreach ($request->alat_id as $index => $alatId) {

            $jumlahPinjam = $request->jumlah[$index];

            $alat = Alat::findOrFail($alatId);

            if ($alat->stok < $jumlahPinjam) {
                throw new \Exception(
                    "Stok alat '{$alat->nama_alat}' tidak mencukupi."
                );
            }

            $peminjaman->detailPinjam()->create([
                'alat_id' => $alatId,
                'jumlah' => $jumlahPinjam,
            ]);

            $alat->decrement('stok', $jumlahPinjam);
        }

        DB::commit();

        return redirect()
            ->route('admin.peminjaman.index')
            ->with(
                'success',
                'Data peminjaman berhasil diperbarui.'
            );

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Data peminjaman gagal diperbarui: ' . $e->getMessage()
            );
    }
}
    
// ==========================================
    // HAPUS PEMINJAMAN
    // ==========================================

    public function destroyPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::find($detail->alat_id);

                if ($alat) {
                    $alat->increment('stok', $detail->jumlah);
                }
            }

            // Hapus detail peminjaman
            DetailPinjam::where(
                'peminjaman_id',
                $peminjaman->id
            )->delete();

            // Hapus peminjaman
            $peminjaman->delete();

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'success',
                    'Data peminjaman berhasil dihapus.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'error',
                    'Data peminjaman gagal dihapus: ' . $e->getMessage()
                );
        }
    }
}