<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * The company details, read once per request. Every place that shows a phone
 * number, an email address, a handle or the head office reads them from here,
 * so the site has one source for them and changing one changes all of them.
 *
 * Nothing here invents a value: a profile link is only built from a handle that
 * exists, and the tel: link is the stored number in international form.
 */
class SiteSettings
{
    /** @var array<string, string>|null */
    private ?array $values = null;

    public function get(string $key, string $default = ''): string
    {
        $this->values ??= SiteSetting::query()->pluck('value', 'key')->all();

        $value = $this->values[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $default;
    }

    public function companyName(): string
    {
        return $this->get('company_name');
    }

    public function phone(): string
    {
        return $this->get('phone');
    }

    /**
     * The stored number as a tel: link. Indonesian numbers are published in
     * local form (0811…) and dialled internationally (+62811…).
     */
    public function phoneLink(): ?string
    {
        return PhoneLink::tel($this->phone());
    }

    public function email(): string
    {
        return $this->get('email');
    }

    public function emailLink(): ?string
    {
        return $this->email() ? 'mailto:'.$this->email() : null;
    }

    public function instagramHandle(): string
    {
        return $this->get('instagram_handle');
    }

    /**
     * The profile URL for a stored handle. Without a handle there is no link —
     * an address is never guessed.
     */
    public function instagramUrl(): ?string
    {
        $handle = ltrim($this->instagramHandle(), '@');

        return $handle ? 'https://www.instagram.com/'.$handle : null;
    }

    public function tiktokHandle(): string
    {
        return $this->get('tiktok_handle');
    }

    public function tiktokUrl(): ?string
    {
        $handle = ltrim($this->tiktokHandle(), '@');

        return $handle ? 'https://www.tiktok.com/@'.$handle : null;
    }

    /**
     * The head office address, one line per row as it is stored.
     *
     * @return array<int, string>
     */
    public function headOfficeLines(): array
    {
        $address = $this->get('head_office_address');

        return array_values(array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $address) ?: []),
            fn (string $line) => $line !== ''
        ));
    }

    public function headOfficeCity(): string
    {
        return $this->get('head_office_city');
    }
}
