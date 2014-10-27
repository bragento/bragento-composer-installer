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
     * rrmdir
     *
     * @param string $dirPath path of the dir to recursively remove
     *
     * @return bool
     */
    public function rremove($dirPath)
    {
        if (false === file_exists($dirPath)) {
            return false;
        }

        if (is_file($dirPath) || is_link($dirPath)) {
            return unlink($dirPath);
        }

        $dirIt = new \RecursiveDirectoryIterator(
            $dirPath,
            \FilesystemIterator::SKIP_DOTS
        );

        $rIt = new \RecursiveIteratorIterator(
            $dirIt,
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($rIt as $path) {
            $this->rm($path);
        }

        return rmdir($dirPath);
    }

    /**
     * rm
     *
     * @param \SplFileInfo $path
     *
     * @return boolean
     */
    public function rm(\SplFileInfo $path)
    {
        if (false === $this->rmIfLinkOrFile($path->getPathname())) {
            return rmdir($path->getPathname());
        }

        return true;
    }

    /**
     * rmIfLinkOrFile
     *
     * @param string $path
     *
     * @return bool
     */
    public function rmIfLinkOrFile($path)
    {
        if (is_file($path) || is_link($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * rcopy
     *
     * @param string $srcPath
     * @param string $destPath
     *
     * @return boolean
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
        return true;
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
     * @param string $src
     * @param string $dest
     *
     * @return bool
     */
    public function symlink($src, $dest)
    {
        $this->ensureDirectoryExists(dirname($dest));

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
     * @param $path
     *
     * @return array
     */
    public function getPathParts($path)
    {
        $path = trim(trim($path, '/\\'));
        return explode('/', str_replace(array('/./', '//'), '/', $path));
    }

    /**
     * removeSymlink
     *
     * @param string $link
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
} 
