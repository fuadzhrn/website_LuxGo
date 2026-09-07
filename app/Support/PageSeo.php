<?php

namespace App\Support;

/**
 * The search and sharing information for one page in one locale, already
 * resolved: what the head needs, with nothing left to decide in the view.
 *
 * The share fields fall back to the search fields, so an editor writes the
 * wording once unless they deliberately want a different one.
 */
class PageSeo
{
    public function __construct(
        private readonly string $title,
        private readonly string $description,
        private readonly ?string $ogTitle,
        private readonly ?string $ogDescription,
        private readonly ?string $ogImageUrl,
        private readonly bool $indexable,
        private readonly string $canonical,
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function ogTitle(): string
    {
        return $this->ogTitle ?: $this->title;
    }

    public function ogDescription(): string
    {
        return $this->ogDescription ?: $this->description;
    }

    public function ogImageUrl(): ?string
    {
        return $this->ogImageUrl;
    }

    public function isIndexable(): bool
    {
        return $this->indexable;
    }

    public function canonical(): string
    {
        return $this->canonical;
    }

    /**
     * What the robots meta tag should say. A page switched off is kept out of
     * the index and its links are not followed.
     */
    public function robots(): string
    {
        return $this->indexable ? 'index, follow' : 'noindex, nofollow';
    }
}
