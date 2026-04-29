# UC06 — Urus dan Kemaskini Status Permohonan

Modul: M04 Kemaskini Portal  
Pelakon: Pentadbir (Unit Aplikasi Teras dan Multimedia)

---

## Keperluan

- Pentadbir lihat semua permohonan M04
- Tapis mengikut status
- Kemaskini status: Diterima → Dalam Proses → Selesai
- Trigger notifikasi emel ke Pemohon bila status berubah
- Log audit setiap perubahan status

---

## Route

```php
Route::middleware(['auth', 'peranan:pentadbir'])->prefix('pentadbir')->group(function () {
    Route::get('/m04', [PentadbirPortalController::class, 'index'])->name('pentadbir.m04.index');
    Route::get('/m04/{permohonan}', [PentadbirPortalController::class, 'show'])->name('pentadbir.m04.show');
    Route::patch('/m04/{permohonan}/status', [PentadbirPortalController::class, 'kemaskiniStatus'])->name('pentadbir.m04.status');
});
```

---

## Controller

```php
// app/Http/Controllers/PentadbirPortalController.php

public function index(Request $request)
{
    $senarai = PermohonanPortal::with('pemohon')
        ->when($request->status, fn($q, $s) => $q->where('status', $s))
        ->latest()
        ->paginate(20);

    return view('pentadbir.m04.index', compact('senarai'));
}

public function kemaskiniStatus(Request $request, PermohonanPortal $permohonan)
{
    $request->validate([
        'status' => 'required|in:diterima,dalam_proses,selesai',
    ]);

    $statusLama = $permohonan->status;

    $permohonan->update([
        'status'       => $request->status,
        'pentadbir_id' => Auth::id(),
        'tarikh_selesai' => $request->status === 'selesai' ? now() : null,
    ]);

    Mail::to($permohonan->pemohon->email)->queue(new StatusDikemaskini($permohonan));

    Notifikasi::create([
        'pengguna_id'   => $permohonan->pemohon_id,
        'permohonan_id' => $permohonan->id,
        'jenis'         => 'status_dikemaskini',
        'mesej'         => "Status {$permohonan->no_tiket} dikemaskini kepada {$request->status}.",
    ]);

    LogAudit::catat(
        Auth::id(),
        $permohonan->id,
        "kemaskini_status: {$statusLama} → {$request->status}",
        'M04'
    );

    return back()->with('berjaya', 'Status berjaya dikemaskini.');
}
```

---

## Livewire Component (Panel Pentadbir)

```php
// app/Livewire/Pentadbir/M04/PanelPermohonan.php

class PanelPermohonan extends Component
{
    public string $tapis = '';

    public function render(): View
    {
        $senarai = PermohonanPortal::with('pemohon')
            ->when($this->tapis, fn($q) => $q->where('status', $this->tapis))
            ->latest()
            ->paginate(20);

        return view('livewire.pentadbir.m04.panel-permohonan', compact('senarai'));
    }
}
```

---

## Blade View (panel pentadbir)

```blade
{{-- resources/views/pentadbir/m04/index.blade.php --}}
<div>
    <select wire:model.live="tapis">
        <option value="">Semua Status</option>
        <option value="diterima">Diterima</option>
        <option value="dalam_proses">Dalam Proses</option>
        <option value="selesai">Selesai</option>
    </select>

    <table>
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Pemohon</th>
                <th>URL Halaman</th>
                <th>Jenis</th>
                <th>Tarikh Mohon</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($senarai as $item)
            <tr>
                <td>{{ $item->no_tiket }}</td>
                <td>{{ $item->pemohon->nama }}</td>
                <td>{{ $item->url_halaman }}</td>
                <td>{{ ucfirst($item->jenis_perubahan) }}</td>
                <td>{{ $item->tarikh_mohon->format('d M Y') }}</td>
                <td>@include('m04._badge', ['permohonan' => $item])</td>
                <td>
                    <form method="POST" action="{{ route('pentadbir.m04.status', $item) }}">
                        @csrf @method('PATCH')
                        <select name="status">
                            <option value="diterima" @selected($item->status === 'diterima')>Diterima</option>
                            <option value="dalam_proses" @selected($item->status === 'dalam_proses')>Dalam Proses</option>
                            <option value="selesai" @selected($item->status === 'selesai')>Selesai</option>
                        </select>
                        <button type="submit">Kemaskini</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7">Tiada permohonan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $senarai->links() }}
</div>
```

---

## LogAudit Helper

```php
// app/Models/LogAudit.php

public static function catat(
    string $penggunaId,
    string $permohonanId,
    string $tindakan,
    string $modul
): void {
    static::create([
        'pengguna_id'   => $penggunaId,
        'permohonan_id' => $permohonanId,
        'tindakan'      => $tindakan,
        'modul'         => $modul,
        'butiran'       => request()->ip(),
    ]);
}
```

---

## Migration log_audits

```php
Schema::create('log_audits', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('pengguna_id')->constrained('penggunas');
    $table->foreignUuid('permohonan_id')->constrained('permohonan_portals');
    $table->string('tindakan');
    $table->string('modul');
    $table->text('butiran')->nullable();
    $table->timestamps();
});
```

---

## Larangan

- Jangan biar pentadbir kemaskini status ke belakang (selesai → diterima)
- Jangan skip notifikasi emel bila status berubah
- Jangan kemaskini status tanpa log audit

---

## Kriteria Penerimaan

- [ ] Pentadbir lihat semua permohonan M04
- [ ] Status boleh dikemaskini dengan satu klik
- [ ] `tarikh_selesai` diset bila status = selesai
- [ ] Notifikasi emel ke pemohon dihantar
- [ ] Log audit direkod dengan status lama dan baru

---

*ICTServe M04 | UC06 | April 2026*
