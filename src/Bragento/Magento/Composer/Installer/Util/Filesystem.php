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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
class Filesystem extends \Composer\Util\Filesystem
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * rrmdir
     *
     * @param string $dirPath path of the dir to recursively remove
     *
     * @return bool
     */
    public function rremove($dirPath)
    {
        if (file_exists($dirPath)) {
            if (is_file($dirPath) || is_link($dirPath)) {
                return unlink($dirPath);
            }
            foreach (
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $dirPath,
                        \FilesystemIterator::SKIP_DOTS
                    ),
                    \RecursiveIteratorIterator::CHILD_FIRST
                ) as $path
            ) {
                /* @var \SplFileInfo $path */
                if ($path->isLink() || $path->isFile()) {
                    unlink($path->getPathname());
                } elseif ($path->isDir()) {
                    rmdir($path->getPathname());
                }
            }
            return rmdir($dirPath);
        }
        return false;
    }

    /**
     * emptyDir
     *
     * @param string $dir dir path to empty
     *
     * @return void
     */
    public function emptyDir($dir)
    {
        $this->rremove($dir);
        $this->ensureDirectoryExists($dir);
    }

    /**
     * rcopy
     *
     * @param $srcPath
     * @param $destPath
     *
     * @return bool
     */
    public function rcopy($srcPath, $destPath)
    {
        if (!is_dir($srcPath)) {
            $this->ensureDirectoryExists(dirname($destPath));
            return copy($srcPath, $destPath);
        }

        $dir = opendir($srcPath);
        $this->ensureDirectoryExists($destPath);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($srcPath . self::DS . $file)) {
                    $this->rcopy(
                        $srcPath . self::DS . $file,
                        $destPath . self::DS . $file
                    );
                } else {
                    copy(
                        $srcPath . self::DS . $file,
                        $destPath . self::DS . $file
                    );
                }
            }
        }
        closedir($dir);
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
        return implode(
            DIRECTORY_SEPARATOR,
            array_merge(
                preg_split('/\\|\//', $path, null, PREG_SPLIT_NO_EMPTY),
                preg_split('/\\|\//', $name, null, PREG_SPLIT_NO_EMPTY)
            )
        );
    }

    /**
     * symlink
     *
     * @param $src
     * @param $dest
     *
     * @return bool
     */
    public function symlink($src, $dest)
    {
        $this->ensureDirectoryExists(dirname($dest));

        // Windows doesn't allow relative symlinks
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // get relative path to cwd
            $src = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $src);
            $src = $this->getRelativePath($dest, $src);
        }

        $target = rtrim($dest, '\\/');
        if (file_exists($target)) {
            if (is_link($target)) {
                if (realpath(readlink($dest)) == realpath($src)) {
                    return true;
                } else {
                    unlink($target);
                    return symlink($src, $target);
                }
            }
            return false;
        } else {
            return symlink($src, $target);
        }
    }

    /**
     * Returns the relative path from $from to $to
     *
     * This is utility method for symlink creation.
     * Orig Source: http://stackoverflow.com/a/2638272/485589
     */
    public function getRelativePath($from, $to)
    {
        // Can't use realpath() here since the destination doesn't exist yet
        $from = str_replace(array('/./', '//'), '/', $from);
        $from = explode('/', $from);

        $to = str_replace(array('/./', '//'), '/', $to);
        $to = explode('/', $to);

        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
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
     * removeSymlink
     *
     * @param $link
     *
     * @return bool
     */
    public function removeSymlink($link)
    {
        if (file_exists($link)) {
            if (is_link($link)) {
                return unlink($link);
            }
        }
        return false;
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
        $this->ensureDirectoryExists($dirPath);
        $finder = new Finder();
        $finder->in(dirname($dirPath))
            ->name(basename($dirPath))
            ->depth('== 0')
            ->directories();

        foreach ($finder as $dirPath) {
            return $dirPath;
        }
    }

    /**
     * getFile
     *
     * @param $dirPath
     * @param $fileName
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
    }
} 
