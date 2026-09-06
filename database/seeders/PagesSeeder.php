<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * The six public pages and the sections they are built from. Only the
     * structure is seeded — the copy still lives in the lang files until the
     * content migration stage.
     *
     * @var array<int, array{key: string, slug: string|null, sections: array<int, string>}>
     */
    private const PAGES = [
        [
            'key' => 'home',
            'slug' => null,
            'sections' => ['hero', 'access_not_ownership', 'business_family_life', 'premium_mobility', 'how_it_works_preview'],
        ],
        [
            'key' => 'membership',
            'slug' => 'membership',
            'sections' => ['hero', 'membership_package', 'more_lot', 'usage', 'faq_cta'],
        ],
        [
            'key' => 'collection',
            'slug' => 'our-collection',
            'sections' => ['hero', 'featured_vehicle', 'inside_experience', 'collection_cta'],
        ],
        [
            'key' => 'experience',
            'slug' => 'experience',
            'sections' => ['hero', 'not_just_driver', 'service_standard'],
        ],
        [
            'key' => 'how_it_works',
            'slug' => 'how-it-works',
            'sections' => ['hero', 'process', 'service_area', 'closing_cta'],
        ],
        [
            'key' => 'about',
            'slug' => 'about',
            'sections' => ['about', 'membership_application', 'contact'],
        ],
    ];

    public function run(): void
    {
        foreach (self::PAGES as $order => $definition) {
            $page = Page::updateOrCreate(
                ['key' => $definition['key']],
                [
                    'slug' => $definition['slug'],
                    'is_active' => true,
                    'sort_order' => $order,
                ]
            );

            foreach ($definition['sections'] as $sectionOrder => $sectionKey) {
                $page->sections()->updateOrCreate(
                    ['section_key' => $sectionKey],
                    [
                        'is_active' => true,
                        'sort_order' => $sectionOrder,
                    ]
                );
            }

            /* An empty SEO record per page; the wording is added per locale
               once the admin area can edit it. */
            SeoSetting::updateOrCreate(
                ['page_id' => $page->id],
                ['is_indexable' => true]
            );
        }
    }
}
