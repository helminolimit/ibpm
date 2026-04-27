<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Laporan'],
        ]" />

        {{-- Header --}}
        <div class="mb-6">
            <flux:heading size="xl">Jana Laporan Aduan</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                {{ auth()->user()->isPentadbir() ? 'Unit: '.auth()->user()->bahagian : 'Semua Unit' }}
            </flux:text>
        </div>

        {{-- Filter Form --}}
        <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm" class="mb-4">Parameter Laporan</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:label class="mb-1 block text-sm">Tarikh Dari <span class="text-red-500">*</span></flux:label>
                    <flux:input type="date" wire:model="tarikhDari" />
                    @error('tarikhDari')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:label class="mb-1 block text-sm">Tarikh Hingga <span class="text-red-500">*</span></flux:label>
                    <flux:input type="date" wire:model="tarikhHingga" />
                    @error('tarikhHingga')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:label class="mb-1 block text-sm">Kategori</flux:label>
                    <flux:select wire:model="filterKategori">
                        <flux:select.option value="">Semua Kategori</flux:select.option>
                        @foreach ($this->kategoriList as $k)
                            <flux:select.option value="{{ $k->id }}">{{ $k->nama }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="mb-1 block text-sm">Status</flux:label>
                    <flux:select wire:model="filterStatus">
                        <flux:select.option value="">Semua Status</flux:select.option>
                        @foreach (\App\Enums\StatusAduan::cases() as $status)
                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                @if (auth()->user()->isSuperadmin())
                    <div>
                        <flux:label class="mb-1 block text-sm">Unit Penerima</flux:label>
                        <flux:select wire:model="filterUnit">
                            <flux:select.option value="">Semua Unit</flux:select.option>
                            @foreach ($this->unitList as $unit)
                                <flux:select.option value="{{ $unit }}">{{ $unit }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif
            </div>

            {{-- Period > 12 months warning --}}
            @if ($periodoLuasWarning)
                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-900/20">
                    <div class="flex items-start gap-3">
                        <flux:icon name="exclamation-triangle" class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Tempoh laporan melebihi 12 bulan</p>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">Laporan dengan tempoh melebihi 12 bulan mungkin mengambil masa lebih lama untuk dijana. Teruskan?</p>
                            <div class="mt-3 flex gap-2">
                                <flux:button variant="primary" size="sm" wire:click="confirmJanaLaporan" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="confirmJanaLaporan">Teruskan</span>
                                    <span wire:loading wire:target="confirmJanaLaporan">Memproses...</span>
                                </flux:button>
                                <flux:button variant="ghost" size="sm" wire:click="batalPeriodoLuas">Ubah Parameter</flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-4 flex justify-end">
                <flux:button
                    variant="primary"
                    icon="magnifying-glass"
                    wire:click="janaLaporan"
                    wire:loading.attr="disabled"
                    wire:target="janaLaporan,confirmJanaLaporan"
                >
                    <span wire:loading.remove wire:target="janaLaporan,confirmJanaLaporan">Jana Laporan</span>
                    <span wire:loading wire:target="janaLaporan,confirmJanaLaporan">Menjana...</span>
                </flux:button>
            </div>
        </div>

        @if ($hasGenerated)
            {{-- Section A: Ringkasan Statistik --}}
            <div class="mb-6">
                <flux:heading size="sm" class="mb-3">Bahagian A — Ringkasan Statistik</flux:heading>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Jumlah Aduan</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums">{{ $this->jumlahAduan }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Aduan Selesai</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-green-600">{{ $this->jumlahSelesai }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Dalam Tindakan</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-yellow-600">{{ $this->jumlahDalamProses }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Aduan Baru</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-blue-600">{{ $this->jumlahBaru }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Purata Masa Penyelesaian</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums">{{ $this->purataMasaPenyelesaian }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">Kadar Penyelesaian</flux:text>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-green-600">{{ $this->kadarPenyelesaian }}</div>
                    </div>
                </div>
            </div>

            {{-- Section B: Pecahan Kategori --}}
            @if ($this->pecahanKategori->isNotEmpty())
                <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-3">Bahagian B — Pecahan mengikut Kategori</flux:heading>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th class="pb-2 text-left font-medium text-zinc-500">Kategori</th>
                                <th class="pb-2 text-right font-medium text-zinc-500">Bilangan</th>
                                <th class="pb-2 text-right font-medium text-zinc-500">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->pecahanKategori as $item)
                                <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                    <td class="py-2">{{ $item->kategori?->nama ?? '(Tanpa Kategori)' }}</td>
                                    <td class="py-2 text-right tabular-nums">{{ $item->jumlah }}</td>
                                    <td class="py-2 text-right tabular-nums text-zinc-500">
                                        {{ $this->jumlahAduan > 0 ? round(($item->jumlah / $this->jumlahAduan) * 100, 1).'%' : '0%' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Section C: Senarai Terperinci --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <flux:heading size="sm">Bahagian C — Senarai Aduan Terperinci</flux:heading>
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="exportExcel" icon="arrow-down-tray" size="sm" variant="outline" wire:loading.attr="disabled" wire:target="exportExcel">
                            Excel
                        </flux:button>
                        <flux:button wire:click="exportPdf" icon="arrow-down-tray" size="sm" variant="outline" wire:loading.attr="disabled" wire:target="exportPdf">
                            PDF
                        </flux:button>
                    </div>
                </div>

                {{-- Per-page + search --}}
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <flux:select wire:model.live="perPage" class="w-20">
                            <flux:select.option value="10">10</flux:select.option>
                            <flux:select.option value="25">25</flux:select.option>
                            <flux:select.option value="50">50</flux:select.option>
                            <flux:select.option value="100">100</flux:select.option>
                        </flux:select>
                        <span class="text-sm text-zinc-500">setiap halaman</span>
                    </div>
                    <flux:text size="sm" class="text-zinc-400">
                        Jumlah {{ $this->aduan->total() }} rekod
                    </flux:text>
                </div>

                @if ($this->aduan->isEmpty())
                    <div class="rounded-lg border border-zinc-200 bg-white py-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:icon name="document-magnifying-glass" class="mx-auto mb-3 size-10 text-zinc-300 dark:text-zinc-600" />
                        <flux:text class="text-zinc-500">Tiada rekod ditemui untuk tempoh dan kriteria yang dipilih.</flux:text>
                    </div>
                @else
                    <flux:table :paginate="$this->aduan">
                        <flux:table.columns>
                            <flux:table.column class="w-12">Bil</flux:table.column>
                            <flux:table.column
                                sortable
                                :sorted="$sortBy === 'no_tiket'"
                                :direction="$sortDirection"
                                wire:click="sort('no_tiket')"
                            >No. Tiket</flux:table.column>
                            <flux:table.column>Pemohon</flux:table.column>
                            <flux:table.column>Kategori</flux:table.column>
                            <flux:table.column>Lokasi</flux:table.column>
                            <flux:table.column
                                sortable
                                :sorted="$sortBy === 'created_at'"
                                :direction="$sortDirection"
                                wire:click="sort('created_at')"
                            >Tarikh Mohon</flux:table.column>
                            <flux:table.column
                                sortable
                                :sorted="$sortBy === 'tarikh_selesai'"
                                :direction="$sortDirection"
                                wire:click="sort('tarikh_selesai')"
                            >Tarikh Selesai</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column>Penanggung Jawab</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($this->aduan as $loop_index => $aduan)
                                <flux:table.row :key="$aduan->id" wire:key="aduan-{{ $aduan->id }}">
                                    <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                                        {{ $this->aduan->firstItem() + $loop_index }}
                                    </flux:table.cell>
                                    <flux:table.cell class="font-mono text-sm font-medium">
                                        {{ $aduan->no_tiket }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div>{{ $aduan->user?->name ?? '-' }}</div>
                                        <div class="text-xs text-zinc-400">{{ $aduan->user?->bahagian ?? '' }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-sm">{{ $aduan->kategori?->nama ?? '-' }}</flux:table.cell>
                                    <flux:table.cell class="text-sm text-zinc-500">{{ $aduan->lokasi }}</flux:table.cell>
                                    <flux:table.cell class="text-sm text-zinc-500">
                                        {{ $aduan->created_at->format('d/m/Y') }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-sm text-zinc-500">
                                        {{ $aduan->tarikh_selesai?->format('d/m/Y') ?? '-' }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge color="{{ $aduan->status->color() }}" size="sm">
                                            {{ $aduan->status->label() }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-sm text-zinc-500">
                                        {{ $aduan->pentadbir?->name ?? '-' }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="9" class="py-12 text-center text-zinc-500">
                                        Tiada rekod ditemui untuk tempoh dan kriteria yang dipilih.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>
        @endif

    </flux:main>
</div>
