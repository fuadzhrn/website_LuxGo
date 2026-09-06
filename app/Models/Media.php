<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    /**
     * Everywhere a media row can be referenced, as table => column.
     *
     * Keeping the list here means the "is this safe to delete?" question is
     * answered in one place. When page sections start pointing at media, this
     * is the single line that changes.
     *
     * @var array<string, string>
     */
    private const REFERENCES = [
        'vehicles' => 'main_media_id',
        'vehicle_media' => 'media_id',
        'seo_settings' => 'og_media_id',
    ];

    protected $table = 'media';

    protected $fillable = [
        'disk', 'path', 'filename', 'mime_type', 'extension',
        'size_bytes', 'width', 'height', 'alt_text', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Public URL built from the disk, so moving storage never means rewriting rows.
     */
    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * True when something still points at this row. Deleting is refused while
     * that is the case, so content never loses its image behind the editor's back.
     */
    public function isInUse(): bool
    {
        return $this->usedBy() !== [];
    }

    /**
     * The tables currently referencing this row.
     *
     * @return array<int, string>
     */
    public function usedBy(): array
    {
        $used = [];

        foreach (self::REFERENCES as $table => $column) {
            /* Guards the list against a table that a later migration has not
               created yet, so adding a reference is never a breaking change. */
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (DB::table($table)->where($column, $this->getKey())->exists()) {
                $used[] = $table;
            }
        }

        return $used;
    }

    public function humanSize(): string
    {
        $bytes = $this->size_bytes;

        if (! $bytes) {
            return '—';
        }

        return $bytes >= 1048576
            ? round($bytes / 1048576, 1).' MB'
            : max(1, (int) round($bytes / 1024)).' KB';
    }

    public function dimensionsLabel(): string
    {
        return $this->width && $this->height
            ? $this->width.' × '.$this->height
            : '—';
    }
}
