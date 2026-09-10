<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pin',
        'employee_number',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
        ];
    }

    public const ROLES = ['admin', 'manager', 'cashier', 'waiter', 'kitchen'];
    public const STATUSES = ['active', 'inactive'];
    // Roles con acceso a la app de cobro (Flutter); ver Api\V1\Cobro\AuthController::pinLogin.
    public const POS_ROLES = ['admin', 'manager', 'cashier', 'waiter'];

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
