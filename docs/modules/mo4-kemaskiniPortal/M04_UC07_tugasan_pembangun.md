# UC07 — Tugaskan Pembangun Web

Modul: M04 Kemaskini Portal  
Pelakon: Pentadbir (Unit Aplikasi Teras dan Multimedia)

---

## Keperluan

- Pentadbir pilih pembangun dari senarai pengguna berperanan `pentadbir`
- Rekod tugasan dalam `tugasan_portals`
- Pembangun terima notifikasi emel tugasan baru
- Pentadbir boleh tukar tugasan jika perlu

---

## Migration

```php
Schema::create('tugasan_portals', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('permohonan_id')->constrained('permohonan_portals');
    $table->foreignUuid('teknisian_id')->constrained('penggunas');
    $table->text('nota_tugasan')->nullable();
    $table->enum('status_tugasan', ['baharu', 'dalam_proses', 'selesai'])->default('baharu');
    $table->timestamp('tarikh_tugasan')->useCurrent();
    $table->timestamps();
});
```

---

## Model

```php
// app/Models/TugasanPortal.php

protected $fillable = [
    'permohonan_id', 'teknisian_id',
    'nota_tugasan', 'status_tugasan',
];

public function permohonan(): BelongsTo
{
    return $this->belongsTo(PermohonanPortal::class, 'permohonan_id');
}

public function teknisian(): BelongsTo
{
    return $this->belongsTo(Pengguna::class, 'teknisian_id');
}
```

---

## Route

```php
Route::middleware(['auth', 'peranan:pentadbir'])->prefix('pentadbir')->group(function () {
    Route::post('/m04/{permohonan}/tugasan', [TugasanPortalController::class, 'store'])->name('pentadbir.m04.tugasan.store');
    Route::patch('/m04/tugasan/{tugasan}', [TugasanPortalController::class, 'update'])->name('pentadbir.m04.tugasan.update');
});
```

---

## Controller

```php
// app/Http/Controllers/TugasanPortalController.php

public function store(Request $request, PermohonanPortal $permohonan)
{
    $request->validate([
        'teknisian_id' => 'required|exists:penggunas,id',
        'nota_tugasan' => 'nullable|string|max:500',
    ]);

    $tugasan = TugasanPortal::create([
        'permohonan_id' => $permohonan->id,
        'teknisian_id'  => $request->teknisian_id,
        'nota_tugasan'  => $request->nota_tugasan,
    ]);

    $teknisian = Pengguna::find($request->teknisian_id);

    Mail::to($teknisian->email)->queue(new TugasanBaru($tugasan));

    LogAudit::catat(Auth::id(), $permohonan->id, 'tugasan_dibuat', 'M04');

    return back()->with('berjaya', 'Tugasan berjaya ditetapkan.');
}

public function update(Request $request, TugasanPortal $tugasan)
{
    $request->validate([
        'teknisian_id' => 'required|exists:penggunas,id',
        'nota_tugasan' => 'nullable|string|max:500',
    ]);

    $tugasan->update($request->only('teknisian_id', 'nota_tugasan'));

    LogAudit::catat(Auth::id(), $tugasan->permohonan_id, 'tugasan_dikemaskini', 'M04');

    return back()->with('berjaya', 'Tugasan dikemaskini.');
}
```

---

## Mailable — Tugasan Baru

```php
// app/Mail/TugasanBaru.php

class TugasanBaru extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public TugasanPortal $tugasan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ICTServe] Tugasan Baru — ' . $this->tugasan->permohonan->no_tiket,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.m04.tugasan-baru');
    }
}
```

---

## Blade Emel — Tugasan Baru

```blade
{{-- resources/views/emails/m04/tugasan-baru.blade.php --}}
<p>Tuan/Puan {{ $tugasan->teknisian->nama }},</p>
<p>Anda telah ditugaskan untuk menyelesaikan permohonan kemaskini portal berikut:</p>
<table>
    <tr><td>No. Tiket</td><td>{{ $tugasan->permohonan->no_tiket }}</td></tr>
    <tr><td>URL Halaman</td><td>{{ $tugasan->permohonan->url_halaman }}</td></tr>
    <tr><td>Jenis</td><td>{{ ucfirst($tugasan->permohonan->jenis_perubahan) }}</td></tr>
    <tr><td>Butiran</td><td>{{ $tugasan->permohonan->butiran_kemaskini }}</td></tr>
    @if($tugasan->nota_tugasan)
    <tr><td>Nota Pentadbir</td><td>{{ $tugasan->nota_tugasan }}</td></tr>
    @endif
</table>
<p>Sila log masuk ke ICTServe untuk maklumat lanjut.</p>
```

---

## Blade View (borang tugasan dalam panel pentadbir)

```blade
{{-- Dalam resources/views/pentadbir/m04/show.blade.php --}}
<form method="POST" action="{{ route('pentadbir.m04.tugasan.store', $permohonan) }}">
    @csrf

    <select name="teknisian_id" required>
        <option value="">-- Pilih Pembangun --</option>
        @foreach($pembangun as $p)
        <option value="{{ $p->id }}">{{ $p->nama }}</option>
        @endforeach
    </select>

    <textarea name="nota_tugasan" placeholder="Nota tugasan (optional)..." rows="3"></textarea>

    <button type="submit">Tugaskan</button>
</form>

@if($permohonan->tugasans->count())
<table>
    <thead>
        <tr><th>Ditugaskan Kepada</th><th>Nota</th><th>Status</th><th>Tarikh</th></tr>
    </thead>
    <tbody>
        @foreach($permohonan->tugasans as $t)
        <tr>
            <td>{{ $t->teknisian->nama }}</td>
            <td>{{ $t->nota_tugasan ?? '—' }}</td>
            <td>{{ ucfirst($t->status_tugasan) }}</td>
            <td>{{ $t->tarikh_tugasan->format('d M Y, H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
```

---

## Larangan

- Jangan biar pemohon akses route tugasan
- Jangan tugaskan kepada pengguna berperanan `pemohon`
- Jangan lupa log audit setiap tugasan

---

## Kriteria Penerimaan

- [ ] Pentadbir boleh pilih pembangun dari dropdown
- [ ] Rekod tugasan tersimpan dalam `tugasan_portals`
- [ ] Pembangun terima notifikasi emel
- [ ] Sejarah tugasan dipapar dalam halaman butiran
- [ ] Log audit direkod

---

*ICTServe M04 | UC07 | April 2026*
