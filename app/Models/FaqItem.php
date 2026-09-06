<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqItem extends Model
{
    use HasFactory;

    protected $fillable = ['page_section_id', 'shows_usage_breakdown', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'shows_usage_breakdown' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(FaqItemTranslation::class);
    }

    public function translation(string $locale): ?FaqItemTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations()->where('locale', $locale)->first();
    }
}
