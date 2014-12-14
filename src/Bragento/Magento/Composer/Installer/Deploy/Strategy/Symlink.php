<?php
/**
 * Symlink.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;

use Bragento\Magento\Composer\Installer\Project\Config;

/**
 * Class Symlink
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Symlink extends AbstractStrategy
{
    /**
     * createDelegate
     *
     * @param string $src
     * @param string $dest
     *
     * @return void
     */
    protected function createDelegate($src, $dest)
    {
        if ($this->getFs()->exists($dest)) {
            if (!$override = Config::getInstance()->isForcedOverride()) {
                $override = $this->getIo()
                    ->ask(sprintf("Destination already exists. Replace %s ? [y/n] ",
                            $dest));
            }
            if ($override) {
                $this->getFs()->remove($dest);
            } else {
                return;
            }
        }

        $this->getFs()->ensureDirectoryExists(dirname($dest));
        $this->getFs()->symlink($src, $dest);
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
        if ($this->getFs()->exists($delegate) && is_link($delegate)) {
            $this->getFs()->remove($delegate);
        }
    }
}
