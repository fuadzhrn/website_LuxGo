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
       A `route` entry keeps the visitor's locale automatically, and a
       `fragment` scrolls to the part of that page the link is about. */
    'cta_targets' => [
        'membership' => ['label' => 'Membership', 'route' => 'membership'],
        'collection' => ['label' => 'Our Collection', 'route' => 'collection'],
        'experience' => ['label' => 'The Experience', 'route' => 'experience'],
        'how-it-works' => ['label' => 'How It Works', 'route' => 'how-it-works'],
        'about' => ['label' => 'About & Contact', 'route' => 'about'],
        'home' => ['label' => 'Home', 'route' => 'home'],
        /* The membership application form lives on the About & Contact page;
           this is the destination every "become a member" link uses. */
        'become_member' => ['label' => 'Become a member (application form)', 'route' => 'about', 'fragment' => 'membership-application'],
    ],

    /* What a vehicle record holds. The name and slug are shared; everything
       here is translated, and the figures a vehicle does not publish are simply
       absent — the site never invents a specification. */
    'vehicle_fields' => [
        'tagline' => ['label' => 'Tagline', 'type' => 'textarea', 'lang' => 'copy', 'rules' => ['required', 'string', 'max:400']],
        'image_alt' => ['label' => 'Main image alt text', 'lang' => 'image_alt', 'rules' => ['nullable', 'string', 'max:200']],
        'features.design' => ['label' => 'Feature 1', 'group' => 'Features', 'lang' => 'features.design', 'rules' => ['required', 'string', 'max:60']],
        'features.comfort' => ['label' => 'Feature 2', 'group' => 'Features', 'lang' => 'features.comfort', 'rules' => ['required', 'string', 'max:60']],
        'features.ev' => ['label' => 'Feature 3', 'group' => 'Features', 'lang' => 'features.ev', 'rules' => ['required', 'string', 'max:60']],
        'features.executive' => ['label' => 'Feature 4', 'group' => 'Features', 'lang' => 'features.executive', 'rules' => ['required', 'string', 'max:60']],
    ],

    'pages' => [

        'home' => [
            'label' => 'Home',
            'route' => 'home',
            'meta' => 'home.meta',
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

        'membership' => [
            'label' => 'Membership',
            'route' => 'membership',
            'meta' => 'membership.meta',
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
        'collection' => [
            'label' => 'Our Collection',
            'route' => 'collection',
            'meta' => 'collection.meta',
            'editable' => true,
            'view' => 'pages.collection.index',

            /* The vehicles themselves are managed in their own module; this
               page holds only the copy around them. */
            'vehicles' => true,

            'sections' => [

                'hero' => [
                    'label' => 'Collection Hero',
                    'view' => 'pages.collection.sections.hero',
                    'lang' => 'collection.hero',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'link' => ['label' => 'Link label', 'rules' => ['required', 'string', 'max:60']],
                        'image_alt' => ['label' => 'Hero image alt text', 'rules' => ['nullable', 'string', 'max:200']],
                    ],
                    'media' => [
                        'hero_image' => [
                            'label' => 'Hero image',
                            'alt' => 'image_alt',
                            'fallback' => 'assets/images/luxgo/collection/hero/collection-hero.webp',
                        ],
                    ],
                ],

                'featured_vehicle' => [
                    'label' => 'Vehicle Showcase',
                    'view' => 'pages.collection.sections.featured-vehicle',
                    'lang' => 'collection.featured',
                    /* The vehicles this section shows come from the vehicle
                       module, so its own copy is only the wrapper around them. */
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'link' => ['label' => 'Link label', 'rules' => ['required', 'string', 'max:60']],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'Link destination', 'type' => 'cta', 'default' => 'experience'],
                    ],
                ],

                'inside_experience' => [
                    'label' => 'Inside the Experience',
                    'view' => 'pages.collection.sections.inside-experience',
                    'lang' => 'collection.inside',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        'main_alt' => ['label' => 'Main image alt text', 'group' => 'Gallery alt text', 'rules' => ['nullable', 'string', 'max:200']],
                        'detail_1_alt' => ['label' => 'Detail 1 alt text', 'group' => 'Gallery alt text', 'rules' => ['nullable', 'string', 'max:200']],
                        'detail_2_alt' => ['label' => 'Detail 2 alt text', 'group' => 'Gallery alt text', 'rules' => ['nullable', 'string', 'max:200']],
                    ],
                    'settings' => [
                        /* The interior shown here is one vehicle's gallery: the
                           images live with the vehicle, never copied to here. */
                        'vehicle_id' => ['label' => 'Vehicle', 'type' => 'vehicle', 'help' => 'The gallery shown in this section comes from this vehicle.'],
                    ],
                ],

                'collection_cta' => [
                    'label' => 'Collection CTA',
                    'view' => 'pages.collection.sections.collection-cta',
                    'lang' => 'collection.cta',
                    'fields' => [
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'title_3' => ['label' => 'Heading line 3', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'link' => ['label' => 'Button label', 'rules' => ['required', 'string', 'max:60']],
                    ],
                    'media' => [
                        'cta_image' => [
                            'label' => 'Background image',
                            'fallback' => 'assets/images/luxgo/collection/cta/collection-cta-detail.webp',
                        ],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'membership'],
                    ],
                ],

            ],
        ],
        'experience' => [
            'label' => 'The Experience',
            'route' => 'experience',
            'meta' => 'experience.meta',
            'editable' => true,
            'view' => 'pages.experience.index',

            'sections' => [

                'hero' => [
                    'label' => 'Experience Hero',
                    'view' => 'pages.experience.sections.hero',
                    'lang' => 'experience.hero',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'link' => ['label' => 'Link label', 'rules' => ['required', 'string', 'max:60']],
                        'image_alt' => ['label' => 'Hero image alt text', 'rules' => ['nullable', 'string', 'max:200']],
                    ],
                    'media' => [
                        'hero_image' => [
                            'label' => 'Hero image',
                            'alt' => 'image_alt',
                            'fallback' => 'assets/images/luxgo/experience/hero/experience-hero.webp',
                        ],
                    ],
                ],

                'not_just_driver' => [
                    'label' => 'Not Just a Driver',
                    'view' => 'pages.experience.sections.not-just-driver',
                    'lang' => 'experience.driver',
                    'fields' => [
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'image_alt' => ['label' => 'Image alt text', 'rules' => ['nullable', 'string', 'max:200']],

                        /* Seven attributes, fixed by the layout. */
                        'attributes.appearance' => ['label' => 'Attribute 01', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.punctual' => ['label' => 'Attribute 02', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.polite' => ['label' => 'Attribute 03', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.defensive' => ['label' => 'Attribute 04', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.customer' => ['label' => 'Attribute 05', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.hospitality' => ['label' => 'Attribute 06', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                        'attributes.privacy' => ['label' => 'Attribute 07', 'group' => 'Service attributes', 'rules' => ['required', 'string', 'max:60']],
                    ],
                    'media' => [
                        'driver_image' => [
                            'label' => 'Service image',
                            'alt' => 'image_alt',
                            'fallback' => 'assets/images/luxgo/experience/driver/driver-service.webp',
                        ],
                    ],
                ],

                'service_standard' => [
                    'label' => 'Service Standard',
                    'view' => 'pages.experience.sections.service-standard',
                    'lang' => 'experience.standard',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],

                        /* Three pillars, fixed by the layout. */
                        'pillars.professional.title' => ['label' => 'Pillar 01 - title', 'group' => 'Pillars', 'rules' => ['required', 'string', 'max:60']],
                        'pillars.professional.copy' => ['label' => 'Pillar 01 - copy', 'group' => 'Pillars', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'pillars.hospitality.title' => ['label' => 'Pillar 02 - title', 'group' => 'Pillars', 'rules' => ['required', 'string', 'max:60']],
                        'pillars.hospitality.copy' => ['label' => 'Pillar 02 - copy', 'group' => 'Pillars', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'pillars.privacy.title' => ['label' => 'Pillar 03 - title', 'group' => 'Pillars', 'rules' => ['required', 'string', 'max:60']],
                        'pillars.privacy.copy' => ['label' => 'Pillar 03 - copy', 'group' => 'Pillars', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],

                        'cta_title_1' => ['label' => 'CTA heading line 1', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta_title_2' => ['label' => 'CTA heading line 2', 'group' => 'Closing CTA', 'rules' => ['required', 'string', 'max:60']],
                        'cta_copy' => ['label' => 'CTA copy', 'group' => 'Closing CTA', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'membership'],
                    ],
                ],

            ],
        ],
        'how_it_works' => [
            'label' => 'How It Works',
            'route' => 'how-it-works',
            'meta' => 'how-it-works.meta',
            'editable' => true,
            'view' => 'pages.how-it-works.index',

            'sections' => [

                'hero' => [
                    'label' => 'How It Works Hero',
                    'view' => 'pages.how-it-works.sections.hero',
                    'lang' => 'how-it-works.hero',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                        'image_alt' => ['label' => 'Hero image alt text', 'rules' => ['nullable', 'string', 'max:200']],
                    ],
                    'media' => [
                        'hero_image' => [
                            'label' => 'Hero image',
                            'alt' => 'image_alt',
                            'fallback' => 'assets/images/luxgo/how-it-works/hero/how-it-works-hero.webp',
                        ],
                    ],
                ],

                'process' => [
                    'label' => 'Join / Book / Use',
                    'view' => 'pages.how-it-works.sections.process',
                    'lang' => 'how-it-works.process',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title' => ['label' => 'Heading', 'rules' => ['required', 'string', 'max:80']],
                        'intro' => ['label' => 'Intro', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        /* Three steps, fixed by the layout: JOIN, BOOK, USE. */
                        'steps.join.title' => ['label' => 'Step 01 - title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.join.copy' => ['label' => 'Step 01 - copy', 'group' => 'Steps', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'steps.book.title' => ['label' => 'Step 02 - title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.book.copy' => ['label' => 'Step 02 - copy', 'group' => 'Steps', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                        'steps.use.title' => ['label' => 'Step 03 - title', 'group' => 'Steps', 'rules' => ['required', 'string', 'max:40']],
                        'steps.use.copy' => ['label' => 'Step 03 - copy', 'group' => 'Steps', 'type' => 'textarea', 'help' => 'Use {{usage_duration}} for the number of hours.', 'rules' => ['required', 'string', 'max:300']],
                    ],
                ],

                'service_area' => [
                    'label' => 'Serving Jabodetabek',
                    'view' => 'pages.how-it-works.sections.service-area',
                    'lang' => 'how-it-works.area',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        /* The service area itself is fixed: the wording can be
                           edited, the regions and locations cannot be added to. */
                        'areas.jakarta.name' => ['label' => 'Region name', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],
                        'areas.jakarta.locations.central' => ['label' => 'Location 1', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],
                        'areas.jakarta.locations.north' => ['label' => 'Location 2', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],
                        'areas.jakarta.locations.south' => ['label' => 'Location 3', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],
                        'areas.jakarta.locations.west' => ['label' => 'Location 4', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],
                        'areas.jakarta.locations.east' => ['label' => 'Location 5', 'group' => 'Jakarta', 'rules' => ['required', 'string', 'max:60']],

                        'areas.tangerang.name' => ['label' => 'Region name', 'group' => 'Tangerang', 'rules' => ['required', 'string', 'max:60']],
                        'areas.tangerang.locations.kota' => ['label' => 'Location 1', 'group' => 'Tangerang', 'rules' => ['required', 'string', 'max:60']],
                        'areas.tangerang.locations.selatan' => ['label' => 'Location 2', 'group' => 'Tangerang', 'rules' => ['required', 'string', 'max:60']],
                        'areas.tangerang.locations.kabupaten' => ['label' => 'Location 3', 'group' => 'Tangerang', 'rules' => ['required', 'string', 'max:60']],

                        'areas.bekasi.name' => ['label' => 'Region name', 'group' => 'Bekasi', 'rules' => ['required', 'string', 'max:60']],
                        'areas.bekasi.locations.kota' => ['label' => 'Location 1', 'group' => 'Bekasi', 'rules' => ['required', 'string', 'max:60']],
                        'areas.bekasi.locations.kabupaten' => ['label' => 'Location 2', 'group' => 'Bekasi', 'rules' => ['required', 'string', 'max:60']],

                        'areas.bogor.name' => ['label' => 'Region name', 'group' => 'Bogor', 'rules' => ['required', 'string', 'max:60']],
                        'areas.bogor.locations.kota' => ['label' => 'Location 1', 'group' => 'Bogor', 'rules' => ['required', 'string', 'max:60']],
                        'areas.bogor.locations.kabupaten' => ['label' => 'Location 2', 'group' => 'Bogor', 'rules' => ['required', 'string', 'max:60']],

                        'areas.depok.name' => ['label' => 'Region name', 'group' => 'Depok', 'rules' => ['required', 'string', 'max:60']],
                        'areas.depok.locations.kota' => ['label' => 'Location 1', 'group' => 'Depok', 'rules' => ['required', 'string', 'max:60']],
                    ],
                ],

                'closing_cta' => [
                    'label' => 'Closing CTA',
                    'view' => 'pages.how-it-works.sections.closing-cta',
                    'lang' => 'how-it-works.closing',
                    'fields' => [
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],
                    ],
                    'settings' => [
                        'cta_target' => ['label' => 'CTA destination', 'type' => 'cta', 'default' => 'membership'],
                    ],
                ],

            ],
        ],
        'about' => [
            'label' => 'About & Contact',
            'route' => 'about',
            'meta' => 'about.meta',
            'editable' => true,
            'view' => 'pages.about-contact.index',

            'sections' => [

                'about' => [
                    'label' => 'About LUX&GO',
                    'view' => 'pages.about-contact.sections.about',
                    'lang' => 'about.intro',
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Brand story', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:600']],

                        'serve_title' => ['label' => 'Heading', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                        /* Five audiences, fixed by the layout. */
                        'audiences.business' => ['label' => 'Audience 01', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                        'audiences.executives' => ['label' => 'Audience 02', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                        'audiences.families' => ['label' => 'Audience 03', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                        'audiences.professionals' => ['label' => 'Audience 04', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                        'audiences.corporate' => ['label' => 'Audience 05', 'group' => 'Who we serve', 'rules' => ['required', 'string', 'max:60']],
                    ],
                ],

                'membership_application' => [
                    'label' => 'Membership Application',
                    'view' => 'pages.about-contact.sections.membership-application',
                    'lang' => 'about.apply',
                    /* Wording only: the form still behaves exactly as approved,
                       and no submission is processed at this stage. */
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'copy' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:400']],

                        'field_name' => ['label' => 'Full name', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],
                        'field_phone' => ['label' => 'Phone / WhatsApp', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],
                        'field_email' => ['label' => 'Email', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],
                        'field_lots' => ['label' => 'Number of LOTs', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],
                        'field_message' => ['label' => 'Message / notes', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],
                        'optional' => ['label' => 'Optional marker', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:40']],
                        'submit' => ['label' => 'Submit button', 'group' => 'Field labels', 'rules' => ['required', 'string', 'max:80']],

                        'error_name' => ['label' => 'Missing name', 'group' => 'Validation messages', 'rules' => ['required', 'string', 'max:200']],
                        'error_phone' => ['label' => 'Missing phone', 'group' => 'Validation messages', 'rules' => ['required', 'string', 'max:200']],
                        'error_email_required' => ['label' => 'Missing email', 'group' => 'Validation messages', 'rules' => ['required', 'string', 'max:200']],
                        'error_email_invalid' => ['label' => 'Invalid email', 'group' => 'Validation messages', 'rules' => ['required', 'string', 'max:200']],
                        'error_lots' => ['label' => 'Invalid LOT count', 'group' => 'Validation messages', 'rules' => ['required', 'string', 'max:200']],
                        'status_unavailable' => ['label' => 'Submission status', 'group' => 'Validation messages', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:300']],
                    ],
                ],

                'contact' => [
                    'label' => 'Contact & Head Office',
                    'view' => 'pages.about-contact.sections.contact-head-office',
                    'lang' => 'about.contact',
                    /* Labels only. The company name, address, phone, email and
                       handles come from site_settings, so they are written once
                       for the whole site. */
                    'fields' => [
                        'eyebrow' => ['label' => 'Eyebrow', 'rules' => ['required', 'string', 'max:60']],
                        'title_1' => ['label' => 'Heading line 1', 'rules' => ['required', 'string', 'max:60']],
                        'title_2' => ['label' => 'Heading line 2', 'rules' => ['required', 'string', 'max:60']],
                        'channels_title' => ['label' => 'Channels heading', 'rules' => ['required', 'string', 'max:60']],

                        'label_whatsapp' => ['label' => 'WhatsApp label', 'group' => 'Channel labels', 'rules' => ['required', 'string', 'max:40']],
                        'label_email' => ['label' => 'Email label', 'group' => 'Channel labels', 'rules' => ['required', 'string', 'max:40']],
                        'label_instagram' => ['label' => 'Instagram label', 'group' => 'Channel labels', 'rules' => ['required', 'string', 'max:40']],
                        'label_tiktok' => ['label' => 'TikTok label', 'group' => 'Channel labels', 'rules' => ['required', 'string', 'max:40']],
                    ],
                ],

            ],
        ],

    ],

];
