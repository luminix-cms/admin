<?php

namespace Workbench\App\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Workbench\Database\Seeders\DatabaseSeeder;

use function Orchestra\Testbench\artisan;

class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            \Luminix\Admin\AdminServiceProvider::class,
            // \Workbench\App\Providers\WorkbenchServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('luminix.admin.middleware', ['web', 'auth']);

        $app['config']->set('luminix.backend.models.include', [
            'Workbench\App\Models\User',
        ]);

        $app['config']->set('auth', require __DIR__ . '/../../config/auth.ci.php');

        $app['router']->get('/login', function () {
            return 'Login Page';
        })->name('login');
    }

    protected function setUp(): void
    {
        parent::setUp();
        // $this->seed(DatabaseSeeder::class);
        // Definir a chave de aplicação
        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);

        // Carregar migrações ou outras configurações
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    // /**
    //  * Define database migrations.
    //  *
    //  * @return void
    //  */
    // protected function defineDatabaseMigrations()
    // {
    //     artisan($this, 'migrate', ['--database' => 'testing']);

    //     $this->beforeApplicationDestroyed(
    //         fn() => artisan($this, 'migrate:rollback', ['--database' => 'testing'])
    //     );
    // }
}
