<?php

namespace Luminix\Admin\Console\Commands;

use Illuminate\Console\Command;
use Luminix\Admin\AdminServiceProvider;
use Luminix\Admin\Support\Unpkg;

class UiCommand extends Command
{

    protected $signature = 'luminix:admin-ui
                            {--force : Overwrite any existing files without asking}';

    protected $description = 'Publishes the admin UI to your application';

    public function handle()
    {
        $forced = $this->option('force');

        $peerDeps = json_decode(file_get_contents(Unpkg::url('package.json')), true)['peerDependencies'] ?? [];
        $dependencies = $peerDeps + [
            "@luminix/mui-cms" => "^" . AdminServiceProvider::CMS_VERSION,
        ];

        ksort($dependencies);

        if (!$forced) {
            $table = array_map(function ($key) use ($dependencies) {
                return [
                    'name' => $key,
                    'version' => $dependencies[$key],
                ];
            }, array_keys($dependencies));

            $this->info('Following dependencies will be installed or updated:');
            $this->newLine(1);
            $this->table(
                ['Name', 'Version'],
                $table,
            );
        }

        if ($forced || $this->confirm('Do you wish to continue?', true)) {
            $currentPackageJson = json_decode(file_get_contents(base_path('package.json')), true);
            $currentPackageJson['dependencies'] = ($currentPackageJson['dependencies'] ?? []) + $dependencies;

            $devDeps = array_keys($currentPackageJson['devDependencies'] ?? []);

            foreach ($devDeps as $key) {
                if (in_array($key, array_keys($dependencies))) {
                    unset($currentPackageJson['devDependencies'][$key]);
                }
            }

            ksort($currentPackageJson['dependencies']);

            $json = json_encode($currentPackageJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

            $result = file_put_contents(base_path('package.json'), $json);

            if ($result === false) {
                $this->error('Failed to publish the admin UI. Please check your permissions.');
                return 1;
            }
        }

        $this->call('vendor:publish', [
            '--provider' => AdminServiceProvider::class,
            '--tag' => 'luminix-ui',
            '--force' => $forced,
        ]);

        if (!$forced) {
            $this->info('Admin UI published successfully.');
            $this->info('Make sure to add the following entry point in [vite.config.js]:');

            $this->newLine(1);

            $this->line('export default defineConfig({');
            $this->line('    plugins: [');
            $this->line('        laravel({');
            $this->line('            input: [');
            $this->line('                // Other entry points...');
            $this->info('+               "resources/js/luminix-admin.jsx",');
            $this->line('            ],');
            $this->line('            refresh: true,');
            $this->line('        }),');
            $this->line('        react(),');
            $this->line('    ],');
            $this->line('});');

            $this->newLine(1);

            $this->info('Then, run `npm install && npm run dev`.');
        }

    }
}


