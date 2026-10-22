<?php

namespace App\Http\Requests\Member;

use App\Services\PengajuanPeminjaman;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $batas = today()->addDays(PengajuanPeminjaman::MAKS_HARI)->toDateString();

        return [
            'tanggal_kembali' => ['required', 'date', 'after:today', "before_or_equal:{$batas}"],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_kembali.required' => 'Pilih tanggal rencana pengembalian.',
            'tanggal_kembali.date' => 'Tanggal pengembalian tidak valid.',
            'tanggal_kembali.after' => 'Tanggal pengembalian harus setelah hari ini.',
            'tanggal_kembali.before_or_equal' => 'Masa pinjam maksimal '.PengajuanPeminjaman::MAKS_HARI.' hari.',
        ];
    }
}
