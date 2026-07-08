<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'siswa_id', 'nip', 'mapel', 'jenis_kelamin', 'telp', 'alamat'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relasi ke model Siswa (jika role adalah siswa).
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke model MataPelajaran (jika role adalah guru).
     */
    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class, 'guru_id');
    }

    /**
     * Relasi ke model AppNotification (semua notifikasi in-app).
     */
    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class, 'user_id');
    }

    /**
     * Relasi ke model AppNotification (notifikasi yang belum dibaca).
     */
    public function unreadAppNotifications()
    {
        return $this->hasMany(AppNotification::class, 'user_id')->where('is_read', false);
    }

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
        ];
    }
}
