<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApkRelease extends Model
{
    protected $fillable = [
        'version',
        'original_name',
        'file_path',
        'size_bytes',
        'notes',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function formattedSize(): string
    {
        $mb = $this->size_bytes / 1048576;
        return number_format($mb, 1) . ' MB';
    }
}
