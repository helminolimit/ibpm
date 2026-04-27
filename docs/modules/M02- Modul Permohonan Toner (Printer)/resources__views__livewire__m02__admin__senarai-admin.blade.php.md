# `resources/views/livewire/m02/admin/senarai-admin.blade.php`

```blade
<div>
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-semibold text-gray-800">Urus Permohonan Toner</h1>
        <a href="{{ route('admin.m02.stok') }}"
           class="text-sm text-blue-600 hover:underline">
            Inventori Stok →
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4 flex gap-3">
        <input type="text"
               wire:model.live.debounce.400ms="carian"
               placeholder="Cari no. tiket, nama pemohon atau model pencetak..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" />

        <select wire:model.live="status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
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
                    <th class="px-4 py-3 text-left">Pemohon</th>
                    <th class="px-4 py-3 text-left">Model Pencetak</th>
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
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-700">{{ $item->pemohon->name }}</div>
                            <div class="text-gray-400 text-xs">{{ $item->bahagian_pemohon }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->model_pencetak }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->kuantiti_diminta }} unit</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->submitted_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->warnaStatus() }}">
                                {{ $item->labelStatus() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.m02.proses', $item->id) }}"
                               class="text-blue-600 hover:underline text-xs">Proses</a>

                            @if ($item->status === 'approved')
                                <a href="{{ route('admin.m02.hantar', $item->id) }}"
                                   class="text-green-600 hover:underline text-xs">Hantar</a>
                            @endif
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
