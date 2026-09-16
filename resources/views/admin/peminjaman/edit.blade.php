@extends('layouts.app')

@section('title', 'Edit Peminjaman - Panel Admin')
@section('header-title', 'Edit Peminjaman Alat')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.peminjaman.update', $peminjaman->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Peminjam</label>
            <select name="user_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $peminjaman->user_id) == $user->id)>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', $peminjaman->tgl_pinjam) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_pinjam')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Kembali (Rencana)</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan', $peminjaman->tgl_kembali_plan) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali_plan')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
            <select name="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach (['diajukan', 'dipinjam', 'dikembalikan', 'telat'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $peminjaman->status) == $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            @error('status')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <hr class="my-6 border-gray-200">

        <div class="mb-3 flex items-center justify-between">
            <label class="block text-gray-700 text-sm font-semibold">Detail Alat yang Dipinjam</label>
            <button type="button" id="btn-add-row"
                class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                + Tambah Alat
            </button>
        </div>

        <div id="detail-wrapper" class="space-y-3 mb-6">
            @foreach ($peminjaman->detailPinjam as $detail)
            <div class="detail-row flex gap-3 items-start">
                <div class="flex-1">
                    <select name="alat_id[]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Alat --</option>
                        @foreach ($alat as $a)
                            <option value="{{ $a->id }}" @selected($a->id == $detail->alat_id)>
                                {{ $a->nama_alat }} (stok: {{ $a->stok }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <input type="number" name="jumlah[]" min="1" value="{{ $detail->jumlah }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="button"
                    class="btn-remove-row bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition">
                    Hapus
                </button>
            </div>
            @endforeach
        </div>
        @error('alat_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.peminjaman.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const wrapper = document.getElementById('detail-wrapper');
    const template = wrapper.querySelector('.detail-row').cloneNode(true);

    document.getElementById('btn-add-row').addEventListener('click', () => {
        const row = template.cloneNode(true);
        row.querySelectorAll('input').forEach(el => el.value = '');
        row.querySelector('select').selectedIndex = 0;
        wrapper.appendChild(row);
    });

    wrapper.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-remove-row')) {
            if (wrapper.querySelectorAll('.detail-row').length > 1) {
                e.target.closest('.detail-row').remove();
            }
        }
    });
</script>
@endsection
