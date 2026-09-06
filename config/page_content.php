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

    /* Internal routes a CTA may point at. A destination is chosen from this
       list rather than typed, so a link can never carry javascript: or leave
       the site, and route() keeps the visitor's locale. */
    'cta_routes' => [
        'membership' => 'Membership',
        'collection' => 'Our Collection',
        'experience' => 'The Experience',
        'how-it-works' => 'How It Works',
        'about' => 'About & Contact',
        'home' => 'Home',
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
                        'cta_route' => ['label' => 'CTA destination', 'type' => 'route', 'default' => 'membership'],
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
                        'cta_route' => ['label' => 'CTA destination', 'type' => 'route', 'default' => 'membership'],
                    ],
                ],

            ],
        ],

        /* Structure only until their own stage; the editor is not offered yet. */
        'membership' => ['label' => 'Membership', 'editable' => false],
        'collection' => ['label' => 'Our Collection', 'editable' => false],
        'experience' => ['label' => 'The Experience', 'editable' => false],
        'how_it_works' => ['label' => 'How It Works', 'editable' => false],
        'about' => ['label' => 'About & Contact', 'editable' => false],

    ],

];
