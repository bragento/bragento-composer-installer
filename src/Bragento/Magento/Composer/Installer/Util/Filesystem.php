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
use Symfony\Component\Config\Definition\Exception\Exception;
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
     * ensureDirectoryExists
     *
     * @param $dir
     *
     * @return void
     */
    public function ensureDirectoryExists($dir)
    {
        if ($this->exists($dir)) {
            return;
        }

        $this->mkdir($dir);
    }

    /**
     * rcopy
     *
     * @param string $srcPath
     * @param string $destPath
     *
     * @return void
     */
    public function rcopy($srcPath, $destPath)
    {
        $srcPath = $this->normalizePath($srcPath);
        $destPath = $this->normalizePath($destPath);

        if (String::endsWith($destPath, DIRECTORY_SEPARATOR)) {
            $destPath .= basename($srcPath);
        }

        if (is_dir($srcPath)) {
            $this->copyDir($srcPath, $destPath);
        } else {
            $this->copy($srcPath, $destPath, true);
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
            DIRECTORY_SEPARATOR,
            $path
        );

        do {
            $path = str_replace('//', '/', $path);
        } while (strpos($path, '//'));

        return $path;
    }

    /**
     * copyDir
     *
     * @param $srcPath
     * @param $destPath
     *
     * @return void
     */
    public function copyDir($srcPath, $destPath)
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $srcPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($it as $src => $object) {
            if (is_file($src)) {
                $this->copy(
                    $src,
                    $this->joinFileUris(
                        $destPath,
                        $this->rmAbsPathPart($src, $srcPath)
                    )
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
        $prefix = $this->startsWithDs($path) ? DIRECTORY_SEPARATOR : '';
        $suffix = $this->endsWithDs($name) ? DIRECTORY_SEPARATOR : '';

        return $prefix . implode(
            DIRECTORY_SEPARATOR,
            array_merge(
                $this->getPathParts($path),
                $this->getPathParts($name)
            )
        ) . $suffix;
    }

    /**
     * startsWithDs
     *
     * @param $path
     *
     * @return bool
     */
    protected function startsWithDs($path)
    {
        return String::startsWith($path, '/')
        || String::startsWith($path, '\\');
    }

    /**
     * endsWithDs
     *
     * @param $path
     *
     * @return bool
     */
    protected function endsWithDs($path)
    {
        return String::endsWith($path, '/')
        || String::endsWith($path, '\\');
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

        foreach (
            $this->getPathParts(
                $this->normalizePath($root)
            ) as $rootPart
        ) {
            if (count($pathParts) && $rootPart === $pathParts[0]) {
                array_shift($pathParts);
            }
        }

        return implode(DIRECTORY_SEPARATOR, $pathParts);
    }

    /**
     * symlink
     *
     * @param string $src
     * @param string $dest
     *
     * @return bool
     */
    public function symlink($src, $dest, $copyOnWindows = true)
    {
        $src = $this->normalizePath($src);
        $dest = $this->normalizePath($dest);

        if ($this->endsWithDs($dest)) {
            $dest = rtrim($dest, DIRECTORY_SEPARATOR);
        }
        parent::symlink(
            $this->getRelativePath($dest, $src), $dest, $copyOnWindows
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
     * removeSymlink
     *
     * @param string $link
     *
     * @return void
     */
    public function removeSymlink($link)
    {
        parent::remove($link);
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
} 
