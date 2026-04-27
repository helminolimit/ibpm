<div>
    <flux:main container>

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Pengurusan Pengguna</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Urus semua akaun pengguna sistem</flux:text>
            </div>
            <flux:button wire:click="bukaTambah" variant="primary" icon="plus">
                Tambah Pengguna
            </flux:button>
        </div>

        {{-- Stats --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Aktif</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums text-green-600">{{ $this->jumlahAktif }}</div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Pending</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums text-yellow-600">{{ $this->jumlahPending }}</div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Tidak Aktif</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums text-red-600">{{ $this->jumlahTidakAktif }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="filterRole" class="w-44">
                    <flux:select.option value="">Semua Peranan</flux:select.option>
                    @foreach (\App\Enums\RolePengguna::cases() as $role)
                        <flux:select.option value="{{ $role->value }}">{{ $role->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="filterStatus" class="w-44">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    @foreach (\App\Enums\StatusPengguna::cases() as $s)
                        <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <flux:select wire:model.live="perPage" class="w-20">
                        <flux:select.option value="10">10</flux:select.option>
                        <flux:select.option value="25">25</flux:select.option>
                        <flux:select.option value="50">50</flux:select.option>
                    </flux:select>
                    <span class="text-sm text-zinc-500">setiap halaman</span>
                </div>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Nama / email / jawatan..." clearable size="sm" class="w-60" />
            </div>
        </div>

        {{-- Table --}}
        <flux:table :paginate="$this->pengguna">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column>Nama</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Jawatan</flux:table.column>
                <flux:table.column>Bahagian</flux:table.column>
                <flux:table.column>Peranan</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->pengguna as $loop_index => $pengguna)
                    <flux:table.row :key="$pengguna->id" wire:key="pengguna-{{ $pengguna->id }}">
                        <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                            {{ $this->pengguna->firstItem() + $loop_index }}
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $pengguna->name }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">{{ $pengguna->email }}</flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $pengguna->jawatan ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">{{ $pengguna->bahagian ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="blue">{{ $pengguna->role->label() }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $pengguna->status->color() }}">
                                {{ $pengguna->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    wire:click="bukaEdit({{ $pengguna->id }})"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                >
                                    Edit
                                </flux:button>
                                @if ($pengguna->isPending())
                                    <flux:button
                                        wire:click="lulusPending({{ $pengguna->id }})"
                                        wire:confirm="Lulus akaun {{ $pengguna->name }}?"
                                        variant="ghost"
                                        size="sm"
                                        icon="check-circle"
                                        class="text-green-600"
                                    >
                                        Lulus
                                    </flux:button>
                                @else
                                    <flux:button
                                        wire:click="konfirmStatus({{ $pengguna->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="{{ $pengguna->isAktif() ? 'x-circle' : 'check-circle' }}"
                                    >
                                        {{ $pengguna->isAktif() ? 'Nyahaktif' : 'Aktifkan' }}
                                    </flux:button>
                                @endif
                                <flux:button
                                    wire:click="konfirmPadam({{ $pengguna->id }}, '{{ addslashes($pengguna->name) }}')"
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-500"
                                >
                                    Padam
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            Tiada pengguna sepadan dengan carian anda.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:main>

    {{-- Modal: Tambah Pengguna --}}
    <flux:modal name="modal-tambah" class="w-full max-w-lg">
        <div class="space-y-4">
            <flux:heading size="lg">Tambah Pengguna Baharu</flux:heading>

            <flux:field>
                <flux:label>Nama Penuh</flux:label>
                <flux:input wire:model="name" placeholder="Nama penuh" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" placeholder="email@motac.gov.my" />
                <flux:error name="email" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Jawatan</flux:label>
                    <flux:input wire:model="jawatan" placeholder="Jawatan" />
                    <flux:error name="jawatan" />
                </flux:field>
                <flux:field>
                    <flux:label>Gred</flux:label>
                    <flux:input wire:model="gred" placeholder="Gred (opsional)" />
                    <flux:error name="gred" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Bahagian</flux:label>
                <flux:input wire:model="bahagian" placeholder="Bahagian" />
                <flux:error name="bahagian" />
            </flux:field>

            <flux:field>
                <flux:label>Unit</flux:label>
                <flux:input wire:model="unit" placeholder="Unit (opsional)" />
                <flux:error name="unit" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Peranan</flux:label>
                    <flux:select wire:model="role">
                        @foreach (\App\Enums\RolePengguna::cases() as $r)
                            <flux:select.option value="{{ $r->value }}">{{ $r->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="role" />
                </flux:field>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        @foreach (\App\Enums\StatusPengguna::cases() as $s)
                            <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Kata Laluan</flux:label>
                <flux:input wire:model="password" type="password" placeholder="Min. 8 aksara" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="simpanPengguna" variant="primary" wire:loading.attr="disabled">
                    Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Edit Pengguna --}}
    <flux:modal name="modal-edit" class="w-full max-w-lg">
        <div class="space-y-4">
            <flux:heading size="lg">Kemaskini Pengguna</flux:heading>

            <flux:field>
                <flux:label>Nama Penuh</flux:label>
                <flux:input wire:model="name" placeholder="Nama penuh" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" placeholder="email@motac.gov.my" />
                <flux:error name="email" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Jawatan</flux:label>
                    <flux:input wire:model="jawatan" placeholder="Jawatan" />
                    <flux:error name="jawatan" />
                </flux:field>
                <flux:field>
                    <flux:label>Gred</flux:label>
                    <flux:input wire:model="gred" placeholder="Gred (opsional)" />
                    <flux:error name="gred" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Bahagian</flux:label>
                <flux:input wire:model="bahagian" placeholder="Bahagian" />
                <flux:error name="bahagian" />
            </flux:field>

            <flux:field>
                <flux:label>Unit</flux:label>
                <flux:input wire:model="unit" placeholder="Unit (opsional)" />
                <flux:error name="unit" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Peranan</flux:label>
                    <flux:select wire:model="role">
                        @foreach (\App\Enums\RolePengguna::cases() as $r)
                            <flux:select.option value="{{ $r->value }}">{{ $r->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="role" />
                </flux:field>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        @foreach (\App\Enums\StatusPengguna::cases() as $s)
                            <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Kata Laluan Baharu (opsional)</flux:label>
                <flux:input wire:model="password" type="password" placeholder="Kosongkan jika tidak tukar" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="kemaskiniPengguna" variant="primary" wire:loading.attr="disabled">
                    Kemaskini
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Togol Status --}}
    <flux:modal name="modal-status" class="max-w-sm">
        <div class="space-y-4">
            <flux:heading size="lg">Tukar Status Akaun</flux:heading>
            <flux:text>Anda pasti untuk menukar status akaun ini?</flux:text>
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="togolStatus" variant="primary" wire:loading.attr="disabled">
                    Ya, Tukar
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Padam Pengguna --}}
    <flux:modal name="modal-padam" class="max-w-sm">
        <div class="space-y-4">
            <flux:heading size="lg">Padam Akaun Pengguna</flux:heading>
            <flux:text>Anda pasti untuk memadam akaun <strong>{{ $padamNama }}</strong>? Tindakan ini tidak boleh dibatalkan.</flux:text>
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="padamPengguna" variant="danger" wire:loading.attr="disabled">
                    Ya, Padam
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
