<x-mail::message>
# Aduan ICT Anda Telah Diselesaikan

Aduan ICT anda telah diselesaikan.

**Nombor Tiket:** {{ $aduan->no_tiket }}

<x-mail::panel>
**Tajuk:** {{ $aduan->tajuk }}

**Tarikh Selesai:** {{ $aduan->updated_at->format('d/m/Y H:i') }}
</x-mail::panel>

Terima kasih kerana menggunakan perkhidmatan ICTServe. Sekiranya masalah berulang, sila hantar aduan baru atau hubungi unit BPM berkenaan.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
