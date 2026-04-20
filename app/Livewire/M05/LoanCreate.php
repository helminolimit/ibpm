<?php

namespace App\Livewire\M05;

use App\Enums\RelationshipType;
use App\Models\Department;
use App\Models\LoanRequest;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Permohonan Pinjaman ICT')]
class LoanCreate extends Component
{
    // Bahagian 1 — editable by applicant
    public string $phone = '';

    public ?int $departmentId = null;

    // Bahagian 1A — on behalf of
    public bool $onBehalf = false;

    public string $onBehalfName = '';

    public string $onBehalfPosition = '';

    public string $onBehalfPhone = '';

    public ?int $onBehalfDepartmentId = null;

    public string $relationship = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->phone = $user->phone ?? '';
        $this->departmentId = $user->department_id;
    }

    public function updatedOnBehalf(): void
    {
        if (! $this->onBehalf) {
            $this->resetOnBehalfFields();
        }
    }

    public function submit(): void
    {
        $this->validate($this->validationRules(), $this->validationMessages());

        $user = Auth::user();

        $user->fill([
            'phone' => $this->phone,
            'department_id' => $this->departmentId,
        ])->save();

        LoanRequest::create([
            'applicant_id' => $user->id,
            'on_behalf_of' => $this->onBehalf ? [
                'name' => $this->onBehalfName,
                'position' => $this->onBehalfPosition,
                'phone' => $this->onBehalfPhone,
                'unit' => Department::find($this->onBehalfDepartmentId)?->name,
                'relationship' => $this->relationship,
            ] : null,
        ]);

        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Permohonan berjaya dihantar.');

        $this->redirect(route('m05.loan.index'), navigate: true);
    }

    #[Computed]
    public function departments(): Collection
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function relationshipTypes(): array
    {
        return RelationshipType::cases();
    }

    private function resetOnBehalfFields(): void
    {
        $this->onBehalfName = '';
        $this->onBehalfPosition = '';
        $this->onBehalfPhone = '';
        $this->onBehalfDepartmentId = null;
        $this->relationship = '';
        $this->resetValidation([
            'onBehalfName',
            'onBehalfPosition',
            'onBehalfPhone',
            'onBehalfDepartmentId',
            'relationship',
        ]);
    }

    /** @return array<string, mixed> */
    private function validationRules(): array
    {
        $phoneRegex = ['required', 'regex:/^(0[0-9]{1,2}[-\s]?[0-9]{3,4}[-\s]?[0-9]{4,5})$/'];

        $rules = [
            'phone' => $phoneRegex,
            'departmentId' => ['required', 'exists:departments,id'],
        ];

        if ($this->onBehalf) {
            $rules['onBehalfName'] = ['required', 'string', 'max:255'];
            $rules['onBehalfPosition'] = ['required', 'string', 'max:255'];
            $rules['onBehalfPhone'] = $phoneRegex;
            $rules['onBehalfDepartmentId'] = ['required', 'exists:departments,id'];
            $rules['relationship'] = ['required', 'string'];
        }

        return $rules;
    }

    /** @return array<string, string> */
    private function validationMessages(): array
    {
        return [
            'phone.required' => 'No. telefon wajib diisi.',
            'phone.regex' => 'Format no. telefon tidak sah. Contoh: 03-8888 8888 atau 012-3456789.',
            'departmentId.required' => 'Bahagian / unit wajib dipilih.',
            'departmentId.exists' => 'Bahagian / unit tidak sah.',
            'onBehalfName.required' => 'Nama penuh kakitangan wajib diisi.',
            'onBehalfPosition.required' => 'Jawatan & gred wajib diisi.',
            'onBehalfPhone.required' => 'No. telefon kakitangan wajib diisi.',
            'onBehalfPhone.regex' => 'Format no. telefon tidak sah. Contoh: 03-8888 8888 atau 012-3456789.',
            'onBehalfDepartmentId.required' => 'Bahagian / unit kakitangan wajib dipilih.',
            'relationship.required' => 'Hubungan / alasan mewakili wajib dipilih.',
        ];
    }

    public function render(): View
    {
        return view('livewire.m05.loan-create');
    }
}
