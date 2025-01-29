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
        $packageJsonPath = base_path('package.json');

        // Conteúdo inicial do package.json (esqueleto do Laravel)
        $initialPackageJson = [
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
            // 'peerDependencies' => []
        ];

        $expectedPackageJson = [
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
                '@luminix/core' => '^0.3.1',
                '@luminix/mui-cms' => '^0.1.9',
                '@luminix/react' => '^0.3.1',
                '@luminix/support' => '^0.4.3',
                '@mui/icons-material' => '^5.16.5',
                '@mui/material' => '^5.16.5',
                'i18next' => '^23.12.2',
                'my-dependency' => '^1.0.0',
                'react' => '^18.3.1',
                'react-dom' => '^18.3.1',
                'react-i18next' => '^15.0.1',
                'react-router-dom' => '6.25.1'
            ],
            'devDependencies' => [
                'autoprefixer' => '^10.4.20',
                'axios' => '^1.7.4',
            ],
            // 'peerDependencies' => [
            //     '@emotion/react' => '^11.13.0',
            //     '@emotion/styled' => '^11.13.0',
            //     '@fontsource/roboto' => '^5.0.12',
            //     '@luminix/core' => '^0.3.1',
            //     '@luminix/react' => '^0.3.1',
            //     '@luminix/support' => '^0.4.3',
            //     '@mui/icons-material' => '^5.16.5',
            //     '@mui/material' => '^5.16.5',
            //     'i18next' => '^23.12.2',
            //     'react' => '^18.3.1',
            //     'react-dom' => '^18.3.1',
            //     'react-i18next' => '^15.0.1',
            //     'react-router-dom' => '6.25.1'
            // ],
        ];

        // Cria o arquivo package.json com o conteúdo inicial
        // if (!is_dir(dirname($packageJsonPath))) {
        //     mkdir(dirname($packageJsonPath), 0777, true);
        // }

        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        // // Baixa as peerDependencies do pacote remoto
        // $remotePackage = file_get_contents('https://unpkg.com/@luminix/mui-cms@0.1.9/package.json');
        // $remotePackageJson = json_decode($remotePackage, true);

        // if (isset($remotePackageJson['peerDependencies'])) {
        //     $initialPackageJson['peerDependencies'] = array_merge(
        //         (array)$initialPackageJson['peerDependencies'],
        //         $remotePackageJson['peerDependencies']
        //     );
        // }

        // // Atualiza o arquivo package.json com as peerDependencies
        // file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        $this->artisan('luminix:admin-ui', ['--force' => true])
            ->assertExitCode(0);

        // Verificações:    
        // 1. Verifica se o package.json foi alterado corretamente
        $modifiedPackageJson = json_decode(file_get_contents($packageJsonPath), true);

        $this->assertEquals($expectedPackageJson, $modifiedPackageJson);

        // 2. Verifica se os arquivos e diretórios foram publicados
        $jsPath = base_path('resources/js/luminix-admin.jsx');
        $viewsPath = base_path('resources/views/vendor/admin');
        $this->assertFileExists($jsPath, 'O arquivo luminix-admin.jsx não foi publicado.');
        $this->assertDirectoryExists($viewsPath, 'O diretório de views não foi publicado.');

        // $this->assertFileExists($packageJsonPath, 'O arquivo package.json não existe.');

        // Apaga os arquivos gerados
        $this->deleteDirectory($packageJsonPath);
        $this->deleteDirectory(base_path('resources/js'));
        $this->deleteDirectory(base_path('resources/views/vendor/admin'));
    }

    public function test_command_without_force_executes_successfully()
    {
        // Cria o arquivo package.json com o conteudo do esqueleto do laravel
        // /vendor/orchestra/testbench-core/laravel/package.json

        $packageJsonPath = base_path('package.json');

        // Conteúdo inicial do package.json (esqueleto do Laravel)
        $initialPackageJson = [
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
            ]
        ];

        $expectedPackageJson = [
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
                '@luminix/core' => '^0.3.1',
                '@luminix/mui-cms' => '^0.1.9',
                '@luminix/react' => '^0.3.1',
                '@luminix/support' => '^0.4.3',
                '@mui/icons-material' => '^5.16.5',
                '@mui/material' => '^5.16.5',
                'i18next' => '^23.12.2',
                'my-dependency' => '^1.0.0',
                'react' => '^18.3.1',
                'react-dom' => '^18.3.1',
                'react-i18next' => '^15.0.1',
                'react-router-dom' => '6.25.1'
            ],
            'devDependencies' => [
                'autoprefixer' => '^10.4.20',
                'axios' => '^1.7.4',
            ],
            // 'peerDependencies' => [
            //     '@emotion/react' => '^11.13.0',
            //     '@emotion/styled' => '^11.13.0',
            //     '@fontsource/roboto' => '^5.0.12',
            //     '@luminix/core' => '^0.3.1',
            //     '@luminix/react' => '^0.3.1',
            //     '@luminix/support' => '^0.4.3',
            //     '@mui/icons-material' => '^5.16.5',
            //     '@mui/material' => '^5.16.5',
            //     'i18next' => '^23.12.2',
            //     'react' => '^18.3.1',
            //     'react-dom' => '^18.3.1',
            //     'react-i18next' => '^15.0.1',
            //     'react-router-dom' => '6.25.1'
            // ],
        ];

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

            $this->assertEquals($expectedPackageJson, $modifiedPackageJson);

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

    // Teste para atualizar o arquivo package.json (luminix:admin-ui --force)
    public function test_command_executes_successfully_updates_versions()
    {
        $packageJsonPath = base_path('package.json');

        $initialPackageJson = [
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
            // 'peerDependencies' => []
        ];

        file_put_contents($packageJsonPath, json_encode($initialPackageJson, JSON_PRETTY_PRINT));

        $this->artisan('luminix:admin-ui', ['--force' => true])->assertExitCode(0);

        $updatedPackageJson = json_decode(file_get_contents($packageJsonPath), true);

        $this->assertArrayHasKey('@luminix/mui-cms', $updatedPackageJson['dependencies']);
        $this->assertNotEquals('^1.0.0', $updatedPackageJson['dependencies']['@luminix/mui-cms']);

        $this->assertArrayNotHasKey('jest', $updatedPackageJson['devDependencies'] ?? [], "DevDependency 'jest' deveria ter sido removida.");

        // $this->assertArrayHasKey('name', $updatedPackageJson);
        // $this->assertEquals('laravel', $updatedPackageJson['name']);

        // Apaga os arquivos gerados
        $this->deleteFile($packageJsonPath);
        $this->deleteDirectory(base_path('resources/js'));
        $this->deleteDirectory(base_path('resources/views/vendor/admin'));
    }
}
