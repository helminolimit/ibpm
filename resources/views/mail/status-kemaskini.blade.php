<x-mail::message>
# Kemaskini Status Aduan ICT Anda

Aduan ICT anda sedang dalam tindakan oleh unit berkaitan.

**Nombor Tiket:** {{ $aduan->no_tiket }}

<x-mail::panel>
**Tajuk:** {{ $aduan->tajuk }}

**Kategori:** {{ $aduan->kategori->nama }}

**Status Terkini:** {{ $aduan->status->label() }}

**Tarikh Kemaskini:** {{ $aduan->updated_at->format('d/m/Y H:i') }}
</x-mail::panel>

Anda akan dimaklumkan semula apabila aduan diselesaikan. Simpan nombor tiket di atas sebagai rujukan.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
