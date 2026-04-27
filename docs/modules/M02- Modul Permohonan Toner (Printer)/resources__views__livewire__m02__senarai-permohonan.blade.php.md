# `resources/views/livewire/m02/senarai-permohonan.blade.php`

```blade
<div>
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-semibold text-gray-800">Permohonan Toner Saya</h1>
        <a href="{{ route('m02.borang') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
            + Permohonan Baru
        </a>
    </div>

    {{-- Filter & Carian --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4 flex gap-3">
        <input type="text"
               wire:model.live.debounce.400ms="carian"
               placeholder="Cari no. tiket atau model pencetak..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />

        <select wire:model.live="status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="submitted">Dihantar</option>
            <option value="reviewing">Dalam Semakan</option>
            <option value="approved">Diluluskan</option>
            <option value="delivered">Toner Dihantar</option>
            <option value="rejected">Ditolak</option>
            <option value="pending_stock">Menunggu Stok</option>
        </select>
    </div>

    {{-- Jadual --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">No. Tiket</th>
                    <th class="px-4 py-3 text-left">Model Pencetak</th>
                    <th class="px-4 py-3 text-left">Jenis Toner</th>
                    <th class="px-4 py-3 text-left">Kuantiti</th>
                    <th class="px-4 py-3 text-left">Tarikh Mohon</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($permohonan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-blue-600">{{ $item->no_tiket }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->model_pencetak }}</td>
                        <td class="px-4 py-3 text-gray-700 capitalize">{{ $item->jenis_toner }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->kuantiti_diminta }} unit</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->submitted_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->warnaStatus() }}">
                                {{ $item->labelStatus() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('m02.butiran', $item->no_tiket) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Tiada permohonan dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($permohonan->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $permohonan->links() }}
            </div>
        @endif
    </div>
</div>
```
