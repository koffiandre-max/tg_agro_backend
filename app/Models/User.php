<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TypeTechnicien;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active', 'type_technicien', 'is_verified', 'verified_at', 'verification_token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function technician(): HasOne
    {
        return $this->hasOne(Technician::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'utilisateur_id')
            ->orderBy('date_creation', 'desc');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'utilisateur_id')
            ->where('lue', false)
            ->orderBy('date_creation', 'desc');
    }

    public function roleModel()
    {
        return Role::where('slug', $this->role)->first();
    }

    public function hasPermission(string $slug): bool
    {
        $role = $this->roleModel();

        if (!$role) {
            return false;
        }

        return $role->permissions()->where('slug', $slug)->exists();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type_technicien' => TypeTechnicien::class,
        ];
    }
}
