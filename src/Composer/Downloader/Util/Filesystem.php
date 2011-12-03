<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Downloader\Util;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Filesystem
{
    public function removeDirectory($directory)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            system(sprintf('rmdir /S /Q %s', escapeshellarg(realpath($directory))));
        } else {
            system(sprintf('rm -rf %s', escapeshellarg($directory)));
        }
    }

    public function ensureDirectoryExists($directory)
    {
        if (!is_dir($directory)) {
            if (file_exists($directory)) {
                throw new \RuntimeException(
                    $directory.' exists and is not a directory.'
                );
            }
            if (!mkdir($directory, 0777, true)) {
                throw new \RuntimeException(
                    $directory.' does not exist and could not be created.'
                );
            }
        }
    }

    /**
     * Returns the shortest path from $from to $to
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function findShortestPath($from, $to)
    {
        if (!$this->isAbsolutePath($from) || !$this->isAbsolutePath($to)) {
            throw new \InvalidArgumentException('from and to must be absolute paths');
        }

        if (dirname($from) === dirname($to)) {
            return './'.basename($to);
        }
        $from = strtr($from, '\\', '/');
        $to = strtr($to, '\\', '/');

        $commonPath = dirname($to);
        while (strpos($from, $commonPath) !== 0 && '/' !== $commonPath && !preg_match('{^[a-z]:/$}i', $commonPath)) {
            $commonPath = strtr(dirname($commonPath), '\\', '/');
        }

        if (0 !== strpos($from, $commonPath) || '/' === $commonPath) {
            return $to;
        }

        $commonPath = rtrim($commonPath, '/') . '/';
        $sourcePathDepth = substr_count(substr($from, strlen($commonPath)), '/');
        $commonPathCode = str_repeat('../', $sourcePathDepth);
        return $commonPathCode . substr($to, strlen($commonPath));
    }

    /**
     * Returns PHP code that, when executed in $from, will return the path to $to
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function findShortestPathCode($from, $to)
    {
        if (!$this->isAbsolutePath($from) || !$this->isAbsolutePath($to)) {
            throw new \InvalidArgumentException('from and to must be absolute paths');
        }

        if ($from === $to) {
            return '__FILE__';
        }
        $from = strtr($from, '\\', '/');
        $to = strtr($to, '\\', '/');

        $commonPath = dirname($to);
        while (strpos($from, $commonPath) !== 0 && '/' !== $commonPath && !preg_match('{^[a-z]:/$}i', $commonPath)) {
            $commonPath = strtr(dirname($commonPath), '\\', '/');
        }

        if (0 !== strpos($from, $commonPath) || '/' === $commonPath) {
            return var_export($to, true);
        }

        $commonPath = rtrim($commonPath, '/') . '/';
        $sourcePathDepth = substr_count(substr($from, strlen($commonPath)), '/');
        $commonPathCode = str_repeat('dirname(', $sourcePathDepth).'__DIR__'.str_repeat(')', $sourcePathDepth);
        return $commonPathCode . '.' . var_export('/' . substr($to, strlen($commonPath)), true);
    }

    /**
     * Checks if the given path is absolute
     *
     * @param string $path
     * @return Boolean
     */
    public function isAbsolutePath($path)
    {
        return substr($path, 0, 1) === '/' || substr($path, 1, 1) === ':';
    }
}
