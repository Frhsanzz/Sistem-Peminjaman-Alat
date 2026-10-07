<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Alat;

class StoreAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // jumlah_rusak dikosongkan -> dianggap 0
        if ($this->input('jumlah_rusak') === null || $this->input('jumlah_rusak') === '') {
            $this->merge(['jumlah_rusak' => 0]);
        }
    }

    public function rules(): array
    {
        return [
            'kategori_id' => [
                'required',
                'integer',
                Rule::exists('kategori', 'id'),
            ],

            'nama_alat' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $nama = trim($value);

                    $sudahAda = Alat::whereRaw(
                        'LOWER(nama_alat) = ?',
                        [strtolower($nama)]
                    )->exists();

                    if ($sudahAda) {
                        $fail("Alat {$nama} sudah ada.");
                    }
                },
            ],

            'stok'           => ['required', 'integer', 'min:0'],
            'jumlah_rusak'   => ['nullable', 'integer', 'min:0', 'lte:stok'],
            'status_kondisi' => ['required', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string'],

            'gambar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],

            // Detail kerusakan (dipakai halaman Detail)
            'keterangan_rusak' => ['nullable', 'string', 'max:500'],
            'tanggal_rusak'    => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.exists' =>
                'Kategori yang dipilih tidak valid atau tidak terdaftar.',

            'stok.min' =>
                'Stok tidak boleh kurang dari 0.',

            'jumlah_rusak.lte' =>
                'Jumlah rusak tidak boleh melebihi total stok.',

            'gambar.max' =>
                'Ukuran gambar maksimal adalah 2 MB.',

            'gambar.image' =>
                'File yang diunggah harus berupa gambar.',

            'keterangan_rusak.max' =>
                'Keterangan kerusakan maksimal 500 karakter.',

            'tanggal_rusak.before_or_equal' =>
                'Tanggal rusak tidak boleh di masa depan.',
        ];
    }
}