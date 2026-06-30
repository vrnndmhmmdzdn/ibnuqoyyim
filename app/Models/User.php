<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Guru\Models\Guru;
use Illuminate\Support\Facades\Auth;

#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * Mengatur tipe data/casting atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke model Guru.
     */
    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    /**
     * Membatasi akses ke panel Filament.
     * Hanya 'admin' dan 'guru' yang diizinkan masuk.
     */


    public function canAccessPanel(Panel $panel): bool
    {
        // Cek apakah user memiliki role yang diizinkan
        $hasAccess = in_array($this->role, ['admin', 'guru']);

        // Jika TIDAK memiliki akses, otomatis paksa logout menggunakan Facade resmi
        if (! $hasAccess) {
            Auth::logout();

            // Menghancurkan session agar bersih dan kembali ke halaman login
            if (request()->hasSession()) {
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }
        }

        return $hasAccess;
    }
}
