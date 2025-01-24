<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Workbench\App\Tests\BaseTestCase;
use Workbench\App\Tests\TestCase;

class UiCommandTest extends BaseTestCase
{
    public function test_artisan_command_is_registered()
    {
        $this->assertArrayHasKey('luminix:admin-ui', Artisan::all());
    }

    public function test_command_executes_successfully()
    {
        // Cria o arquivo package.json com o conteudo do esqueleto do laravel
        // /vendor/orchestra/testbench-core/laravel/package.json

        $packageJsonPath = base_path('vendor/orchestra/testbench-core/laravel/package.json');

        // Conteúdo inicial do package.json (esqueleto do Laravel)
        $initialPackageJson = [
            'name' => 'laravel',
            'version' => '1.0.0',
            'dependencies' => [],
            'peerDependencies' => []
        ];

        // Cria o arquivo package.json com o conteúdo inicial
        if (!is_dir(dirname($packageJsonPath))) {
            mkdir(dirname($packageJsonPath), 0777, true);
        }
        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        // Baixa as peerDependencies do pacote remoto
        $remotePackage = file_get_contents('https://unpkg.com/@luminix/mui-cms@0.1.9/package.json');
        $remotePackageJson = json_decode($remotePackage, true);

        if (isset($remotePackageJson['peerDependencies'])) {
            $initialPackageJson['peerDependencies'] = array_merge(
                (array)$initialPackageJson['peerDependencies'],
                $remotePackageJson['peerDependencies']
            );
        }

        // Atualiza o arquivo package.json com as peerDependencies
        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        $this->artisan('luminix:admin-ui', ['--force' => true])
            ->assertExitCode(0);

        // Verificações:    
        // 1. Verifica se o package.json foi alterado corretamente
        $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);
        $this->assertArrayHasKey('peerDependencies', $modifiedPackageJson, 'O arquivo package.json não contém peerDependencies.');
        $this->assertNotEmpty($modifiedPackageJson['peerDependencies'], 'O campo peerDependencies do package.json está vazio.');

        // 2. Verifica se os arquivos e diretórios foram publicados
        $jsPath = base_path('vendor/orchestra/testbench-core/laravel/resources/js');
        $viewsPath = base_path('vendor/orchestra/testbench-core/laravel/resources/views/vendor/admin');
        $this->assertDirectoryExists($jsPath, 'O diretório JS não foi publicado.');
        $this->assertDirectoryExists($viewsPath, 'O diretório de views não foi publicado.');

        $this->assertFileExists($packageJsonPath, 'O arquivo package.json não existe.');

        // Apaga os arquivos gerados
        $this->deleteFile($packageJsonPath);
        $this->deleteDirectory($jsPath);
        $this->deleteDirectory($viewsPath);
    }

    public function test_command_without_force_executes_successfully()
    {
        // Cria o arquivo package.json com o conteudo do esqueleto do laravel
        // /vendor/orchestra/testbench-core/laravel/package.json

        $packageJsonPath = base_path('vendor/orchestra/testbench-core/laravel/package.json');

        // Conteúdo inicial do package.json (esqueleto do Laravel)
        $initialPackageJson = [
            'name' => 'laravel',
            'version' => '1.0.0',
            'dependencies' => [],
            'peerDependencies' => []
        ];

        // Cria o arquivo package.json com o conteúdo inicial
        if (!is_dir(dirname($packageJsonPath))) {
            mkdir(dirname($packageJsonPath), 0777, true);
        }
        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        // Baixa as peerDependencies do pacote remoto
        $remotePackage = file_get_contents('https://unpkg.com/@luminix/mui-cms@0.1.9/package.json');
        $remotePackageJson = json_decode($remotePackage, true);

        if (isset($remotePackageJson['peerDependencies'])) {
            $initialPackageJson['peerDependencies'] = array_merge(
                (array)$initialPackageJson['peerDependencies'],
                $remotePackageJson['peerDependencies']
            );
        }

        // Atualiza o arquivo package.json com as peerDependencies
        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        try {
            // Simula a entrada do comando para confirmar a instalação
            $this->artisan('luminix:admin-ui')
                ->expectsOutput('Following dependencies will be installed or updated:') // Verifica a mensagem inicial
                ->doesntExpectOutput('+------------------+---------+') // Adaptação para verificar saída real
                ->expectsQuestion('Do you wish to continue?', true) // Simula o "Sim" do usuário
                ->expectsOutput('Admin UI published successfully.') // Verifica mensagem final
                ->assertExitCode(0);

            // Verificações:
            // 1. Verifica se o package.json foi atualizado corretamente
            $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);
            $this->assertArrayHasKey('dependencies', $modifiedPackageJson);
            $this->assertArrayHasKey('@luminix/mui-cms', $modifiedPackageJson['dependencies']);
            $this->assertEquals(
                '^' . \Luminix\Admin\AdminServiceProvider::CMS_VERSION,
                $modifiedPackageJson['dependencies']['@luminix/mui-cms']
            );

            // 2. Verifica se os arquivos e diretórios foram publicados
            $jsPath = base_path('resources/js/luminix-admin.jsx');
            $viewsPath = base_path('resources/views/vendor/admin');
            $this->assertFileExists($jsPath, 'O arquivo luminix-admin.jsx não foi publicado.');
            $this->assertDirectoryExists($viewsPath, 'O diretório de views não foi publicado.');
        } finally {
            // Apaga os arquivos gerados
            $this->deleteFile($packageJsonPath);
            $this->deleteDirectory(base_path('resources/js'));
            $this->deleteDirectory(base_path('resources/views/vendor/admin'));
        }
    }
}
