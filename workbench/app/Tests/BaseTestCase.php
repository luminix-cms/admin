<?php

namespace Workbench\App\Tests;

use Workbench\App\Tests\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * Apaga um arquivo se ele existir.
     *
     * @param string $file Caminho para o arquivo.
     */
    protected function deleteFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Apaga um diretório e todo o seu conteúdo (arquivos e subdiretórios).
     *
     * @param string $directory Caminho para o diretório.
     */
    protected function deleteDirectory($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = array_diff(scandir($directory), ['.', '..']); // Ignora "." e ".."

        foreach ($files as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath); // Recursivamente apaga subdiretórios
            } else {
                $this->deleteFile($filePath);
            }
        }

        rmdir($directory);
    }
}
