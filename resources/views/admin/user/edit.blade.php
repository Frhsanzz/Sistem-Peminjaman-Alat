@extends('layouts.app')

@section('title', 'Edit user - Panel Admin')
@section('header-title', 'Edit Data Pengguna')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-6 flex items-center gap-4">
            <img id="photo-preview"
    src="{{ $user->foto_profil
        ? asset($user->foto_profil)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366F1&color=fff' }}"
    class="w-16 h-16 rounded-full object-cover border-2 border-gray-100">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Foto Profil</label>
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg transition">
                        Pilih Foto
                        <input type="file" name="photo" accept="image/*" onchange="previewPhoto(event)" class="hidden">
                    </label>
                    <span id="file-name" class="text-xs text-gray-400">Belum ada file dipilih</span>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Kosongkan jika tidak ingin mengganti foto</p>
                @error('photo')<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru
                <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span></label>
            <input type="password" name="password" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password')<span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Role / Hak Akses</label>
            <select name="role" required
    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

    <option value="peminjam" {{ $user->role == 'peminjam' ? 'selected' : '' }}>
        Peminjam
    </option>

    <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>
        Petugas
    </option>

    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
        Admin
    </option>

</select>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP (opsional)</label>
            <input type="text"
       name="no_hp"
       value="{{ old('no_hp', $user->no_hp) }}"
       inputmode="numeric"
       maxlength="15"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
       title="Hanya angka, 10 sampai 15 digit"
       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
@error('no_hp')
    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.user.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit"
             class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>

<script>
    function previewPhoto(event) {
        const preview = document.getElementById('photo-preview');
        const fileName = document.getElementById('file-name');
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            fileName.textContent = file.name;
            fileName.classList.remove('text-gray-400');
            fileName.classList.add('text-gray-700', 'font-medium');
        }
    }
</script>
@endsection