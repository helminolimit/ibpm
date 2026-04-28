<x-layouts::app :title="__('Kelulusan Penamatan Akaun — Peringkat 1')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Kelulusan Penamatan Akaun'],
    ]" />

    <div class="space-y-4">
        <flux:heading size="xl">Permohonan Menunggu Kelulusan Peringkat 1</flux:heading>

        @if (session('berjaya'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ session('berjaya') }}</flux:callout.text>
            </flux:callout>
        @endif
        @if (session('maklum'))
            <flux:callout variant="warning" icon="information-circle">
                <flux:callout.text>{{ session('maklum') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:table :paginate="$senarai">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column>No. Tiket</flux:table.column>
                <flux:table.column>Pemohon</flux:table.column>
                <flux:table.column>Pengguna Sasaran</flux:table.column>
                <flux:table.column>ID Login</flux:table.column>
                <flux:table.column>Tarikh BK</flux:table.column>
                <flux:table.column>Jenis</flux:table.column>
                <flux:table.column class="text-right">Tindakan</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($senarai as $i => $permohonan)
                    <flux:table.row :key="$permohonan->id">
                        <flux:table.cell class="tabular-nums text-sm text-zinc-400">
                            {{ $senarai->firstItem() + $i }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono font-semibold text-sm">{{ $permohonan->no_tiket }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->pemohon?->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->penggunaSasaran?->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-sm">{{ $permohonan->id_login_komputer }}</flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $permohonan->tarikh_berkuat_kuasa->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $permohonan->jenis_tindakan === 'TAMAT' ? 'red' : 'yellow' }}">
                                {{ $permohonan->jenis_tindakan }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Butang Lulus --}}
                                <flux:modal.trigger name="lulus-{{ $permohonan->id }}">
                                    <flux:button size="sm" variant="primary" icon="check">Lulus</flux:button>
                                </flux:modal.trigger>

                                {{-- Butang Tolak --}}
                                <flux:modal.trigger name="tolak-{{ $permohonan->id }}">
                                    <flux:button size="sm" variant="danger" icon="x-mark">Tolak</flux:button>
                                </flux:modal.trigger>
                            </div>

                            {{-- Modal Lulus --}}
                            <flux:modal name="lulus-{{ $permohonan->id }}" class="min-w-[22rem]" :closable="false">
                                <div class="space-y-4">
                                    <flux:heading size="lg">Luluskan Permohonan?</flux:heading>
                                    <flux:subheading>
                                        Tiket <span class="font-mono font-semibold">{{ $permohonan->no_tiket }}</span>
                                        akan dihantar kepada Pentadbir ICT.
                                    </flux:subheading>
                                    <div class="flex gap-2">
                                        <flux:spacer />
                                        <flux:modal.close>
                                            <flux:button variant="ghost">Batal</flux:button>
                                        </flux:modal.close>
                                        <form method="POST" action="{{ route('kelulusan.penamatan.lulus', $permohonan) }}">
                                            @csrf @method('PATCH')
                                            <flux:button type="submit" variant="primary">Ya, Lulus</flux:button>
                                        </form>
                                    </div>
                                </div>
                            </flux:modal>

                            {{-- Modal Tolak --}}
                            <flux:modal name="tolak-{{ $permohonan->id }}" class="min-w-[26rem]" :closable="false">
                                <form method="POST" action="{{ route('kelulusan.penamatan.tolak', $permohonan) }}">
                                    @csrf @method('PATCH')
                                    <div class="space-y-4">
                                        <flux:heading size="lg">Tolak Permohonan?</flux:heading>
                                        <flux:subheading>
                                            Pemohon akan dimaklumkan melalui emel.
                                        </flux:subheading>
                                        <flux:field>
                                            <flux:label>Sebab Penolakan <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                                            <flux:textarea name="catatan" rows="3" placeholder="Nyatakan sebab penolakan..." />
                                            @error('catatan')
                                                <flux:error>{{ $message }}</flux:error>
                                            @enderror
                                        </flux:field>
                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">Batal</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger">Tolak</flux:button>
                                        </div>
                                    </div>
                                </form>
                            </flux:modal>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            Tiada permohonan menunggu kelulusan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</x-layouts::app>
