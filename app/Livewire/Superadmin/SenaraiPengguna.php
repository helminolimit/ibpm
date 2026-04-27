<?php

namespace App\Livewire\Superadmin;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Mail\AkaunAktif;
use App\Mail\AkaunBaharu;
use App\Mail\AkaunTidakAktif;
use App\Mail\PerananDikemaskini;
use App\Models\AuditLog;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengurusan Pengguna — Superadmin')]
class SenaraiPengguna extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterRole = '';

    #[Url]
    public string $filterStatus = '';

    public int $perPage = 10;

    // Form fields
    public ?int $editId = null;

    public string $name = '';

    public string $email = '';

    public string $jawatan = '';

    public string $gred = '';

    public string $bahagian = '';

    public string $unit = '';

    public string $role = 'pengguna';

    public string $status = 'pending';

    public string $password = '';

    // Confirm targets
    public ?int $padamId = null;

    public ?string $padamNama = null;

    public ?int $statusId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function bukaTambah(): void
    {
        $this->reset(['editId', 'name', 'email', 'jawatan', 'gred', 'bahagian', 'unit', 'password']);
        $this->role = 'pengguna';
        $this->status = 'pending';
        Flux::modal('modal-tambah')->show();
    }

    public function simpanPengguna(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'jawatan' => ['required', 'string', 'max:100'],
            'gred' => ['nullable', 'string', 'max:20'],
            'bahagian' => ['required', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:100'],
            'role' => ['required', Rule::enum(RolePengguna::class)],
            'status' => ['required', Rule::enum(StatusPengguna::class)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'jawatan' => $this->jawatan,
            'gred' => $this->gred,
            'bahagian' => $this->bahagian,
            'unit_bpm' => $this->unit,
            'role' => $this->role,
            'status' => $this->status,
            'password' => Hash::make($this->password),
        ]);

        Mail::to($user->email)->queue(new AkaunBaharu($user));
        AuditLog::catat('Tambah Pengguna', 'M00', "Akaun baharu dibuat untuk {$user->name} ({$user->email})");

        Flux::modal('modal-tambah')->close();
        unset($this->pengguna, $this->jumlahAktif, $this->jumlahPending, $this->jumlahTidakAktif);
        Flux::toast(variant: 'success', text: 'Pengguna berjaya ditambah.');
    }

    public function bukaEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->jawatan = $user->jawatan ?? '';
        $this->gred = $user->gred ?? '';
        $this->bahagian = $user->bahagian ?? '';
        $this->unit = $user->unit_bpm ?? '';
        $this->role = $user->role->value;
        $this->status = $user->status->value;
        $this->password = '';
        Flux::modal('modal-edit')->show();
    }

    public function kemaskiniPengguna(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editId)],
            'jawatan' => ['required', 'string', 'max:100'],
            'gred' => ['nullable', 'string', 'max:20'],
            'bahagian' => ['required', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:100'],
            'role' => ['required', Rule::enum(RolePengguna::class)],
            'status' => ['required', Rule::enum(StatusPengguna::class)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = User::findOrFail($this->editId);
        $roleChanged = $user->role->value !== $this->role;

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'jawatan' => $this->jawatan,
            'gred' => $this->gred,
            'bahagian' => $this->bahagian,
            'unit_bpm' => $this->unit,
            'role' => $this->role,
            'status' => $this->status,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        if ($roleChanged) {
            Mail::to($user->email)->queue(new PerananDikemaskini($user));
        }

        AuditLog::catat('Kemaskini Pengguna', 'M00', "Maklumat pengguna {$user->name} ({$user->email}) dikemaskini");

        Flux::modal('modal-edit')->close();
        unset($this->pengguna, $this->jumlahAktif, $this->jumlahPending, $this->jumlahTidakAktif);
        Flux::toast(variant: 'success', text: 'Maklumat pengguna berjaya dikemaskini.');
    }

    public function lulusPending(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['status' => StatusPengguna::Aktif]);

        Mail::to($user->email)->queue(new AkaunAktif($user));
        AuditLog::catat('Lulus Akaun', 'M00', "Akaun {$user->name} ({$user->email}) telah diluluskan");

        unset($this->pengguna, $this->jumlahAktif, $this->jumlahPending, $this->jumlahTidakAktif);
        Flux::toast(variant: 'success', text: "Akaun {$user->name} telah diluluskan.");
    }

    public function konfirmStatus(int $id): void
    {
        $this->statusId = $id;
        Flux::modal('modal-status')->show();
    }

    public function togolStatus(): void
    {
        $user = User::findOrFail($this->statusId);

        if ($user->id === Auth::id() && $user->isSuperadmin()) {
            Flux::toast(variant: 'danger', text: 'Tidak boleh nyahaktifkan akaun sendiri.');

            return;
        }

        $newStatus = $user->status === StatusPengguna::Aktif
            ? StatusPengguna::TidakAktif
            : StatusPengguna::Aktif;

        $user->update(['status' => $newStatus]);

        $mail = $newStatus === StatusPengguna::Aktif ? new AkaunAktif($user) : new AkaunTidakAktif($user);
        Mail::to($user->email)->queue($mail);
        AuditLog::catat('Togol Status', 'M00', "Status akaun {$user->name} ditukar kepada {$newStatus->label()}");

        Flux::modal('modal-status')->close();
        $this->statusId = null;
        unset($this->pengguna, $this->jumlahAktif, $this->jumlahPending, $this->jumlahTidakAktif);
        Flux::toast(variant: 'success', text: "Status akaun {$user->name} berjaya dikemaskini.");
    }

    public function konfirmPadam(int $id, string $nama): void
    {
        $this->padamId = $id;
        $this->padamNama = $nama;
        Flux::modal('modal-padam')->show();
    }

    public function padamPengguna(): void
    {
        $user = User::findOrFail($this->padamId);

        if ($user->isSuperadmin() && $user->isAktif()) {
            Flux::toast(variant: 'danger', text: 'Tidak boleh padam akaun Superadmin yang aktif.');

            return;
        }

        AuditLog::catat('Padam Pengguna', 'M00', "Akaun {$user->name} ({$user->email}) dipadamkan");
        $user->delete();

        Flux::modal('modal-padam')->close();
        $this->padamId = null;
        $this->padamNama = null;
        unset($this->pengguna, $this->jumlahAktif, $this->jumlahPending, $this->jumlahTidakAktif);
        Flux::toast(variant: 'success', text: 'Akaun pengguna berjaya dipadamkan.');
    }

    #[Computed]
    public function pengguna(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('jawatan', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function jumlahAktif(): int
    {
        return User::where('status', StatusPengguna::Aktif)->count();
    }

    #[Computed]
    public function jumlahPending(): int
    {
        return User::where('status', StatusPengguna::Pending)->count();
    }

    #[Computed]
    public function jumlahTidakAktif(): int
    {
        return User::where('status', StatusPengguna::TidakAktif)->count();
    }

    public function render()
    {
        return view('livewire.superadmin.senarai-pengguna');
    }
}
