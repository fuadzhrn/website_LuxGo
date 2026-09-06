<?php

namespace App\Support;

/**
 * A page as the public view renders it: its active sections, already ordered,
 * each one carrying the Blade partial it belongs to.
 */
class PageContent
{
    /**
     * @param  array<string, SectionContent>  $sections  keyed by section_key, in display order
     * @param  array<string, string>  $views  section_key => Blade view
     */
    public function __construct(
        public readonly string $key,
        private readonly array $sections,
        private readonly array $views,
    ) {}

    /**
     * @return array<string, SectionContent>
     */
    public function sections(): array
    {
        return $this->sections;
    }

    public function has(string $sectionKey): bool
    {
        return isset($this->sections[$sectionKey]);
    }

    public function section(string $sectionKey): ?SectionContent
    {
        return $this->sections[$sectionKey] ?? null;
    }

    public function view(string $sectionKey): ?string
    {
        return $this->views[$sectionKey] ?? null;
    }
}
