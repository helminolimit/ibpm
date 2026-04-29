<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermohonanPortalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url_halaman' => ['required', 'url'],
            'jenis_perubahan' => ['required', 'in:kandungan,konfigurasi,lain_lain'],
            'butiran_kemaskini' => ['required', 'string', 'min:10'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['file', 'mimes:pdf,jpg,png', 'max:5120'],
        ];
    }
}
