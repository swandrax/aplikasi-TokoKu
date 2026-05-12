<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'produk_id' => 'required|exists:produk,id',
            'foto_produk' => 'required|array|min:1',
            'foto_produk.*' => 'required|image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'produk_id.exists' => 'Produk yang dipilih tidak valid.',
            'foto_produk.required' => 'Silakan pilih minimal satu foto produk.',
            'foto_produk.array' => 'Format upload foto produk tidak valid.',
            'foto_produk.min' => 'Silakan pilih minimal satu foto produk.',
            'foto_produk.*.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto_produk.*.max' => 'Ukuran file gambar maksimal adalah 1024 KB.',
        ];
    }
}
