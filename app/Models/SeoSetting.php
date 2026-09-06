<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'og_media_id', 'is_indexable'];

    protected function casts(): array
    {
        return [
            'is_indexable' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SeoTranslation::class);
    }
}
