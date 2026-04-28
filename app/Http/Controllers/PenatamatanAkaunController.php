<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenatamatanAkaunRequest;
use App\Models\PermohonanPenamatan;
use App\Notifications\PenatamatanNotification;
use Illuminate\Support\Facades\Auth;

class PenatamatanAkaunController extends Controller
{
    public function index()
    {
        $senarai = PermohonanPenamatan::where('pemohon_id', Auth::id())
            ->with(['penggunaSasaran'])
            ->latest()
            ->paginate(15);

        return view('m03.index', compact('senarai'));
    }

    public function create()
    {
        return view('m03.buat');
    }

    public function store(PenatamatanAkaunRequest $request)
    {
        $permohonan = PermohonanPenamatan::create([
            ...$request->validated(),
            'pemohon_id' => Auth::id(),
            'no_tiket' => PermohonanPenamatan::janaNoTiket(),
            'status' => 'MENUNGGU_KEL_1',
        ]);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'permohonan_dihantar',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'HANTAR'));

        $permohonan->notifikasi()->create([
            'penerima_id' => Auth::id(),
            'jenis' => 'HANTAR',
            'tajuk' => 'Permohonan '.$permohonan->no_tiket.' diterima',
            'mesej' => 'Permohonan penamatan akaun '.$permohonan->id_login_komputer.' sedang diproses.',
            'dihantar_pada' => now(),
        ]);

        return redirect()->route('penamatan-akaun.index')
            ->with('berjaya', 'Permohonan '.$permohonan->no_tiket.' berjaya dihantar.');
    }

    public function show($id)
    {
        $permohonan = PermohonanPenamatan::with([
            'pemohon',
            'penggunaSasaran',
            'kelulusan.pelulus',
            'logAudit.pengguna',
        ])
            ->where('pemohon_id', Auth::id())
            ->findOrFail($id);

        return view('m03.butiran', compact('permohonan'));
    }
}
