<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenatamatanAkaunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'pengguna_sasaran_id' => ['required', 'exists:users,id', Rule::notIn([auth()->id()])],
            'id_login_komputer' => ['required', 'string', 'max:100'],
            'tarikh_berkuat_kuasa' => ['required', 'date', 'after_or_equal:today'],
            'jenis_tindakan' => ['required', 'in:TAMAT,GANTUNG'],
            'sebab_penamatan' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pengguna_sasaran_id.required' => 'Sila pilih pengguna sasaran.',
            'pengguna_sasaran_id.different' => 'Anda tidak boleh memohon penamatan akaun sendiri.',
            'id_login_komputer.required' => 'Sila masukkan ID login komputer.',
            'tarikh_berkuat_kuasa.after_or_equal' => 'Tarikh berkuat kuasa mestilah hari ini atau masa hadapan.',
            'sebab_penamatan.min' => 'Sebab penamatan mestilah sekurang-kurangnya 10 aksara.',
        ];
    }
}
