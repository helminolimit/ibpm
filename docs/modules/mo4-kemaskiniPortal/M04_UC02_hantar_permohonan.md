# UC02 — Hantar Permohonan Kemaskini Portal

Modul: M04 Kemaskini Portal  
Pelakon: Pemohon

---

## Keperluan

- Borang dengan validasi wajib
- Auto-generate no. tiket format `#ICT-YYYY-NNN`
- Sokong muat naik lampiran (PDF, JPG, PNG — max 5MB)
- Simpan ke `permohonan_portal`
- Trigger notifikasi emel ke Pentadbir selepas simpan

---

## Route

```php
// routes/web.php
Route::middleware(['auth', 'peranan:pemohon'])->group(function () {
    Route::get('/kemaskini-portal/baru', [PermohonanPortalController::class, 'create'])->name('m04.create');
    Route::post('/kemaskini-portal', [PermohonanPortalController::class, 'store'])->name('m04.store');
});
```

---

## Migration

```php
// database/migrations/xxxx_create_permohonan_portals_table.php

Schema::create('permohonan_portals', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('no_tiket')->unique();
    $table->foreignUuid('pemohon_id')->constrained('penggunas');
    $table->foreignUuid('pentadbir_id')->nullable()->constrained('penggunas');
    $table->string('url_halaman');
    $table->enum('jenis_perubahan', ['kandungan', 'konfigurasi', 'lain_lain']);
    $table->text('butiran_kemaskini');
    $table->enum('status', ['diterima', 'dalam_proses', 'selesai'])->default('diterima');
    $table->timestamp('tarikh_mohon')->useCurrent();
    $table->timestamp('tarikh_selesai')->nullable();
    $table->timestamps();
});
```

---

## Model

```php
// app/Models/PermohonanPortal.php

protected $fillable = [
    'no_tiket', 'pemohon_id', 'pentadbir_id',
    'url_halaman', 'jenis_perubahan',
    'butiran_kemaskini', 'status',
];

public static function janaNoTiket(): string
{
    $tahun = now()->year;
    $terakhir = static::whereYear('created_at', $tahun)->count() + 1;
    return sprintf('#ICT-%d-%03d', $tahun, $terakhir);
}

public function pemohon(): BelongsTo
{
    return $this->belongsTo(Pengguna::class, 'pemohon_id');
}

public function lampirans(): HasMany
{
    return $this->hasMany(Lampiran::class, 'permohonan_id');
}
```

---

## Controller

```php
// app/Http/Controllers/PermohonanPortalController.php

public function store(Request $request)
{
    $data = $request->validate([
        'url_halaman'      => 'required|url',
        'jenis_perubahan'  => 'required|in:kandungan,konfigurasi,lain_lain',
        'butiran_kemaskini'=> 'required|string|min:10',
        'lampiran.*'       => 'nullable|file|mimes:pdf,jpg,png|max:5120',
    ]);

    $permohonan = PermohonanPortal::create([
        ...$data,
        'no_tiket'   => PermohonanPortal::janaNoTiket(),
        'pemohon_id' => Auth::id(),
    ]);

    if ($request->hasFile('lampiran')) {
        foreach ($request->file('lampiran') as $fail) {
            $path = $fail->store('lampiran/m04', 'local');
            $permohonan->lampirans()->create([
                'nama_fail'  => $fail->getClientOriginalName(),
                'path_fail'  => $path,
                'jenis_fail' => $fail->getMimeType(),
            ]);
        }
    }

    Mail::to($this->emailPentadbir())->send(new PermohonanDiterima($permohonan));

    LogAudit::catat(Auth::id(), $permohonan->id, 'hantar_permohonan', 'M04');

    return redirect()->route('m04.show', $permohonan)->with('berjaya', 'Permohonan dihantar.');
}

private function emailPentadbir(): string
{
    return Pengguna::where('peranan', 'pentadbir')
        ->where('bahagian', 'Unit Aplikasi Teras dan Multimedia')
        ->value('email');
}
```

---

## Livewire Component

```php
// app/Livewire/M04/BentukPermohonan.php

class BentukPermohonan extends Component
{
    public string $url_halaman      = '';
    public string $jenis_perubahan  = '';
    public string $butiran_kemaskini = '';
    public array  $lampiran         = [];

    protected $rules = [
        'url_halaman'       => 'required|url',
        'jenis_perubahan'   => 'required',
        'butiran_kemaskini' => 'required|min:10',
        'lampiran.*'        => 'nullable|file|mimes:pdf,jpg,png|max:5120',
    ];

    public function hantar(): void
    {
        $this->validate();
        // panggil controller logic atau action
    }

    public function render(): View
    {
        return view('livewire.m04.bentuk-permohonan');
    }
}
```

---

## Blade View (bentuk)

```blade
{{-- resources/views/livewire/m04/bentuk-permohonan.blade.php --}}
<form wire:submit="hantar" enctype="multipart/form-data">
    <input type="url" wire:model="url_halaman" placeholder="https://portal.motac.gov.my/..." required />
    @error('url_halaman') <span>{{ $message }}</span> @enderror

    <select wire:model="jenis_perubahan" required>
        <option value="">-- Pilih Jenis --</option>
        <option value="kandungan">Kandungan</option>
        <option value="konfigurasi">Konfigurasi</option>
        <option value="lain_lain">Lain-lain</option>
    </select>

    <textarea wire:model="butiran_kemaskini" rows="5" required></textarea>

    <input type="file" wire:model="lampiran" multiple accept=".pdf,.jpg,.png" />

    <button type="submit">Hantar Permohonan</button>
</form>
```

---

## Larangan

- Jangan biar `no_tiket` boleh diedit oleh pengguna
- Jangan simpan fail lampiran di `public/` — guna `storage/app/local`
- Jangan skip log audit selepas simpan

---

## Kriteria Penerimaan

- [ ] No. tiket auto-jana dengan format betul
- [ ] Validasi URL wajib lulus
- [ ] Lampiran disimpan dan boleh diakses semula
- [ ] Notifikasi emel ke Pentadbir dihantar
- [ ] Log audit direkod

---

*ICTServe M04 | UC02 | April 2026*
