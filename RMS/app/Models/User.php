<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasSyncableUuid;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const ROLES = ['admin', 'manager', 'cashier', 'waiter', 'kitchen'];
    public const STATUSES = ['active', 'inactive'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin'   => 'Administrador',
            'manager' => 'Gerente',
            'cashier' => 'Cajero',
            'waiter'  => 'Mesero',
            'kitchen' => 'Cocina',
            default   => $this->role,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            default    => $this->status,
        };
    }
}
