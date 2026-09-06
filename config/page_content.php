<?php

/*
| The shape of every CMS-managed page: which sections it has, which text is
| translated, which values are shared between the locales, and which images each
| section can carry.
|
| This file is the contract between the editor, the validator and the public
| page. Nothing here describes design — a section's item count and layout are
| fixed by the front end, so the editor changes wording and imagery, never
| structure. Adding a page later means adding an entry here, not new controllers.
|
| Per section:
|   view      the Blade partial the public page renders
|   lang      the translation namespace the initial content came from, also used
|             as a safety net before the content has been seeded
|   fields    translated text, keyed by its dotted path inside the content JSON
|   media     image slots, shared between locales; `alt` names the translated
|             field that describes the image
|   settings  shared, untranslated values (currently CTA destinations)
*/

return [

    /* Destinations a CTA may point at. One is chosen from this list rather
       than typed, so a link can never carry javascript: or leave the site.
       A `route` entry keeps the visitor's locale automatically; `path` is for
       the site-wide placeholder link that has no page of its own yet. */
    'cta_targets' => [
        'membership' => ['label' => 'Membership', 'route' => 'membership'],
        'collection' => ['label' => 'Our Collection', 'route' => 'collection'],
        'experience' => ['label' => 'The Experience', 'route' => 'experience'],
        'how-it-works' => ['label' => 'How It Works', 'route' => 'how-it-works'],
        'about' => ['label' => 'About & Contact', 'route' => 'about'],
        'home' => ['label' => 'Home', 'route' => 'home'],
        'become_member' => ['label' => 'Become a member', 'path' => '/become-a-member'],
    ],

    'pages' => [

        'home' => [
            'label' => 'Home',
            'editable' => true,
            'view' => 'pages.home.index',

            'sections' => [

                'hero' => [
                    'label' => 'Hero',
                    'view' => 'pages.home.sections.hero',
                    'lang' => 'home.hero',
                    'fields' => [
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'title_4' => ['label' => 'Heading line 4', 'rules' => ['required', 'string', 'max:60']],
                        'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'cta' => ['label' => 'CTA label', 'rules' => ['required', 'string', 'max:60']],
                    ],
                    'media' => [
                        /* The hero image sits behind the copy and is decorative,
                           so it carries no alt text on the public page. */
                        'hero_image' => [
                            'label' => 'Background image',
                            'fallback' => 'assets/images/luxgo/home/hero/gambar_bg.png',
                        ],
                    ],
                    'settings' => [
                        'cta_route' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'membership'],
                    ],
                ],

                'access_not_ownership' => [
                    'label' => 'Access, Not Ownership',
                    'view' => 'pages.home.sections.access-not-ownership',
                    'lang' => 'home.access',
                    'fields' => [
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'items.price' => ['label' => 'Item 1', 'group' => 'Ownership considerations', 'rules' => ['required', 'string', 'max:60']],
                        'items.depreciation' => ['label' => 'Item 2', 'group' => 'Ownership considerations', 'rules' => ['required', 'string', 'max:60']],
                        'items.maintenance' => ['label' => 'Item 3', 'group' => 'Ownership considerations', 'rules' => ['required', 'string', 'max:60']],
                        'items.insurance' => ['label' => 'Item 4', 'group' => 'Ownership considerations', 'rules' => ['required', 'string', 'max:60']],
                        'items.operational' => ['label' => 'Item 5', 'group' => 'Ownership considerations', 'rules' => ['required', 'string', 'max:60']],
                    ],
                ],

                'business_family_life' => [
                    'label' => 'Business / Family / Life',
                    'view' => 'pages.home.sections.use-cases',
                    'lang' => 'home.use_cases',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        'business.label' => ['label' => 'Title', 'group' => 'Business', 'rules' => ['required', 'string', 'max:60']],
                        'business.line_1' => ['label' => 'Copy line 1', 'group' => 'Business', 'rules' => ['required', 'string', 'max:120']],
                        'business.line_2' => ['label' => 'Copy line 2', 'group' => 'Business', 'rules' => ['required', 'string', 'max:120']],
                        'business.alt' => ['label' => 'Image alt text', 'group' => 'Business', 'rules' => ['nullable', 'string', 'max:200']],

                        'family.label' => ['label' => 'Title', 'group' => 'Family', 'rules' => ['required', 'string', 'max:60']],
                        'family.line_1' => ['label' => 'Copy line 1', 'group' => 'Family', 'rules' => ['required', 'string', 'max:120']],
                        'family.line_2' => ['label' => 'Copy line 2', 'group' => 'Family', 'rules' => ['required', 'string', 'max:120']],
                        'family.alt' => ['label' => 'Image alt text', 'group' => 'Family', 'rules' => ['nullable', 'string', 'max:200']],

                        'life.label' => ['label' => 'Title', 'group' => 'Life', 'rules' => ['required', 'string', 'max:60']],
                        'life.line_1' => ['label' => 'Copy line 1', 'group' => 'Life', 'rules' => ['required', 'string', 'max:120']],
                        'life.line_2' => ['label' => 'Copy line 2', 'group' => 'Life', 'rules' => ['required', 'string', 'max:120']],
                        'life.alt' => ['label' => 'Image alt text', 'group' => 'Life', 'rules' => ['nullable', 'string', 'max:200']],
                    ],
                    /* Three cards, fixed by the layout — three slots, no more. */
                    'media' => [
                        'business_image' => ['label' => 'Business image', 'alt' => 'business.alt', 'fallback' => 'assets/images/luxgo/home/use-cases/business.webp'],
                        'family_image' => ['label' => 'Family image', 'alt' => 'family.alt', 'fallback' => 'assets/images/luxgo/home/use-cases/family.webp'],
                        'life_image' => ['label' => 'Life image', 'alt' => 'life.alt', 'fallback' => 'assets/images/luxgo/home/use-cases/life.webp'],
                    ],
                ],

                'premium_mobility' => [
                    'label' => 'Premium Mobility Preview',
                    'view' => 'pages.home.sections.premium-mobility',
                    'lang' => 'home.mobility',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'vehicle_alt' => ['label' => 'Vehicle image alt text', 'rules' => ['nullable', 'string', 'max:200']],

                        'features.design.line_1' => ['label' => 'Feature 1 — line 1', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.design.line_2' => ['label' => 'Feature 1 — line 2', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.comfort.line_1' => ['label' => 'Feature 2 — line 1', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.comfort.line_2' => ['label' => 'Feature 2 — line 2', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.ev.line_1' => ['label' => 'Feature 3 — line 1', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.ev.line_2' => ['label' => 'Feature 3 — line 2', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.executive.line_1' => ['label' => 'Feature 4 — line 1', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],
                        'features.executive.line_2' => ['label' => 'Feature 4 — line 2', 'group' => 'Features', 'rules' => ['required', 'string', 'max:40']],

                        'driver_title' => ['label' => 'Driver note — title', 'group' => 'Driver note', 'rules' => ['required', 'string', 'max:80']],
                        'driver_accent' => ['label' => 'Driver note — accent word', 'group' => 'Driver note', 'rules' => ['required', 'string', 'max:40']],
                        'driver_copy' => ['label' => 'Driver note — copy', 'group' => 'Driver note', 'rules' => ['required', 'string', 'max:200']],
                    ],
                    'media' => [
                        'vehicle_image' => [
                            'label' => 'Vehicle image',
                            'alt' => 'vehicle_alt',
                            'fallback' => 'assets/images/luxgo/collection/denza-d9/gambar_bg2.png',
                        ],
                    ],
                ],

                'how_it_works_preview' => [
                    'label' => 'How It Works Preview',
                    'view' => 'pages.home.sections.how-it-works',
                    'lang' => 'home.how',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title' => ['label' => 'Heading', 'rules' => ['required', 'string', 'max:80']],

                        /* Three steps, fixed by the layout: JOIN, BOOK, USE. */
                        'steps.join.title' => ['label' => 'Step 1 — title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.join.copy' => ['label' => 'Step 1 — copy', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:200']],
                        'steps.book.title' => ['label' => 'Step 2 — title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.book.copy' => ['label' => 'Step 2 — copy', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:200']],
                        'steps.use.title' => ['label' => 'Step 3 — title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.use.copy' => ['label' => 'Step 3 — copy', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:200']],

                        'cta_title_1' => ['label' => 'CTA heading line 1', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta_title_2' => ['label' => 'CTA heading line 2', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta_copy' => ['label' => 'CTA copy', 'group' => 'Closing CTA', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'cta_link' => ['label' => 'CTA label', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                    ],
                    'settings' => [
                        'cta_route' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'membership'],
                    ],
                ],

            ],
        ],

        /* Structure only until their own stage; the editor is not offered yet. */
        'membership' => [
            'label' => 'Membership',
            'editable' => true,
            'view' => 'pages.membership.index',

            /* Every figure this page shows lives in membership_settings, so the
               editor gets a screen of its own for them. */
            'business_settings' => true,

            'sections' => [

                'hero' => [
                    'label' => 'Hero',
                    'view' => 'pages.membership.sections.hero',
                    'lang' => 'membership.hero',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        'panel_label' => ['label' => 'Panel label', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'unit_years' => ['label' => 'Unit - years', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:40']],
                        'unit_per_year' => ['label' => 'Unit - per year', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:40']],
                        'unit_per_five_years' => ['label' => 'Unit - per membership period', 'group' => 'Key numbers', 'help' => 'Use {{membership_period}} for the number of years.', 'rules' => ['required', 'string', 'max:40']],
                        'label_period' => ['label' => 'Label - membership period', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'label_rights' => ['label' => 'Label - usage rights', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'label_total_rights' => ['label' => 'Label - total usage rights', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                    ],
                ],

                'membership_package' => [
                    'label' => 'Membership Package',
                    'view' => 'pages.membership.sections.membership-package',
                    'lang' => 'membership.package',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'lot_label' => ['label' => 'LOT label', 'rules' => ['required', 'string', 'max:60']],

                        'fee_label' => ['label' => 'Fee block label', 'group' => 'Fee block', 'rules' => ['required', 'string', 'max:60']],
                        'price_regular' => ['label' => 'Regular price caption', 'group' => 'Fee block', 'rules' => ['required', 'string', 'max:60']],
                        'price_promo' => ['label' => 'Promo price caption', 'group' => 'Fee block', 'rules' => ['required', 'string', 'max:60']],
                        'price_note' => ['label' => 'Promo label', 'group' => 'Fee block', 'help' => 'Use {{promo_member_limit}} for the promo quota.', 'rules' => ['required', 'string', 'max:120']],

                        'what_you_get' => ['label' => 'Metrics block label', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'unit_years' => ['label' => 'Unit - years', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:40']],
                        'unit_per_year' => ['label' => 'Unit - per year', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:40']],
                        'unit_per_five_years' => ['label' => 'Unit - per membership period', 'group' => 'Key numbers', 'help' => 'Use {{membership_period}} for the number of years.', 'rules' => ['required', 'string', 'max:40']],
                        'label_period' => ['label' => 'Label - membership period', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'label_rights' => ['label' => 'Label - usage rights', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],
                        'label_total_rights' => ['label' => 'Label - total usage rights', 'group' => 'Key numbers', 'rules' => ['required', 'string', 'max:60']],

                        'usage_label' => ['label' => 'Usage block label', 'group' => 'Usage fee', 'rules' => ['required', 'string', 'max:60']],
                        'usage_unit' => ['label' => 'Usage unit', 'group' => 'Usage fee', 'help' => 'Use {{usage_duration}} for the hours.', 'rules' => ['required', 'string', 'max:60']],
                        'usage_note' => ['label' => 'Usage note', 'group' => 'Usage fee', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'become_member'],
                    ],
                ],

                'more_lot' => [
                    'label' => 'More LOT. More Access',
                    'view' => 'pages.membership.sections.more-access',
                    'lang' => 'membership.access',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'help' => 'Use {{base_usage_rights}} and {{additional_lot_rights}} instead of typing the figures.', 'rules' => ['required', 'string', 'max:400']],

                        'rule_one_lot' => ['label' => 'Rule - one LOT', 'group' => 'Rule', 'rules' => ['required', 'string', 'max:60']],
                        'rule_additional' => ['label' => 'Rule - additional LOT', 'group' => 'Rule', 'rules' => ['required', 'string', 'max:60']],
                        'unit_per_year' => ['label' => 'Unit - per year', 'group' => 'Rule', 'rules' => ['required', 'string', 'max:40']],

                        'calculator_title' => ['label' => 'Calculator title', 'group' => 'Calculator', 'rules' => ['required', 'string', 'max:80']],
                        'calculator_copy' => ['label' => 'Calculator copy', 'group' => 'Calculator', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'decrease' => ['label' => 'Decrease button label', 'group' => 'Calculator', 'rules' => ['required', 'string', 'max:60']],
                        'increase' => ['label' => 'Increase button label', 'group' => 'Calculator', 'rules' => ['required', 'string', 'max:60']],
                        'result_annual' => ['label' => 'Result - per year', 'group' => 'Calculator', 'rules' => ['required', 'string', 'max:60']],
                        'result_total' => ['label' => 'Result - total', 'group' => 'Calculator', 'help' => 'Use {{membership_period}} for the number of years.', 'rules' => ['required', 'string', 'max:80']],
                        'calculator_note' => ['label' => 'Calculator note', 'group' => 'Calculator', 'type' => 'textarea', 'help' => 'Use {{additional_lot_rights}} instead of typing the figure.', 'rules' => ['required', 'string', 'max:300']],
                    ],
                ],

                'usage' => [
                    'label' => 'Understanding Your Usage',
                    'view' => 'pages.membership.sections.understanding-usage',
                    'lang' => 'membership.usage',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'unit' => ['label' => 'Unit', 'help' => 'Use {{usage_duration}} for the hours.', 'rules' => ['required', 'string', 'max:60']],

                        'with_rights' => ['label' => 'Label', 'group' => 'With usage rights', 'rules' => ['required', 'string', 'max:60']],
                        'caption' => ['label' => 'Fee caption', 'group' => 'With usage rights', 'rules' => ['required', 'string', 'max:60']],
                        'driver_included' => ['label' => 'Driver note', 'group' => 'With usage rights', 'rules' => ['required', 'string', 'max:120']],

                        'after_rights' => ['label' => 'Label', 'group' => 'After rights are used', 'rules' => ['required', 'string', 'max:60']],
                        'regular_usage' => ['label' => 'Row - regular usage', 'group' => 'After rights are used', 'rules' => ['required', 'string', 'max:60']],
                        'additional_usage' => ['label' => 'Row - additional usage', 'group' => 'After rights are used', 'rules' => ['required', 'string', 'max:60']],
                        'total' => ['label' => 'Row - total', 'group' => 'After rights are used', 'rules' => ['required', 'string', 'max:60']],
                        'availability' => ['label' => 'Availability note', 'group' => 'After rights are used', 'rules' => ['required', 'string', 'max:200']],
                    ],
                ],

                'faq_cta' => [
                    'label' => 'FAQ + Closing CTA',
                    /* One section, two partials - the FAQ list and the closing
                       CTA are switched on and off together, as they are on the
                       approved page. */
                    'view' => [
                        'pages.membership.sections.faq',
                        'pages.membership.sections.membership-cta',
                    ],
                    'lang' => ['' => 'membership.faq', 'cta' => 'membership.cta'],

                    /* The questions the page shipped with. They are seeded once
                       into faq_items, after which the list is the admin's. */
                    'faq' => [
                        'lang' => 'membership.faq',
                        'items' => [
                            ['question' => 'q1', 'answer' => 'a1'],
                            ['question' => 'q2', 'answer' => 'a2'],
                            ['question' => 'q3', 'answer' => 'a3'],
                            ['question' => 'q4', 'answer' => 'a4'],
                            ['question' => 'q5', 'answer' => 'a5'],
                            ['question' => 'q6', 'answer' => 'a6'],
                            ['question' => 'q7', 'answer' => 'a7', 'breakdown' => true],
                        ],
                    ],
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        'row_regular' => ['label' => 'Row - regular usage', 'group' => 'Usage breakdown labels', 'rules' => ['required', 'string', 'max:60']],
                        'row_additional' => ['label' => 'Row - additional usage', 'group' => 'Usage breakdown labels', 'rules' => ['required', 'string', 'max:60']],
                        'row_total' => ['label' => 'Row - total', 'group' => 'Usage breakdown labels', 'rules' => ['required', 'string', 'max:60']],
                        'row_total_value' => ['label' => 'Row - total value', 'group' => 'Usage breakdown labels', 'help' => 'Use {{additional_usage_total}} and {{usage_duration}} instead of typing the figures.', 'rules' => ['required', 'string', 'max:80']],

                        'cta.kicker' => ['label' => 'Kicker', 'group' => 'Closing CTA', 'help' => 'Use {{membership_period}} for the number of years.', 'rules' => ['required', 'string', 'max:80']],
                        'cta.title_1' => ['label' => 'Heading line 1', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta.title_2' => ['label' => 'Heading line 2', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta.copy' => ['label' => 'Description', 'group' => 'Closing CTA', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'become_member'],
                    ],
                ],

            ],
        ],
        'collection' => ['label' => 'Our Collection', 'editable' => false],
        'experience' => ['label' => 'The Experience', 'editable' => false],
        'how_it_works' => ['label' => 'How It Works', 'editable' => false],
        'about' => ['label' => 'About & Contact', 'editable' => false],

    ],

];
