<?php

namespace App\Support;

/**
 * The schema.org description of the site, built from the company details an
 * administrator maintains rather than written into the markup.
 *
 * Only values that are actually stored are emitted: an empty social handle or a
 * blank phone number leaves its property out entirely, so the graph never
 * asserts something the business has not supplied.
 */
class StructuredData
{
    public function __construct(private readonly SiteSettings $site) {}

    /**
     * One graph covering the whole site, referenced by every page.
     *
     * @return array<string, mixed>
     */
    public function siteGraph(string $locale): array
    {
        $home = route('home', ['locale' => $locale]);

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $home.'#website',
                    'url' => $home,
                    'name' => config('app.name'),
                    'inLanguage' => $locale,
                    'publisher' => ['@id' => url('/').'#organization'],
                ],
                $this->organization(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function organization(): array
    {
        $organization = array_filter([
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => config('app.name'),
            'legalName' => $this->site->companyName(),
            'url' => url('/'),
            'logo' => asset('assets/images/luxgo/global/logo/favicon-512x512.png'),
            'email' => $this->site->email(),
            /* Published locally as 0811…, dialled as +62811… — the link already
               holds the international form. */
            'telephone' => $this->dialable(),
        ], fn ($value) => $value !== null && $value !== '');

        $address = $this->address();
        if ($address !== null) {
            $organization['address'] = $address;
        }

        $sameAs = array_values(array_filter([
            $this->site->instagramUrl(),
            $this->site->tiktokUrl(),
        ]));

        if ($sameAs !== []) {
            $organization['sameAs'] = $sameAs;
        }

        return $organization;
    }

    private function dialable(): ?string
    {
        $link = $this->site->phoneLink();

        return $link === null ? null : substr($link, strlen('tel:'));
    }

    /**
     * The head office, only when there is an address to give. The lines are
     * passed through as stored; nothing is parsed out of them, so no postal
     * code or district is asserted that the business did not write.
     *
     * @return array<string, string>|null
     */
    private function address(): ?array
    {
        $lines = $this->site->headOfficeLines();

        if ($lines === []) {
            return null;
        }

        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => implode(', ', $lines),
            'addressLocality' => $this->site->headOfficeCity(),
            'addressCountry' => 'ID',
        ], fn ($value) => $value !== '');
    }
}
