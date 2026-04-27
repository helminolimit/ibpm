<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['role', 'module_code', 'can_view', 'can_create', 'can_update', 'can_delete'])]
class RoleModuleAccess extends Model
{
    protected $table = 'role_module_access';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean',
            'can_create' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
        ];
    }

    public static function bolehAkses(string $role, string $moduleCode, string $permission): bool
    {
        $record = static::where('role', $role)->where('module_code', $moduleCode)->first();

        return $record ? (bool) $record->{"can_{$permission}"} : false;
    }
}
