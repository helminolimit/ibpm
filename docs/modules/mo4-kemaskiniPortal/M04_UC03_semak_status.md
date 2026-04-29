# UC03 — Semak Status Permohonan

Modul: M04 Kemaskini Portal  
Pelakon: Pemohon

---

## Keperluan

- Pemohon lihat senarai permohonan sendiri sahaja
- Tapis mengikut status: Semua / Diterima / Dalam Proses / Selesai
- Papar badge berwarna mengikut status
- Klik tiket → lihat butiran penuh

---

## Route

```php
Route::middleware(['auth', 'peranan:pemohon'])->group(function () {
    Route::get('/kemaskini-portal', [PermohonanPortalController::class, 'index'])->name('m04.index');
    Route::get('/kemaskini-portal/{permohonan}', [PermohonanPortalController::class, 'show'])->name('m04.show');
});
```

---

## Controller

```php
public function index(Request $request)
{
    $senarai = PermohonanPortal::where('pemohon_id', Auth::id())
        ->when($request->status, fn($q, $s) => $q->where('status', $s))
        ->latest('tarikh_mohon')
        ->paginate(10);

    return view('m04.index', compact('senarai'));
}

public function show(PermohonanPortal $permohonan)
{
    abort_if($permohonan->pemohon_id !== Auth::id(), 403);

    return view('m04.show', compact('permohonan'));
}
```

---

## Livewire Component

```php
// app/Livewire/M04/SenaraiPermohonan.php

class SenaraiPermohonan extends Component
{
    public string $tapis = '';

    public function render(): View
    {
        $senarai = PermohonanPortal::where('pemohon_id', Auth::id())
            ->when($this->tapis, fn($q) => $q->where('status', $this->tapis))
            ->latest()
            ->paginate(10);

        return view('livewire.m04.senarai-permohonan', compact('senarai'));
    }
}
```

---

## Badge Status (Blade)

```blade
@php
$warna = match($permohonan->status) {
    'diterima'     => 'bg-blue-100 text-blue-800',
    'dalam_proses' => 'bg-yellow-100 text-yellow-800',
    'selesai'      => 'bg-green-100 text-green-800',
    default        => 'bg-gray-100 text-gray-600',
};
$label = match($permohonan->status) {
    'diterima'     => 'Diterima',
    'dalam_proses' => 'Dalam Proses',
    'selesai'      => 'Selesai',
    default        => 'Tidak Diketahui',
};
@endphp
<span class="px-2 py-1 rounded text-xs font-medium {{ $warna }}">{{ $label }}</span>
```

---

## Blade View (senarai)

```blade
{{-- resources/views/m04/index.blade.php --}}
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
                <th>URL Halaman</th>
                <th>Tarikh Mohon</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($senarai as $item)
            <tr>
                <td>{{ $item->no_tiket }}</td>
                <td>{{ $item->url_halaman }}</td>
                <td>{{ $item->tarikh_mohon->format('d M Y') }}</td>
                <td>@include('m04._badge', ['permohonan' => $item])</td>
                <td>
                    <a href="{{ route('m04.show', $item) }}">Lihat</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5">Tiada permohonan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $senarai->links() }}
</div>
```

---

## Larangan

- Jangan papar permohonan pemohon lain
- Jangan biar pemohon edit status dari halaman ini

---

## Kriteria Penerimaan

- [ ] Hanya permohonan milik pemohon dipapar
- [ ] Tapis status berfungsi tanpa reload halaman
- [ ] Badge warna betul mengikut status
- [ ] Klik tiket → halaman butiran penuh

---

*ICTServe M04 | UC03 | April 2026*
