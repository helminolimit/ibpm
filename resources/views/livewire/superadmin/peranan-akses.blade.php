<div>
    <flux:main container>

        <div class="mb-6">
            <flux:heading size="xl">Peranan &amp; Akses Modul</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Konfigurasi akses modul mengikut peranan</flux:text>
        </div>

        {{-- Role Selector --}}
        <div class="mb-6 flex gap-2">
            @foreach (['pentadbir' => 'Pentadbir BPM', 'teknician' => 'Teknician ICT', 'pengguna' => 'Pengguna'] as $value => $label)
                <flux:button
                    wire:click="$set('selectedRole', '{{ $value }}')"
                    :variant="$selectedRole === '{{ $value }}' ? 'primary' : 'outline'"
                    size="sm"
                >
                    {{ $label }}
                </flux:button>
            @endforeach
        </div>

        {{-- Module Access Grid --}}
        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-700 dark:text-zinc-300">Modul</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-zinc-700 dark:text-zinc-300">Lihat</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-zinc-700 dark:text-zinc-300">Tambah</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-zinc-700 dark:text-zinc-300">Kemaskini</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-zinc-700 dark:text-zinc-300">Padam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @php
                        $moduleLabels = [
                            'M01' => 'M01 — Aduan ICT',
                            'M02' => 'M02 — Permohonan Perisian',
                            'M03' => 'M03 — Permohonan Perkakasan',
                            'M04' => 'M04 — Permohonan Rangkaian',
                            'M05' => 'M05 — Pinjaman ICT',
                            'M06' => 'M06 — Laporan & Statistik',
                        ];
                    @endphp
                    @foreach ($moduleLabels as $code => $label)
                        <tr class="bg-white dark:bg-zinc-900">
                            <td class="px-4 py-3 text-sm font-medium">{{ $label }}</td>
                            @foreach (['view', 'create', 'update', 'delete'] as $perm)
                                <td class="px-4 py-3 text-center">
                                    <flux:checkbox wire:model="akses.{{ $code }}.{{ $perm }}" />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end">
            <flux:button wire:click="simpanKonfigurasi" variant="primary" wire:loading.attr="disabled">
                Simpan Konfigurasi
            </flux:button>
        </div>

    </flux:main>
</div>
