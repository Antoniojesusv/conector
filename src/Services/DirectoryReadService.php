<?php

namespace App\Services;

class DirectoryReadService
{
    public function readFilesFromDirectory($directory)
    {
        return $this->loopTreeDirectory($directory);
    }

    private function loopTreeDirectory($currentDirectory, $files = [], $directories = [])
    {
        $dir = dir($currentDirectory);

        while (($element = $dir->read()) !== false) {
            preg_match('/^\.\.|\.$/', $element, $match);
            if (empty($match)) {
                $fileOrDirectory = $currentDirectory . DIRECTORY_SEPARATOR . $element;
                if (is_dir($fileOrDirectory)) {
                    $directories[] = $fileOrDirectory;
                } else {
                    $files[] = $fileOrDirectory;
                }
            }
        }

        if (!empty($directories)) {
            $dir->close();

            foreach ($directories as $directory) {
                $temporalFiles = $this->loopTreeDirectory($directory);
                $files = array_merge($files, $temporalFiles);
            }
        }

        return $files;
    }
}
