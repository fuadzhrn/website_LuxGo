<?php

namespace App\Models;

use App\Support\PhoneLink;
use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    /**
     * The statuses in words, in the order a lead moves through them.
     *
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? Str::headline($this->status);
    }

    /**
     * The language the applicant used, in words rather than a locale code.
     */
    public function localeLabel(): string
    {
        return config('admin.locale_labels')[$this->locale] ?? strtoupper($this->locale);
    }

    /**
     * When the lead arrived. Submissions made before submitted_at was recorded
     * fall back to the row's own timestamp.
     */
    public function submittedAt(): ?Carbon
    {
        return $this->submitted_at ?? $this->created_at;
    }

    public function phoneLink(): ?string
    {
        return PhoneLink::tel($this->phone);
    }

    public function emailLink(): ?string
    {
        return $this->email ? 'mailto:'.$this->email : null;
    }
}
