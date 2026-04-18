<x-mail::message>
# Aduan ICT Ditugaskan kepada Anda

Aduan ICT berikut telah ditugaskan kepada anda untuk tindakan lanjut.

**Nombor Tiket:** {{ $aduan->no_tiket }}

<x-mail::panel>
**Pemohon:** {{ $aduan->user->name }}

**Bahagian:** {{ $aduan->user->bahagian ?? '-' }}

**Kategori:** {{ $aduan->kategori->nama }}

**Lokasi:** {{ $aduan->lokasi }}

**Tarikh Mohon:** {{ $aduan->created_at->format('d/m/Y H:i') }}
</x-mail::panel>

Sila log masuk ke portal ICTServe untuk mengambil tindakan ke atas aduan ini.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
