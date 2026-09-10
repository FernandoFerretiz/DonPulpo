<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'motivo',
        'description',
        'expense_date',
        'ticket_photo_path',
    ];

    protected $appends = ['ticket_photo_url'];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTicketPhotoUrlAttribute(): ?string
    {
        return $this->ticket_photo_path
            ? asset('storage/' . $this->ticket_photo_path)
            : null;
    }
}
