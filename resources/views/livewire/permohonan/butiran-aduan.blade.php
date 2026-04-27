<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Aduan Saya', 'url' => route('senarai-saya')],
            ['label' => 'Butiran'],
        ]" />

        {{-- Back link --}}
        <div class="mb-4">
            <flux:button :href="route('senarai-saya')" wire:navigate variant="ghost" icon="arrow-left" size="sm">
                Kembali ke Senarai
            </flux:button>
        </div>

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <flux:heading size="xl" class="font-mono">{{ $this->aduan->no_tiket }}</flux:heading>
                    <flux:badge color="{{ $this->aduan->status->color() }}" size="lg">
                        {{ $this->aduan->status->label() }}
                    </flux:badge>
                </div>
                <flux:text class="mt-1 text-zinc-500">
                    Dihantar pada {{ $this->aduan->created_at->format('d/m/Y, H:i') }}
                </flux:text>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Maklumat Aduan + Sejarah Status (2/3 width) --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Maklumat Aduan --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Maklumat Aduan</flux:heading>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Kategori</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->kategori->nama }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Unit BPM Penerima</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->kategori->unit_bpm }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Lokasi / Bilik</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->lokasi }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">No. Telefon</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->no_telefon }}</flux:text>
                        </div>
                        <div class="sm:col-span-2">
                            <flux:text size="sm" class="text-zinc-500">Tajuk Aduan</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->tajuk }}</flux:text>
                        </div>
                        <div class="sm:col-span-2">
                            <flux:text size="sm" class="text-zinc-500">Keterangan Masalah</flux:text>
                            <flux:text class="whitespace-pre-wrap">{{ $this->aduan->keterangan }}</flux:text>
                        </div>
                    </div>
                </div>

                {{-- Sejarah Status --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Sejarah Status</flux:heading>
                    <div class="space-y-0">
                        @foreach ($this->aduan->statusLogs as $log)
                            <div class="relative flex gap-4">
                                {{-- Connector line --}}
                                @if (! $loop->last)
                                    <div class="absolute left-4 top-8 bottom-0 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                                @endif

                                {{-- Timeline dot --}}
                                <div class="relative flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="size-2.5 rounded-full
                                        @if ($log->status->color() === 'blue') bg-blue-500
                                        @elseif ($log->status->color() === 'yellow') bg-yellow-500
                                        @elseif ($log->status->color() === 'green') bg-green-500
                                        @elseif ($log->status->color() === 'red') bg-red-500
                                        @else bg-zinc-400
                                        @endif
                                    "></div>
                                </div>

                                {{-- Content --}}
                                <div class="min-w-0 flex-1 pb-6">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:badge color="{{ $log->status->color() }}" size="sm">
                                            {{ $log->status->label() }}
                                        </flux:badge>
                                        <flux:text size="sm" class="text-zinc-400">
                                            {{ $log->created_at->format('d/m/Y, H:i') }}
                                        </flux:text>
                                    </div>
                                    @if ($log->catatan)
                                        <flux:text size="sm" class="mt-1 text-zinc-600 dark:text-zinc-400">
                                            {{ $log->catatan }}
                                        </flux:text>
                                    @endif
                                    @if ($log->user)
                                        <flux:text size="sm" class="mt-0.5 text-zinc-400">
                                            oleh {{ $log->user->name }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Sidebar (1/3 width) --}}
            <div class="space-y-6">

                {{-- Maklumat Pemohon --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Maklumat Pemohon</flux:heading>
                    <div class="space-y-3">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Nama</flux:text>
                            <flux:text class="font-medium">{{ auth()->user()->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">E-mel</flux:text>
                            <flux:text class="font-medium">{{ auth()->user()->email }}</flux:text>
                        </div>
                        @if (auth()->user()->bahagian)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Bahagian</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->bahagian }}</flux:text>
                            </div>
                        @endif
                        @if (auth()->user()->jawatan)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Jawatan</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->jawatan }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Lampiran --}}
                @if ($this->aduan->lampiran->isNotEmpty())
                    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Lampiran</flux:heading>
                        <div class="space-y-2">
                            @foreach ($this->aduan->lampiran as $lampiran)
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lampiran->path) }}"
                                    target="_blank"
                                    class="flex items-center gap-3 rounded-md border border-zinc-200 p-3 text-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800"
                                >
                                    <flux:icon
                                        name="{{ str_starts_with($lampiran->jenis_fail, 'image/') ? 'photo' : 'document' }}"
                                        class="size-5 shrink-0 text-zinc-400"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium text-zinc-700 dark:text-zinc-300">{{ $lampiran->nama_fail }}</p>
                                        <p class="text-zinc-400">
                                            {{ $lampiran->saiz >= 1048576 ? number_format($lampiran->saiz / 1048576, 1).' MB' : number_format($lampiran->saiz / 1024, 1).' KB' }}
                                        </p>
                                    </div>
                                    <flux:icon name="arrow-down-tray" class="size-4 shrink-0 text-zinc-400" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </flux:main>
</div>
