# UC05 — Lihat Sejarah Permohonan

Modul: M04 Kemaskini Portal  
Pelakon: Pemohon

---

## Keperluan

- Papar semua permohonan lalu milik pemohon (termasuk selesai)
- Carian mengikut no. tiket atau URL
- Susun mengikut tarikh terbaru
- Export senarai sebagai PDF / Excel (optional)

---

## Route

```php
Route::middleware(['auth', 'peranan:pemohon'])->group(function () {
    Route::get('/kemaskini-portal/sejarah', [PermohonanPortalController::class, 'sejarah'])->name('m04.sejarah');
});
```

---

## Controller

```php
public function sejarah(Request $request)
{
    $senarai = PermohonanPortal::where('pemohon_id', Auth::id())
        ->when($request->carian, function ($q, $carian) {
            $q->where('no_tiket', 'like', "%$carian%")
              ->orWhere('url_halaman', 'like', "%$carian%");
        })
        ->latest()
        ->paginate(15);

    return view('m04.sejarah', compact('senarai'));
}
```

---

## Livewire Component

```php
// app/Livewire/M04/SejarahPermohonan.php

class SejarahPermohonan extends Component
{
    public string $carian = '';

    public function render(): View
    {
        $senarai = PermohonanPortal::where('pemohon_id', Auth::id())
            ->when($this->carian, function ($q) {
                $q->where('no_tiket', 'like', "%{$this->carian}%")
                  ->orWhere('url_halaman', 'like', "%{$this->carian}%");
            })
            ->latest()
            ->paginate(15);

        return view('livewire.m04.sejarah-permohonan', compact('senarai'));
    }
}
```

---

## Blade View

```blade
{{-- resources/views/livewire/m04/sejarah-permohonan.blade.php --}}
<div>
    <input
        type="text"
        wire:model.live.debounce.400ms="carian"
        placeholder="Cari no. tiket atau URL..."
    />

    <table>
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>URL Halaman</th>
                <th>Jenis</th>
                <th>Tarikh Mohon</th>
                <th>Tarikh Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($senarai as $item)
            <tr>
                <td>
                    <a href="{{ route('m04.show', $item) }}">{{ $item->no_tiket }}</a>
                </td>
                <td>{{ $item->url_halaman }}</td>
                <td>{{ ucfirst($item->jenis_perubahan) }}</td>
                <td>{{ $item->tarikh_mohon->format('d M Y') }}</td>
                <td>{{ $item->tarikh_selesai?->format('d M Y') ?? '—' }}</td>
                <td>@include('m04._badge', ['permohonan' => $item])</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Tiada rekod ditemui.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $senarai->links() }}
</div>
```

---

## Scope pada Model (optional)

```php
// app/Models/PermohonanPortal.php

public function scopeMilikPemohon(Builder $query): Builder
{
    return $query->where('pemohon_id', Auth::id());
}

public function scopeCarian(Builder $query, string $carian): Builder
{
    return $query->where(function ($q) use ($carian) {
        $q->where('no_tiket', 'like', "%$carian%")
          ->orWhere('url_halaman', 'like', "%$carian%");
    });
}
```

---

## Larangan

- Jangan papar permohonan pemohon lain walaupun ID dimanipulasi dalam URL
- Jangan buat carian tanpa debounce — akan flood query

---

## Kriteria Penerimaan

- [ ] Semua permohonan lalu dipapar (termasuk selesai)
- [ ] Carian no. tiket dan URL berfungsi masa nyata
- [ ] Tarikh selesai papar `—` jika belum selesai
- [ ] Pagination berfungsi

---

*ICTServe M04 | UC05 | April 2026*
