<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipApplication extends Model
{
    use HasFactory;

    public const STATUSES = ['new', 'contacted', 'in_progress', 'completed', 'rejected'];

    protected $fillable = [
        'full_name', 'phone', 'email', 'lots_interested',
        'message', 'locale', 'status', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'lots_interested' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }
}
