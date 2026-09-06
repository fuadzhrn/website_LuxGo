<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root path carries no locale, so it hands the visitor to the default one.
     */
    public function test_the_root_path_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect(route('home', ['locale' => config('locales.default')]));
    }

    public function test_the_home_page_responds(): void
    {
        $this->get(route('home', ['locale' => config('locales.default')]))->assertOk();
    }
}
