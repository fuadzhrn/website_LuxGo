<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'section_key', 'settings', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageSectionTranslation::class);
    }

    /**
     * Images this section shows, one per slot. The same relation serves every
     * page, so a new page needs no new media plumbing.
     */
    public function sectionMedia(): HasMany
    {
        return $this->hasMany(PageSectionMedia::class)->orderBy('sort_order');
    }

    /**
     * FAQ entries this section renders, in display order. Empty for the
     * sections that do not host a FAQ list.
     */
    public function faqItems(): HasMany
    {
        return $this->hasMany(FaqItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function mediaForSlot(string $slot): ?Media
    {
        return $this->sectionMedia->firstWhere('slot', $slot)?->media;
    }

    /**
     * The translation for one locale, without loading the others.
     */
    public function translation(string $locale): ?PageSectionTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations()->where('locale', $locale)->first();
    }
}
