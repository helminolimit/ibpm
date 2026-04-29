# UC01 — Log Masuk Sistem

Modul: M04 Kemaskini Portal  
Pelakon: Semua pengguna (Pemohon, Pentadbir, Superadmin)

---

## Keperluan

- Guna Laravel Breeze sebagai auth scaffolding
- Redirect mengikut `role` selepas login
- Sekat akses halaman tanpa sesi aktif

---

## Route

```php
// routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
```

---

## Controller

```php
// app/Http/Controllers/AuthController.php

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return back()->withErrors(['email' => 'Kelayakan tidak sah.']);
    }

    return match (Auth::user()->peranan) {
        'superadmin' => redirect()->route('superadmin.dashboard'),
        'pentadbir'  => redirect()->route('pentadbir.dashboard'),
        default      => redirect()->route('pemohon.dashboard'),
    };
}

public function logout()
{
    Auth::logout();
    return redirect()->route('login');
}
```

---

## Model Pengguna

```php
// app/Models/Pengguna.php

protected $fillable = [
    'nama', 'jawatan', 'bahagian', 'email',
    'password', 'peranan', 'status_akaun',
];

protected $casts = [
    'password' => 'hashed',
];
```

---

## Migration

```php
// database/migrations/xxxx_create_penggunas_table.php

Schema::create('penggunas', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('jawatan');
    $table->string('bahagian');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('peranan', ['pemohon', 'pentadbir', 'superadmin'])->default('pemohon');
    $table->enum('status_akaun', ['aktif', 'tidak_aktif'])->default('aktif');
    $table->timestamps();
});
```

---

## Middleware

```php
// app/Http/Middleware/CheckPeranan.php

public function handle(Request $request, Closure $next, string $peranan)
{
    if (Auth::user()->peranan !== $peranan) {
        abort(403, 'Akses tidak dibenarkan.');
    }
    return $next($request);
}

// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['peranan' => CheckPeranan::class]);
})
```

---

## Blade View

```blade
{{-- resources/views/auth/login.blade.php --}}
<form wire:submit="login">
    <input type="email" wire:model="email" placeholder="Emel" required />
    <input type="password" wire:model="password" placeholder="Kata Laluan" required />
    <button type="submit">Log Masuk</button>
    @error('email') <span>{{ $message }}</span> @enderror
</form>
```

---

## Larangan

- Jangan simpan password plain text
- Jangan redirect semua peranan ke halaman yang sama
- Jangan skip middleware `auth` pada route yang perlu perlindungan

---

## Kriteria Penerimaan

- [ ] Login berjaya → redirect mengikut peranan
- [ ] Login gagal → mesej ralat ditunjuk
- [ ] Akses tanpa login → redirect ke `/login`
- [ ] Logout bersihkan sesi

---

*ICTServe M04 | UC01 | April 2026*
