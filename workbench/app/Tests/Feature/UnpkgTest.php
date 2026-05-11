<?php

namespace Workbench\App\Tests\Feature;

use Luminix\Admin\AdminServiceProvider;
use Luminix\Admin\Support\Unpkg;
use Workbench\App\Tests\TestCase;

class UnpkgTest extends TestCase
{
    public function test_url_contains_correct_version()
    {
        $url = Unpkg::url('package.json');

        $this->assertStringContainsString(AdminServiceProvider::CMS_VERSION, $url);
    }

    public function test_url_points_to_correct_package()
    {
        $url = Unpkg::url('package.json');

        $this->assertStringStartsWith('https://unpkg.com/@luminix/mui-cms@', $url);
    }

    public function test_url_appends_file_path()
    {
        $url = Unpkg::url('bundle/mui-cms.bundle.iife.js');

        $expected = 'https://unpkg.com/@luminix/mui-cms@'
            . AdminServiceProvider::CMS_VERSION
            . '/bundle/mui-cms.bundle.iife.js';

        $this->assertEquals($expected, $url);
    }

    public function test_url_works_for_css_bundle()
    {
        $url = Unpkg::url('bundle/style.css');

        $expected = 'https://unpkg.com/@luminix/mui-cms@'
            . AdminServiceProvider::CMS_VERSION
            . '/bundle/style.css';

        $this->assertEquals($expected, $url);
    }
}
