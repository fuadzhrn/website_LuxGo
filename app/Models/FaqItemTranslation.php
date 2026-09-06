<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqItemTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['faq_item_id', 'locale', 'question', 'answer'];

    public function faqItem(): BelongsTo
    {
        return $this->belongsTo(FaqItem::class);
    }
}
