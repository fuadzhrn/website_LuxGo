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
     * @param  array<string, array<int, string>>  $views  section_key => Blade views
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

    /**
     * The partials a section renders. Usually one; a section that the approved
     * page splits across two partials lists both.
     *
     * @return array<int, string>
     */
    public function views(string $sectionKey): array
    {
        return $this->views[$sectionKey] ?? [];
    }
}
