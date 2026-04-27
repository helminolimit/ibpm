<?php

namespace App\Livewire\Superadmin;

use App\Enums\RolePengguna;
use App\Models\AuditLog;
use App\Models\RoleModuleAccess;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Peranan & Akses Modul — Superadmin')]
class PerananAkses extends Component
{
    public string $selectedRole = 'pentadbir';

    /** @var array<string, array<string, bool>> */
    public array $akses = [];

    /** @var string[] */
    protected array $modules = ['M01', 'M02', 'M03', 'M04', 'M05', 'M06'];

    /** @var string[] */
    protected array $permissions = ['view', 'create', 'update', 'delete'];

    public function mount(): void
    {
        $this->muatAkses();
    }

    public function updatedSelectedRole(): void
    {
        $this->muatAkses();
    }

    private function muatAkses(): void
    {
        $records = RoleModuleAccess::where('role', $this->selectedRole)
            ->get()
            ->keyBy('module_code');

        $this->akses = [];
        foreach ($this->modules as $module) {
            $record = $records->get($module);
            $this->akses[$module] = [
                'view' => $record?->can_view ?? true,
                'create' => $record?->can_create ?? false,
                'update' => $record?->can_update ?? false,
                'delete' => $record?->can_delete ?? false,
            ];
        }
    }

    public function simpanKonfigurasi(): void
    {
        foreach ($this->akses as $module => $permissions) {
            RoleModuleAccess::updateOrCreate(
                ['role' => $this->selectedRole, 'module_code' => $module],
                [
                    'can_view' => $permissions['view'] ?? false,
                    'can_create' => $permissions['create'] ?? false,
                    'can_update' => $permissions['update'] ?? false,
                    'can_delete' => $permissions['delete'] ?? false,
                ]
            );
        }

        $roleLabel = RolePengguna::from($this->selectedRole)->label();
        AuditLog::catat('Kemaskini Akses Modul', 'M00', "Konfigurasi akses modul untuk peranan {$roleLabel} dikemaskini");

        Flux::toast(variant: 'success', text: 'Konfigurasi akses modul berjaya disimpan.');
    }

    public function render()
    {
        return view('livewire.superadmin.peranan-akses');
    }
}
