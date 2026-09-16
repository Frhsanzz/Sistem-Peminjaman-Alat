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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

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
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
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
        $search = $request->input('search');

        $alat = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where(
                            'nama_kategori',
                            'like',
                            "%{$search}%"
                        );
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alat', 'search'));
    }

    public function createAlat()
    {
        $kategori = Kategori::all();

        return view('admin.alat.create', compact('kategori'));
    }

    public function storeAlat(Request $request)
    {
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
}