<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = ['name', 'slug', 'main_media_id', 'status', 'sort_order'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function mainMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'main_media_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(VehicleTranslation::class);
    }

    public function galleryMedia(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'vehicle_media')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function translation(string $locale): ?VehicleTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations()->where('locale', $locale)->first();
    }

    /**
     * @param  Builder<Vehicle>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * The name as the showcase heading sets it: the make on one line and the
     * model on the next, which is how the approved page renders "Denza D9".
     *
     * @return array<int, string>
     */
    public function nameLines(): array
    {
        $parts = preg_split('/\s+/', trim($this->name), 2) ?: [];

        return array_values(array_filter($parts, fn (string $part) => $part !== ''));
    }

    /**
     * The sections that point at this vehicle. Deleting is refused while the
     * list is not empty, so a page never loses the vehicle behind its content.
     *
     * @return array<int, string>
     */
    public function usedBy(): array
    {
        $used = [];

        foreach (PageSection::query()->whereNotNull('settings')->get() as $section) {
            foreach ($section->settings ?? [] as $value) {
                if ((int) $value === $this->getKey() && $this->isVehicleSetting($section, $value)) {
                    $used[] = $section->page?->key.' / '.$section->section_key;
                }
            }
        }

        return array_values(array_unique($used));
    }

    public function isInUse(): bool
    {
        return $this->usedBy() !== [];
    }

    /**
     * A settings value only counts as a reference when the section definition
     * says that key holds a vehicle.
     */
    private function isVehicleSetting(PageSection $section, mixed $value): bool
    {
        $definition = config("page_content.pages.{$section->page?->key}.sections.{$section->section_key}.settings", []);

        foreach ($definition as $key => $setting) {
            if (($setting['type'] ?? null) === 'vehicle' && (int) ($section->settings[$key] ?? 0) === (int) $value) {
                return true;
            }
        }

        return false;
    }
}
