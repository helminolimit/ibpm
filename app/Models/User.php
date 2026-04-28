<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'bahagian', 'unit_bpm', 'jawatan', 'no_telefon', 'role', 'status', 'last_login_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RolePengguna::class,
            'status' => StatusPengguna::class,
            'last_login_at' => 'datetime',
        ];
    }

    public function isPelulus1(): bool
    {
        return $this->role === RolePengguna::Pelulus1;
    }

    public function isPentadbir(): bool
    {
        return $this->role === RolePengguna::Pentadbir;
    }

    public function isSuperadmin(): bool
    {
        return $this->role === RolePengguna::Superadmin;
    }

    public function isTeknician(): bool
    {
        return $this->role === RolePengguna::Teknician;
    }

    public function isAdmin(): bool
    {
        return $this->isPentadbir() || $this->isSuperadmin();
    }

    public function isAktif(): bool
    {
        return $this->status === StatusPengguna::Aktif;
    }

    public function isPending(): bool
    {
        return $this->status === StatusPengguna::Pending;
    }

    public function isProfileComplete(): bool
    {
        return filled($this->bahagian)
            && filled($this->jawatan)
            && filled($this->no_telefon);
    }

    public function permohonanPenamatan(): HasMany
    {
        return $this->hasMany(PermohonanPenamatan::class, 'pemohon_id');
    }

    public function penatamatanSasaran(): HasMany
    {
        return $this->hasMany(PermohonanPenamatan::class, 'pengguna_sasaran_id');
    }

    public function kelulusanPenamatan(): HasMany
    {
        return $this->hasMany(KelulusanPenamatan::class, 'pelulus_id');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
