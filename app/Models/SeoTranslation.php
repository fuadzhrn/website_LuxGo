<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_setting_id', 'locale',
        'meta_title', 'meta_description', 'og_title', 'og_description',
    ];

    public function seoSetting(): BelongsTo
    {
        return $this->belongsTo(SeoSetting::class);
    }
}
