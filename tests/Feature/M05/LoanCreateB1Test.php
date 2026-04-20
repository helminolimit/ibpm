<?php

use App\Enums\LoanStatus;
use App\Enums\RelationshipType;
use App\Livewire\M05\LoanCreate;
use App\Models\Department;
use App\Models\LoanRequest;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

describe('LoanCreate B1 — Maklumat Pemohon', function () {
    beforeEach(function () {
        $this->department = Department::factory()->create();
        $this->user = User::factory()->create([
            'phone' => '03-8888 8888',
            'department_id' => $this->department->id,
            'position' => 'Pegawai Tadbir, N41',
        ]);
    });

    it('auto-fills phone and department from authenticated user on mount', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->assertSet('phone', '03-8888 8888')
            ->assertSet('departmentId', $this->department->id);
    });

    it('shows the form page to authenticated users', function () {
        $this->actingAs($this->user)
            ->get(route('m05.loan.create'))
            ->assertOk()
            ->assertSeeLivewire(LoanCreate::class);
    });

    it('redirects unauthenticated users to login', function () {
        $this->get(route('m05.loan.create'))
            ->assertRedirect(route('login'));
    });

    it('validates phone is required', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '')
            ->set('departmentId', $this->department->id)
            ->call('submit')
            ->assertHasErrors(['phone' => 'required']);
    });

    it('validates phone format', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', 'invalid-phone')
            ->set('departmentId', $this->department->id)
            ->call('submit')
            ->assertHasErrors(['phone' => 'regex']);
    });

    it('validates department is required', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '03-8888 8888')
            ->set('departmentId', null)
            ->call('submit')
            ->assertHasErrors(['departmentId' => 'required']);
    });

    it('creates a loan request for self when on_behalf is off', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '03-8888 8888')
            ->set('departmentId', $this->department->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('m05.loan.index'));

        $loan = LoanRequest::where('applicant_id', $this->user->id)->first();
        expect($loan)->not->toBeNull()
            ->and($loan->on_behalf_of)->toBeNull()
            ->and($loan->status)->toBe(LoanStatus::MenungguSokongan);
    });

    it('updates user phone and department on submit', function () {
        $newDept = Department::factory()->create();

        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '012-3456789')
            ->set('departmentId', $newDept->id)
            ->call('submit');

        expect($this->user->fresh()->phone)->toBe('012-3456789')
            ->and($this->user->fresh()->department_id)->toBe($newDept->id);
    });
});

describe('LoanCreate B1A — Mohon Bagi Pihak Orang Lain', function () {
    beforeEach(function () {
        $this->department = Department::factory()->create();
        $this->onBehalfDept = Department::factory()->create();
        $this->user = User::factory()->create([
            'phone' => '03-8888 8888',
            'department_id' => $this->department->id,
        ]);
    });

    it('clears on-behalf fields when toggle is turned off', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('onBehalf', true)
            ->set('onBehalfName', 'Ahmad bin Ali')
            ->set('onBehalfPosition', 'Pegawai Tadbir, N41')
            ->set('onBehalfPhone', '03-7777 7777')
            ->set('onBehalfDepartmentId', $this->onBehalfDept->id)
            ->set('relationship', RelationshipType::RakanSeunit->value)
            ->set('onBehalf', false)
            ->assertSet('onBehalfName', '')
            ->assertSet('onBehalfPosition', '')
            ->assertSet('onBehalfPhone', '')
            ->assertSet('onBehalfDepartmentId', null)
            ->assertSet('relationship', '');
    });

    it('requires on-behalf fields when toggle is active', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '03-8888 8888')
            ->set('departmentId', $this->department->id)
            ->set('onBehalf', true)
            ->call('submit')
            ->assertHasErrors([
                'onBehalfName' => 'required',
                'onBehalfPosition' => 'required',
                'onBehalfPhone' => 'required',
                'onBehalfDepartmentId' => 'required',
                'relationship' => 'required',
            ]);
    });

    it('creates a loan request with on_behalf_of data', function () {
        Livewire::actingAs($this->user)
            ->test(LoanCreate::class)
            ->set('phone', '03-8888 8888')
            ->set('departmentId', $this->department->id)
            ->set('onBehalf', true)
            ->set('onBehalfName', 'Siti binti Ahmad')
            ->set('onBehalfPosition', 'Pembantu Tadbir, N19')
            ->set('onBehalfPhone', '03-7777 7777')
            ->set('onBehalfDepartmentId', $this->onBehalfDept->id)
            ->set('relationship', RelationshipType::RakanSeunit->value)
            ->call('submit')
            ->assertHasNoErrors();

        $loan = LoanRequest::where('applicant_id', $this->user->id)->first();
        expect($loan->on_behalf_of)->not->toBeNull()
            ->and($loan->on_behalf_of['name'])->toBe('Siti binti Ahmad')
            ->and($loan->on_behalf_of['position'])->toBe('Pembantu Tadbir, N19')
            ->and($loan->on_behalf_of['phone'])->toBe('03-7777 7777')
            ->and($loan->on_behalf_of['relationship'])->toBe(RelationshipType::RakanSeunit->value);
    });
});
