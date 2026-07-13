<?php

namespace App\Models;

use App\Concerns\HasSyncableUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dish extends Model
{
    use HasSyncableUuid;

    protected $fillable = [
        'dish_category_id',
        'name',
        'description',
        'image_path',
        'price',
        'status',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public const STATUSES = ['active', 'temporarily_inactive', 'inactive'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DishCategory::class, 'dish_category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active'               => 'Activo',
            'temporarily_inactive' => 'Temporalmente inactivo',
            'inactive'             => 'Inactivo',
            default                => $this->status,
        };
    }
}
