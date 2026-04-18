<x-mail::message>
# Aduan ICT Anda Telah Diterima

Terima kasih kerana menggunakan sistem ICTServe. Aduan ICT anda telah berjaya dihantar.

**Nombor Tiket:** {{ $aduan->no_tiket }}

<x-mail::panel>
**Tajuk:** {{ $aduan->tajuk }}

**Kategori:** {{ $aduan->kategori->nama }}

**Unit BPM Berkenaan:** {{ $aduan->kategori->unit_bpm }}

**Lokasi:** {{ $aduan->lokasi }}

**Tarikh Dihantar:** {{ $aduan->created_at->format('d/m/Y H:i') }}

**Status:** {{ $aduan->status->label() }}
</x-mail::panel>

Anda boleh memantau status aduan anda melalui portal ICTServe. Simpan nombor tiket di atas sebagai rujukan.

Sekiranya ada pertanyaan lanjut, sila hubungi unit BPM berkenaan.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
