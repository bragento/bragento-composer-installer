<?php
/**
 * IOInterfaceAwareInterface.php
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

use Composer\IO\IOInterface;

/**
 * Interfaceŝ IOInterfaceAwareInterface
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\DI
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
interface IOInterfaceAwareInterface
{
    /**
     * setIOInterface
     *
     * @param IOInterface $io
     *
     * @return void
     */
    public function setIOInterface(IOInterface $io);

    /**
     * getIOInterface
     *
     * @return IOInterface
     */
    public function getIOInterface();
}
