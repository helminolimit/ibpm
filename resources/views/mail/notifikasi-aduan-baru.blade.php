<x-mail::message>
# Aduan ICT Baru Diterima

Aduan ICT baru telah dihantar dan memerlukan perhatian unit anda.

**Nombor Tiket:** {{ $aduan->no_tiket }}

<x-mail::panel>
**Pemohon:** {{ $aduan->user->name }}

**Bahagian:** {{ $aduan->user->bahagian ?? '-' }}

**Jawatan:** {{ $aduan->user->jawatan ?? '-' }}

**No. Telefon:** {{ $aduan->no_telefon }}

**Tajuk:** {{ $aduan->tajuk }}

**Kategori:** {{ $aduan->kategori->nama }}

**Lokasi:** {{ $aduan->lokasi }}

**Keterangan:**
{{ $aduan->keterangan }}

**Tarikh Dihantar:** {{ $aduan->created_at->format('d/m/Y H:i') }}
</x-mail::panel>

Sila log masuk ke portal ICTServe untuk memproses aduan ini.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
