<?php
/**
 * Filesystem.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Util
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Util;

use Bragento\Magento\Composer\Installer\Project\Config;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Filesystem
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Util
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * emptyDir
     *
     * @param string $dir dir path to empty
     *
     * @return void
     */
    public function emptyDirectory($dir)
    {
        if ($this->exists($dir)) {
            $this->remove($dir);
        }
        $this->ensureDirectoryExists($dir);
    }

    /**
     * rrmdir
     *
     * @param string $path path of the file or dir to remove
     *
     * @return void
     */
    public function remove($path)
    {
        if (false === file_exists($path)) {
            return;
        }

        if (is_file($path) || is_link($path)) {
            parent::remove($path);
        } else {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $path,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($it as $subPath) {
                parent::remove($subPath);
            }

            parent::remove($path);
        }
    }

    /**
     * ensureDirectoryExists
     *
     * @param $dir
     *
     * @return void
     */
    public function ensureDirectoryExists($dir)
    {
        if (!$this->exists($dir)) {
            $this->mkdir($dir);
        }
    }

    /**
     * rcopy
     *
     * @param string $srcPath
     * @param string $destPath
     * @param bool   $override
     *
     * @return void
     */
    public function copy($srcPath, $destPath, $override = false)
    {
        $srcPath = $this->normalizePath($srcPath);
        $destPath = $this->normalizePath($destPath);

        if ($this->endsWithDs($destPath)) {
            $destPath = $this->joinFileUris($destPath, basename($srcPath));
        }

        if (is_dir($srcPath)) {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $srcPath,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($it as $src => $object) {
                if (is_file($src)) {
                    parent::copy(
                        $src,
                        $this->joinFileUris(
                            $destPath,
                            $this->rmAbsPathPart($src, $srcPath)
                        ),
                        $override
                    );
                } elseif (is_dir($src)) {
                    $this->mkdir(
                        $this->joinFileUris(
                            $destPath,
                            $this->rmAbsPathPart($src, $srcPath)
                        )
                    );
                }
            }
        } else {
            parent::copy($srcPath, $destPath, $override);
        }
    }

    /**
     * normalizePath
     *
     * @param $path
     *
     * @return mixed
     */
    public function normalizePath($path)
    {
        $path = str_replace(
            array('/./', '\\.\\', '\\'),
            self::DS,
            $path
        );

        $doubleDs = self::DS . self::DS;
        do {
            $path = str_replace($doubleDs, self::DS, $path);
        } while (strpos($path, $doubleDs));

        return $path;
    }

    /**
     * endsWithDs
     *
     * @param $path
     *
     * @return bool
     */
    public function endsWithDs($path)
    {
        return String::endsWith($path, '/')
        || String::endsWith($path, '\\');
    }

    /**
     * joinFileUris
     *
     * @param $path
     * @param $name
     *
     * @return string
     */
    public function joinFileUris($path, $name)
    {
        $prefix = $this->startsWithDs($path) ? self::DS : '';
        $suffix = $this->endsWithDs($name) ? self::DS : '';
        return $this->normalizePath(
            $prefix . implode(
                self::DS,
                array_merge(
                    $this->getPathParts($path),
                    $this->getPathParts($name)
                )
            ) . $suffix
        );
    }

    /**
     * startsWithDs
     *
     * @param $path
     *
     * @return bool
     */
    public function startsWithDs($path)
    {
        return String::startsWith($path, '/')
        || String::startsWith($path, '\\');
    }

    /**
     * getPathParts
     *
     * @param $path
     *
     * @return array
     */
    public function getPathParts($path)
    {
        return preg_split(
            '/\\\|\//',
            $this->normalizePath($path),
            null,
            PREG_SPLIT_NO_EMPTY
        );
    }

    /**
     * rmAbsPathPart
     *
     * @param $path
     * @param $root
     *
     * @return string
     * @throws \ErrorException
     */
    public function rmAbsPathPart($path, $root)
    {
        $pathParts = $this->getPathParts(
            $this->normalizePath($path)
        );

        foreach ($this->getPathParts($this->normalizePath($root)) as $rootPart) {
            if (count($pathParts) && $rootPart === $pathParts[0]) {
                array_shift($pathParts);
            }
        }

        return implode(self::DS, $pathParts);
    }

    /**
     * symlink
     *
     * @param string $source
     * @param string $destination
     * @param bool   $copyOnWindows
     *
     * @return void
     */
    public function symlink($source, $destination, $copyOnWindows = true)
    {
        $source = $this->normalizePath($source);
        $destination = $this->normalizePath($destination);

        parent::symlink(
            $this->getRelativePath($destination, $source),
            $destination,
            $copyOnWindows
        );
    }

    /**
     * Returns the relative path from $from to $to
     *
     * This is utility method for symlink creation.
     * Orig Source: http://stackoverflow.com/a/2638272/485589
     *
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public function getRelativePath($from, $to)
    {
        $from = $this->getPathParts($from);
        $to = $this->getPathParts($to);

        $relPath = $to;
        foreach ($from as $depth => $dir) {
            if ($dir === $to[$depth]) {
                array_shift($relPath);
            } else {
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }

    /**
     * getDir
     *
     * @param string $dirPath
     *
     * @return SplFileInfo
     */
    public function getDir($dirPath)
    {
        $this->mkdir($dirPath);
        $finder = new Finder();
        $finder->in(dirname($dirPath))
            ->name(basename($dirPath))
            ->depth('== 0')
            ->directories();

        foreach ($finder as $dirPath) {
            return $dirPath;
        }

        return null;
    }

    /**
     * getFile
     *
     * @param string $dirPath
     * @param string $fileName
     *
     * @return SplFileInfo
     */
    public function getFile($dirPath, $fileName)
    {
        $finder = new Finder();
        $finder
            ->in($dirPath)
            ->name($fileName)
            ->depth('== 0');

        foreach ($finder as $file) {
            return $file;
        }

        return null;
    }

    /**
     * removeTrailingDs
     *
     * @param $path
     *
     * @return string
     */
    public function removeTrailingDs($path)
    {
        return rtrim($path, '/\\');
    }

    /**
     * isEmptyDir
     *
     * @param $dir
     *
     * @return bool
     */
    public function isEmptyDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $iterator = new FilesystemIterator($dir);
        return !$iterator->valid();
    }
}
