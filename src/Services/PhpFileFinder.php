<?php

namespace Stan\Services;

class PhpFileFinder {
    public function findPhpFiles($path) {
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, -1);
        }
        if (is_file($path) && 'php' == pathinfo($path, PATHINFO_EXTENSION)) {
            return [$path];
        } else if (is_dir($path)) {
            $phpFiles = [];
            foreach (scandir($path) as $child) {
                if (!in_array($child, ['.', '..'])) {
                    foreach ($this->findPhpFiles("$path/$child") as $file) {
                        $phpFiles[] = $file;
                    }
                }
            }
            return $phpFiles;
        } else {
            return [];
        }
    }
}
