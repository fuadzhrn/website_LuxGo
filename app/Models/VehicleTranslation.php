<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'locale', 'content'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
