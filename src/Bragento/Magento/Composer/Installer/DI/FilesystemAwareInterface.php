<?php
/**
 * FilesystemAwareInterface.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\DI;

use Symfony\Component\Filesystem\Filesystem;

interface FilesystemAwareInterface
{
    /**
     * setFilesystem
     *
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * getFilesystem
     *
     * @return Filesystem
     */
    public function getFilesystem();
}
