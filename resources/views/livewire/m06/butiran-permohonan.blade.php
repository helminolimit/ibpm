<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Kumpulan Emel', 'url' => route('kumpulan-emel.index')],
            ['label' => $this->permohonan->no_tiket],
        ]" />

        {{-- Header --}}
        <div class="mb-6 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" class="font-mono">{{ $this->permohonan->no_tiket }}</flux:heading>
                    <flux:badge color="{{ $this->permohonan->status->color() }}" size="lg">
                        {{ $this->permohonan->status->label() }}
                    </flux:badge>
                </div>
                <flux:text class="mt-1 text-zinc-500">
                    Dihantar pada {{ $this->permohonan->created_at->format('d/m/Y, H:i') }}
                </flux:text>
            </div>
            <flux:button :href="route('kumpulan-emel.index')" wire:navigate variant="ghost" icon="arrow-left">
                Kembali
            </flux:button>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Left column: main info --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Maklumat Permohonan --}}
                <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <flux:heading size="sm">Maklumat Permohonan</flux:heading>
                    </div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Kumpulan Emel</flux:text>
                            <flux:text class="font-medium">{{ $this->permohonan->kumpulanEmel->nama_kumpulan }}</flux:text>
                            <flux:text size="sm" class="text-zinc-400">{{ $this->permohonan->kumpulanEmel->alamat_emel }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Jenis Tindakan</flux:text>
                            <flux:badge
                                color="{{ $this->permohonan->jenis_tindakan->value === 'tambah' ? 'green' : 'red' }}"
                                size="sm"
                            >
                                {{ $this->permohonan->jenis_tindakan->label() }}
                            </flux:badge>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Pemohon</flux:text>
                            <flux:text class="font-medium">{{ $this->permohonan->user->name }}</flux:text>
                            <flux:text size="sm" class="text-zinc-400">{{ $this->permohonan->user->email }}</flux:text>
                        </div>
                        @if ($this->permohonan->catatan_pemohon)
                            <div class="sm:col-span-2">
                                <flux:text size="sm" class="text-zinc-500">Catatan Pemohon</flux:text>
                                <flux:text class="whitespace-pre-wrap">{{ $this->permohonan->catatan_pemohon }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catatan Pentadbir --}}
                @if ($this->permohonan->catatan_pentadbir)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950">
                        <div class="border-b border-amber-100 px-5 py-4 dark:border-amber-900">
                            <div class="flex items-center gap-2">
                                <flux:icon name="chat-bubble-left-ellipsis" class="size-4 text-amber-600 dark:text-amber-400" />
                                <flux:heading size="sm" class="text-amber-800 dark:text-amber-300">Catatan Pentadbir</flux:heading>
                            </div>
                        </div>
                        <div class="p-5">
                            <flux:text class="whitespace-pre-wrap text-amber-900 dark:text-amber-200">{{ $this->permohonan->catatan_pentadbir }}</flux:text>
                        </div>
                    </div>
                @endif

                {{-- Senarai Ahli --}}
                <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <flux:heading size="sm">
                            {{ $this->permohonan->jenis_tindakan->value === 'tambah' ? 'Senarai Ahli Untuk Ditambah' : 'Senarai Ahli Untuk Dibuang' }}
                            <flux:badge size="sm" color="zinc" class="ml-2">{{ $this->permohonan->ahliKumpulan->count() }} orang</flux:badge>
                        </flux:heading>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Bil</th>
                                    @if ($this->permohonan->jenis_tindakan->value === 'tambah')
                                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Nama Ahli</th>
                                    @endif
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Emel Ahli</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach ($this->permohonan->ahliKumpulan as $i => $ahli)
                                    <tr wire:key="ahli-{{ $ahli->id }}">
                                        <td class="px-5 py-3 tabular-nums text-zinc-400">{{ $i + 1 }}</td>
                                        @if ($this->permohonan->jenis_tindakan->value === 'tambah')
                                            <td class="px-5 py-3 font-medium">{{ $ahli->nama_ahli ?: '-' }}</td>
                                        @endif
                                        <td class="px-5 py-3 text-zinc-600 dark:text-zinc-300">{{ $ahli->emel_ahli }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Right column: log aktiviti --}}
            <div class="space-y-6">
                <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <flux:heading size="sm">Log Aktiviti</flux:heading>
                    </div>
                    <div class="space-y-0 p-5">

                        {{-- Selesai --}}
                        @if ($this->permohonan->selesai_at)
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                        <flux:icon name="check" class="size-4 text-green-600 dark:text-green-400" />
                                    </div>
                                    <div class="w-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
                                </div>
                                <div class="pb-5 pt-1">
                                    <flux:text class="font-medium">Permohonan Selesai</flux:text>
                                    <flux:text size="sm" class="text-zinc-400">{{ $this->permohonan->selesai_at->format('d/m/Y, H:i') }}</flux:text>
                                </div>
                            </div>
                        @endif

                        {{-- Ditolak --}}
                        @if ($this->permohonan->status->value === 'ditolak')
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                                        <flux:icon name="x-mark" class="size-4 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="w-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
                                </div>
                                <div class="pb-5 pt-1">
                                    <flux:text class="font-medium">Permohonan Ditolak</flux:text>
                                    <flux:text size="sm" class="text-zinc-400">{{ $this->permohonan->updated_at->format('d/m/Y, H:i') }}</flux:text>
                                </div>
                            </div>
                        @endif

                        {{-- Dalam Tindakan --}}
                        @if (in_array($this->permohonan->status->value, ['dalam_tindakan', 'selesai', 'ditolak']))
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                                        <flux:icon name="cog-6-tooth" class="size-4 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div class="w-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
                                </div>
                                <div class="pb-5 pt-1">
                                    <flux:text class="font-medium">Sedang Diproses</flux:text>
                                    <flux:text size="sm" class="text-zinc-400">Pentadbir sedang mengendalikan permohonan.</flux:text>
                                </div>
                            </div>
                        @endif

                        {{-- Diterima (always shown) --}}
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="flex size-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                    <flux:icon name="paper-airplane" class="size-4 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="pt-1">
                                <flux:text class="font-medium">Permohonan Diterima</flux:text>
                                <flux:text size="sm" class="text-zinc-400">{{ $this->permohonan->created_at->format('d/m/Y, H:i') }}</flux:text>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </flux:main>
</div>
