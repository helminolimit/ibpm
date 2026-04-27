# `resources/views/livewire/m02/borang-permohonan.blade.php`

```blade
<div class="max-w-2xl mx-auto">

    {{-- Mesej berjaya --}}
    @if ($berjaya)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center mb-6">
            <div class="text-green-600 text-4xl mb-2">✓</div>
            <h2 class="text-lg font-semibold text-green-800 mb-1">Permohonan Berjaya Dihantar</h2>
            <p class="text-green-700 text-sm mb-4">
                No. Tiket anda: <span class="font-mono font-bold">{{ $no_tiket_baru }}</span>
            </p>
            <a href="{{ route('m02.senarai') }}"
               class="inline-block bg-green-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-green-700">
                Lihat Senarai Permohonan
            </a>
        </div>
    @else

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-1">Permohonan Toner Printer</h1>
        <p class="text-sm text-gray-500 mb-6">Sila isi semua maklumat yang bertanda <span class="text-red-500">*</span></p>

        {{-- Maklumat Pemohon --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 class="text-sm font-medium text-gray-600 mb-3">Maklumat Pemohon</h2>
            <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                <div><span class="text-gray-400">Nama:</span> {{ $nama_penuh }}</div>
                <div><span class="text-gray-400">Jawatan:</span> {{ $jawatan }}</div>
                <div><span class="text-gray-400">Bahagian:</span> {{ $bahagian_unit }}</div>
                <div><span class="text-gray-400">No. Tel:</span> {{ $no_telefon }}</div>
            </div>
        </div>

        {{-- Maklumat Toner --}}
        <div class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Model Pencetak <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="model_pencetak"
                           placeholder="cth: HP LaserJet Pro M404n"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    @error('model_pencetak')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenama Toner <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="jenama_toner"
                           placeholder="cth: HP, Canon, Epson"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    @error('jenama_toner')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis / Warna Toner <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="jenis_toner"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="hitam">Hitam (Black)</option>
                        <option value="cyan">Cyan</option>
                        <option value="magenta">Magenta</option>
                        <option value="kuning">Kuning (Yellow)</option>
                    </select>
                    @error('jenis_toner')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kuantiti Diperlukan <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           wire:model="kuantiti"
                           min="1" max="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    @error('kuantiti')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        No. Siri / Kod Toner
                    </label>
                    <input type="text"
                           wire:model="no_siri_toner"
                           placeholder="Pilihan"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tarikh Diperlukan
                    </label>
                    <input type="date"
                           wire:model="tarikh_diperlukan"
                           min="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lokasi Pencetak <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       wire:model="lokasi_pencetak"
                       placeholder="cth: Bilik 3.01, Tingkat 3, Blok A"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                @error('lokasi_pencetak')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tujuan Permohonan <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="tujuan"
                          rows="3"
                          placeholder="Terangkan keperluan toner secara ringkas..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('tujuan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lampiran (Foto pencetak / toner semasa)
                </label>
                <input type="file"
                       wire:model="lampiran"
                       accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG atau PDF. Maksimum 2MB.</p>
                @error('lampiran')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Butang Hantar --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
            <a href="{{ route('m02.senarai') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
            <button wire:click="hantar"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                <span wire:loading.remove>Hantar Permohonan</span>
                <span wire:loading>Menghantar...</span>
            </button>
        </div>
    </div>

    @endif
</div>
```
