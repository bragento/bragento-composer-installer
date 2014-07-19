<?php
/**
 * Copy.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;


/**
 * Class Copy
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Copy extends AbstractStrategy
{
    /**
     * createDelegate
     *
     * @param string $src
     * @param string $dest
     *
     * @return mixed
     */
    protected function createDelegate($src, $dest)
    {
        $this->getFs()->ensureDirectoryExists(dirname($dest));
        $this->getFs()->rcopy($src, $dest);
    }

    /**
     * removeDelegate
     *
     * @param string $delegate
     *
     * @return mixed
     */
    protected function removeDelegate($delegate)
    {
        $this->getFs()->rremove($delegate);
    }

} 
