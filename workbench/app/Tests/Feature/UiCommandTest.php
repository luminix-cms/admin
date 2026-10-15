<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Workbench\App\Tests\BaseTestCase;

class UiCommandTest extends BaseTestCase
{
    private array $initialPackageJson = [
        'private' => true,
        'type' => 'module',
        'scripts' => [
            'build' => 'vite build',
            'dev' => 'vite',
        ],
        'dependencies' => [
            'my-dependency' => '^1.0.0',
            '@luminix/mui-cms' => '^0.1.9',
        ],
        'devDependencies' => [
            'autoprefixer' => '^10.4.20',
            'axios' => '^1.7.4',
        ],
    ];

    private array $expectedPackageJson = [
        'private' => true,
        'type' => 'module',
        'scripts' => [
            'build' => 'vite build',
            'dev' => 'vite',
        ],
        'dependencies' => [
            '@emotion/react' => '^11.13.0',
            '@emotion/styled' => '^11.13.0',
            '@fontsource/roboto' => '^5.0.12',
            '@luminix/core' => '^1.1.0',
            '@luminix/mui-cms' => '^2.0.0',
            '@luminix/react' => '^1.3.0',
            '@luminix/support' => '^1.2.1',
            '@mui/icons-material' => '^5.16.5',
            '@mui/material' => '^5.16.5',
            'i18next' => '^23.12.2',
            'my-dependency' => '^1.0.0',
            'react' => '^18.3.1',
            'react-dom' => '^18.3.1',
            'react-i18next' => '^15.0.1',
            'react-router-dom' => '^7.18.0',
        ],
        'devDependencies' => [
            'autoprefixer' => '^10.4.20',
            'axios' => '^1.7.4',
        ],
    ];

    /**
     * What the 1.x line of this package leaves in a host's package.json:
     * `@luminix/mui-cms` 1.x with react-router-dom 6 pinned to an exact version.
     */
    private array $packageJsonWrittenBy1x = [
        'private' => true,
        'type' => 'module',
        'scripts' => [
            'build' => 'vite build',
            'dev' => 'vite',
        ],
        'dependencies' => [
            '@emotion/react' => '^11.13.0',
            '@emotion/styled' => '^11.13.0',
            '@fontsource/roboto' => '^5.0.12',
            '@luminix/core' => '^1.0.0',
            '@luminix/mui-cms' => '^1.0.0',
            '@luminix/react' => '^1.0.0',
            '@luminix/support' => '^1.0.1',
            '@mui/icons-material' => '^5.16.5',
            '@mui/material' => '^5.16.5',
            'i18next' => '^23.12.2',
            'my-dependency' => '^1.0.0',
            'react' => '^18.3.1',
            'react-dom' => '^18.3.1',
            'react-i18next' => '^15.0.1',
            'react-router-dom' => '6.25.1',
        ],
        'devDependencies' => [
            'autoprefixer' => '^10.4.20',
            'axios' => '^1.7.4',
        ],
    ];

    public function test_artisan_command_is_registered()
    {
        $this->assertArrayHasKey('luminix:admin-ui', Artisan::all());
    }

    public function test_command_executes_successfully()
    {
        $packageJsonPath = base_path('package.json');

        file_put_contents($packageJsonPath, json_encode($this->initialPackageJson, JSON_PRETTY_PRINT));

        try {
            $this->artisan('luminix:admin-ui', ['--force' => true])
                ->assertExitCode(0);

            $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);
            $this->assertEquals($this->expectedPackageJson, $modifiedPackageJson);

            $this->assertFileExists(base_path('resources/js/luminix-admin.jsx'));
            $this->assertDirectoryExists(base_path('resources/views/vendor/admin'));
        } finally {
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }

    public function test_command_without_force_executes_successfully()
    {
        $packageJsonPath = base_path('package.json');

        file_put_contents($packageJsonPath, json_encode($this->initialPackageJson, JSON_PRETTY_PRINT));

        try {
            $this->artisan('luminix:admin-ui')
                ->expectsOutput('Following dependencies will be installed or updated:')
                ->expectsQuestion('Do you wish to continue?', true)
                ->expectsOutput('Admin UI published successfully.')
                ->expectsOutput('Then, run `npm install && npm run dev` (Node.js 20 or newer is required).')
                ->assertExitCode(0);

            $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);
            $this->assertEquals($this->expectedPackageJson, $modifiedPackageJson);

            $this->assertFileExists(base_path('resources/js/luminix-admin.jsx'));
            $this->assertDirectoryExists(base_path('resources/views/vendor/admin'));
        } finally {
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }

    public function test_command_aborts_package_json_update_when_user_declines()
    {
        $packageJsonPath = base_path('package.json');

        file_put_contents($packageJsonPath, json_encode($this->initialPackageJson, JSON_PRETTY_PRINT));

        try {
            $this->artisan('luminix:admin-ui')
                ->expectsQuestion('Do you wish to continue?', false)
                ->assertExitCode(0);

            // package.json must not have been modified
            $unchangedPackageJson = json_decode(file_get_contents($packageJsonPath), true);
            $this->assertEquals($this->initialPackageJson, $unchangedPackageJson);
        } finally {
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }

    public function test_command_executes_successfully_updates_versions()
    {
        $packageJsonPath = base_path('package.json');

        file_put_contents($packageJsonPath, json_encode($this->initialPackageJson, JSON_PRETTY_PRINT));

        try {
            $this->artisan('luminix:admin-ui', ['--force' => true])
                ->assertExitCode(0);

            $updatedPackageJson = json_decode(file_get_contents($packageJsonPath), true);

            $this->assertArrayHasKey('@luminix/mui-cms', $updatedPackageJson['dependencies']);
            $this->assertEquals('^' . \Luminix\Admin\AdminServiceProvider::CMS_VERSION, $updatedPackageJson['dependencies']['@luminix/mui-cms']);
            $this->assertArrayNotHasKey('jest', $updatedPackageJson['devDependencies'] ?? []);
        } finally {
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }

    public function test_command_moves_a_host_written_by_1x_to_the_2x_dependency_set()
    {
        $packageJsonPath = base_path('package.json');

        file_put_contents($packageJsonPath, json_encode($this->packageJsonWrittenBy1x, JSON_PRETTY_PRINT));

        try {
            $this->artisan('luminix:admin-ui', ['--force' => true])
                ->assertExitCode(0);

            $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);

            // the exact react-router-dom 6 pin gives way to the 7 range; what the host owns stays
            $this->assertEquals($this->expectedPackageJson, $modifiedPackageJson);
            $this->assertSame('^7.18.0', $modifiedPackageJson['dependencies']['react-router-dom']);
            $this->assertSame('^1.0.0', $modifiedPackageJson['dependencies']['my-dependency']);
        } finally {
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }
}
