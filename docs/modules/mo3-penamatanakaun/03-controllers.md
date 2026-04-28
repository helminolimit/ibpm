# 03 — Controllers
## M03 Penamatan Akaun Login Komputer

Cipta 3 controller berikut. Semua logik bisnes dalam controller — JANGAN dalam Livewire atau Blade.

---

## Controller 1: Pemohon
**Fail:** `app/Http/Controllers/PenatamatanAkaunController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\PermohonanPenamatan;
use App\Http\Requests\PenatamatanAkaunRequest;
use App\Notifications\PenatamatanNotification;
use Illuminate\Support\Facades\Auth;

class PenatamatanAkaunController extends Controller
{
    // Papar senarai permohonan milik pemohon yang log masuk
    // Return: view m03.index dengan senarai permohonan sendiri sahaja
    public function index()
    {
        $senarai = PermohonanPenamatan::where('pemohon_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('m03.index', compact('senarai'));
    }

    // Papar borang permohonan baru
    // Return: view m03.buat
    public function create()
    {
        return view('m03.buat');
    }

    // Simpan permohonan baharu dan hantar ke peringkat kelulusan pertama
    // Parameter: $request — data borang yang telah disahkan oleh PenatamatanAkaunRequest
    // Return: redirect ke halaman senarai dengan mesej kejayaan
    public function store(PenatamatanAkaunRequest $request)
    {
        // Jana nombor tiket unik format PAK-YYYY-NNN
        $permohonan = PermohonanPenamatan::create([
            ...$request->validated(),
            'pemohon_id' => Auth::id(),
            'no_tiket'   => PermohonanPenamatan::janaNoTiket(),
            'status'     => 'MENUNGGU_KEL_1',
        ]);

        // Rekod tindakan dalam log audit
        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan'    => 'permohonan_dihantar',
            'modul'       => 'M03',
            'ip_address'  => request()->ip(),
        ]);

        // Hantar notifikasi emel kepada pemohon dan pelulus peringkat 1
        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'HANTAR'));

        return redirect()->route('penamatan-akaun.index')
            ->with('berjaya', 'Permohonan ' . $permohonan->no_tiket . ' berjaya dihantar.');
    }

    // Papar butiran satu permohonan — hanya pemohon sendiri atau pentadbir boleh akses
    // Parameter: $id — ID permohonan dalam pangkalan data
    // Return: view m03.butiran
    public function show($id)
    {
        $permohonan = PermohonanPenamatan::with(['pemohon','penggunaSasaran','kelulusan.pelulus','logAudit'])
            ->where('pemohon_id', Auth::id()) // Pastikan pemohon hanya lihat permohonan sendiri
            ->findOrFail($id);

        return view('m03.butiran', compact('permohonan'));
    }
}
```

---

## Controller 2: Pelulus Peringkat 1
**Fail:** `app/Http/Controllers/KelulusanPeringkat1Controller.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\PermohonanPenamatan;
use App\Models\Kelulusan;
use App\Notifications\PenatamatanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelulusanPeringkat1Controller extends Controller
{
    // Papar senarai permohonan yang menunggu kelulusan peringkat 1
    // Return: view m03.kelulusan1.index
    public function index()
    {
        $senarai = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')
            ->with(['pemohon', 'penggunaSasaran'])
            ->latest()
            ->paginate(15);

        return view('m03.kelulusan1.index', compact('senarai'));
    }

    // Luluskan permohonan peringkat 1 — tukar status ke MENUNGGU_KEL_2
    // Parameter: $id — ID permohonan
    // Return: redirect dengan mesej kejayaan
    public function lulus($id)
    {
        $permohonan = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')->findOrFail($id);

        // Rekod kelulusan peringkat 1
        $permohonan->kelulusan()->create([
            'pelulus_id'      => Auth::id(),
            'peringkat'       => 'PERINGKAT_1',
            'keputusan'       => 'LULUS',
            'tarikh_tindakan' => now(),
        ]);

        // Tukar status supaya menunggu kelulusan pentadbir ICT
        $permohonan->update(['status' => 'MENUNGGU_KEL_2']);

        // Rekod audit + hantar notifikasi kepada Pentadbir ICT
        $permohonan->logAudit()->create(['pengguna_id'=>Auth::id(),'tindakan'=>'kelulusan_peringkat_1','modul'=>'M03','ip_address'=>request()->ip()]);
        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'KELULUSAN'));

        return redirect()->route('kelulusan.penamatan.index')->with('berjaya', 'Permohonan ' . $permohonan->no_tiket . ' diluluskan.');
    }

    // Tolak permohonan peringkat 1 — tukar status ke DITOLAK
    // Parameter: $id, $request->catatan (sebab penolakan — wajib)
    // Return: redirect dengan mesej makluman
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5|max:500']);

        $permohonan = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')->findOrFail($id);

        $permohonan->kelulusan()->create([
            'pelulus_id'      => Auth::id(),
            'peringkat'       => 'PERINGKAT_1',
            'keputusan'       => 'TOLAK',
            'catatan'         => $request->catatan,
            'tarikh_tindakan' => now(),
        ]);

        $permohonan->update(['status' => 'DITOLAK']);
        $permohonan->logAudit()->create(['pengguna_id'=>Auth::id(),'tindakan'=>'tolak_peringkat_1','modul'=>'M03','ip_address'=>request()->ip()]);
        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'TOLAK'));

        return redirect()->route('kelulusan.penamatan.index')->with('maklum', 'Permohonan ' . $permohonan->no_tiket . ' ditolak.');
    }
}
```

---

## Controller 3: Pentadbir ICT
**Fail:** `app/Http/Controllers/Admin/PenatamatanAdminController.php`

4 method wajib:

| Method | Guard Status | Tindakan |
|--------|-------------|---------|
| `index(Request $r)` | — | Query semua + `when($r->status,...)` filter + paginate(20) |
| `lulus($id)` | `MENUNGGU_KEL_2` | Rekod kelulusan PERINGKAT_2, update status → `DALAM_PROSES`, log audit |
| `selesai(Request $r, $id)` | `DALAM_PROSES` | Update status → `SELESAI`, isi `tarikh_selesai=now()`, log audit, notify pemohon `SELESAI` |
| `audit($id)` | — | Load `with('logAudit.pengguna')`, return view `m03.admin.audit` |

Setiap method yang tukar status **wajib** panggil `$permohonan->logAudit()->create([...])` sebelum return.

---

## Form Request
**Fail:** `app/Http/Requests/PenatamatanAkaunRequest.php`

```php
public function rules(): array
{
    return [
        'pengguna_sasaran_id'  => 'required|exists:users,id|different:' . auth()->id(),
        'id_login_komputer'    => 'required|string|max:100',
        'tarikh_berkuat_kuasa' => 'required|date|after_or_equal:today',
        'jenis_tindakan'       => 'required|in:TAMAT,GANTUNG',
        'sebab_penamatan'      => 'required|string|min:10|max:1000',
    ];
}
```
