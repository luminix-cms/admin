<?php

namespace Workbench\App\Tests\Feature;

use Luminix\Frontend\Facades\Boot;
use Workbench\App\Tests\TestCase;

class BrandTest extends TestCase
{
    public function test_the_panel_carries_the_host_identity()
    {
        config([
            'luminix.admin.brand.name' => 'Acme',
            'luminix.admin.brand.logo' => '/brand/acme.svg',
            'luminix.admin.brand.logo_dark' => '/brand/acme-dark.svg',
        ]);

        $brand = Boot::get()['luminix']['admin']['brand'];

        $this->assertSame('Acme', $brand['name']);
        $this->assertSame('/brand/acme.svg', $brand['logo']);
        $this->assertSame('/brand/acme-dark.svg', $brand['logo_dark']);
    }

    public function test_the_name_falls_back_to_the_application_name()
    {
        // Most applications never set a panel-specific name; reading `app.name`
        // means they still get their own, instead of ours.
        config(['luminix.admin.brand.name' => null, 'app.name' => 'Acme']);

        $this->assertSame('Acme', Boot::get()['luminix']['admin']['brand']['name']);
    }

    public function test_no_logo_configured_reads_as_nothing()
    {
        // The frontend falls back to the Luminix mark when there is no URL, so
        // nothing changes for who does not configure a brand.
        config(['luminix.admin.brand.logo' => null, 'luminix.admin.brand.logo_dark' => null]);

        $brand = Boot::get()['luminix']['admin']['brand'];

        $this->assertNull($brand['logo']);
        $this->assertNull($brand['logo_dark']);
    }
}
