<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Luminix\Backend\Facades\Finder;
use Workbench\App\Tests\TestCase;
use Workbench\App\Models\User;

class UiCommandTest extends TestCase
{
    public function test_artisan_command_is_registered()
    {
        $this->assertArrayHasKey('luminix:admin-ui', Artisan::all());
    }

    // public function test_command_executes_successfully()
    // {
    //     // criar o arquivo package.json com o conteudo do esqueleto do laravel
    //     // /vendor/orchestra/testbench-core/laravel/package.json

    //     // deverá juntar as peerDependencies do pacote abaixo
    //     // https://unpkg.com/@luminix/mui-cms@0.1.9/package.json

    
    //     $this->artisan('luminix:admin-ui', ['--force' => true])
    //         //->expectsOutput('Admin UI generated successfully.')
    //         ->assertExitCode(0);


    //     // deverá ter:
    //     //   alterado o package.json
    //     //   publicado a pasta skeleton/js para /vendor/orchestra/testbench-core/laravel/resources/js
    //     //   publicado a pasta skeleton/views para /vendor/orchestra/testbench-core/laravel/resources/views/vendor/admin

    //     // apagar os arquivos gerados
    // }

}
