<?php

namespace App\Http\Controllers;

use App\Models\PermohonanPortal;
use Illuminate\Support\Facades\Auth;

class PermohonanPortalController extends Controller
{
    public function index()
    {
        $senarai = PermohonanPortal::where('pemohon_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('m04.index', compact('senarai'));
    }

    public function create()
    {
        return view('m04.buat');
    }

    public function show(int $id)
    {
        $permohonan = PermohonanPortal::with([
            'pemohon',
            'pentadbir',
            'lampirans',
            'logAudits.pengguna',
        ])
            ->where('pemohon_id', Auth::id())
            ->findOrFail($id);

        return view('m04.butiran', compact('permohonan'));
    }
}
