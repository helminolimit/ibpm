<?php

namespace App\Livewire\M02\Admin;

use App\Enums\JenisToner;
use App\Models\LogToner;
use App\Models\StokToner;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Inventori Stok Toner')]
class InventoriStok extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterJenis = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public ?int $editId = null;

    public string $modelToner = '';

    public string $jenama = '';

    public string $jenisToner = '';

    public string $warna = '';

    public int $kuantitiAda = 0;

    public int $kuantitiMinimum = 1;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);
    }

    #[Computed]
    public function stok(): LengthAwarePaginator
    {
        return StokToner::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('model_toner', 'like', "%{$this->search}%")
                        ->orWhere('jenama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_toner', $this->filterJenis))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /** @return list<JenisToner> */
    public function getJenisList(): array
    {
        return JenisToner::cases();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function bukaTambah(): void
    {
        $this->resetForm();
        Flux::modal('tambah-stok')->show();
    }

    public function bukaEdit(int $id): void
    {
        $stok = StokToner::findOrFail($id);
        $this->editId = $stok->id;
        $this->modelToner = $stok->model_toner ?? '';
        $this->jenama = $stok->jenama ?? '';
        $this->jenisToner = $stok->jenis_toner instanceof JenisToner
            ? $stok->jenis_toner->value
            : (string) $stok->jenis_toner;
        $this->warna = $stok->warna ?? '';
        $this->kuantitiAda = $stok->kuantiti_ada;
        $this->kuantitiMinimum = $stok->kuantiti_minimum;
        Flux::modal('tambah-stok')->show();
    }

    public function simpan(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $this->validate(
            [
                'modelToner' => ['required', 'string', 'max:100'],
                'jenama' => ['required', 'string', 'max:100'],
                'jenisToner' => ['required', Rule::in(array_column(JenisToner::cases(), 'value'))],
                'warna' => ['nullable', 'string', 'max:100'],
                'kuantitiAda' => ['required', 'integer', 'min:0'],
                'kuantitiMinimum' => ['required', 'integer', 'min:1'],
            ],
            [
                'modelToner.required' => 'Model toner wajib diisi.',
                'modelToner.max' => 'Model toner tidak boleh melebihi 100 aksara.',
                'jenama.required' => 'Jenama wajib diisi.',
                'jenama.max' => 'Jenama tidak boleh melebihi 100 aksara.',
                'jenisToner.required' => 'Jenis toner wajib dipilih.',
                'jenisToner.in' => 'Jenis toner tidak sah.',
                'warna.max' => 'Warna tidak boleh melebihi 100 aksara.',
                'kuantitiAda.required' => 'Kuantiti ada wajib diisi.',
                'kuantitiAda.min' => 'Kuantiti ada tidak boleh kurang dari 0.',
                'kuantitiMinimum.required' => 'Kuantiti minimum wajib diisi.',
                'kuantitiMinimum.min' => 'Kuantiti minimum mestilah sekurang-kurangnya 1.',
            ]
        );

        $duplikat = StokToner::where('model_toner', $this->modelToner)
            ->where('jenama', $this->jenama)
            ->where('jenis_toner', $this->jenisToner)
            ->when($this->editId, fn ($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplikat) {
            $this->addError('modelToner', 'Rekod stok toner ini sudah wujud. Sila gunakan fungsi Edit.');

            return;
        }

        DB::transaction(function () use ($user) {
            if ($this->editId) {
                $stok = StokToner::findOrFail($this->editId);
                $stok->update([
                    'model_toner' => $this->modelToner,
                    'jenama' => $this->jenama,
                    'jenis_toner' => $this->jenisToner,
                    'warna' => $this->warna ?: null,
                    'kuantiti_ada' => $this->kuantitiAda,
                    'kuantiti_minimum' => $this->kuantitiMinimum,
                ]);
            } else {
                $stok = StokToner::create([
                    'model_toner' => $this->modelToner,
                    'jenama' => $this->jenama,
                    'jenis_toner' => $this->jenisToner,
                    'warna' => $this->warna ?: null,
                    'kuantiti_ada' => $this->kuantitiAda,
                    'kuantiti_minimum' => $this->kuantitiMinimum,
                ]);
            }

            LogToner::create([
                'permohonan_toner_id' => null,
                'tindakan' => 'stock_updated',
                'catatan' => "Stok dikemaskini: {$this->modelToner} ({$this->jenisToner}) — {$this->kuantitiAda} unit.",
                'user_id' => $user->id,
            ]);
        });

        $mesej = $this->editId
            ? 'Rekod stok toner berjaya dikemaskini.'
            : 'Rekod stok toner baharu berjaya ditambah.';

        $this->resetForm();
        unset($this->stok);
        Flux::modals()->close();
        Flux::toast(variant: 'success', text: $mesej);
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->modelToner = '';
        $this->jenama = '';
        $this->jenisToner = '';
        $this->warna = '';
        $this->kuantitiAda = 0;
        $this->kuantitiMinimum = 1;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.m02.admin.inventori-stok');
    }
}
