<?php
/**
 * Service.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\App;
use Bragento\Magento\Composer\Installer\DI\ComposerAwareInterface;
use Bragento\Magento\Composer\Installer\DI\ComposerAwareTrait;
use Bragento\Magento\Composer\Installer\DI\IOInterfaceAwareInterface;
use Bragento\Magento\Composer\Installer\DI\IOInterfaceAwareTrait;
use Composer\Package\Package;

/**
 * Class Service
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Service implements ComposerAwareInterface, IOInterfaceAwareInterface
{
    use ComposerAwareTrait;
    use IOInterfaceAwareTrait;

    /**
     * deploy
     *
     * @param Package     $package
     *
     * @return void
     */
    public function deploy(Package $package)
    {
        App::getStrategyLoader()
            ->loadDeployStrategy($package)
            ->deploy();
    }
}
