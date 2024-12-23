<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneNumber extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'ddi',
        'ddd',
        'number',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
