<?php
/**
 * ComposerAwareInterface.php
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

use Composer\Composer;

interface ComposerAwareInterface
{
    /**
     * setComposer
     *
     * @param Composer $composer
     *
     * @return void
     */
    public function setComposer(Composer $composer);

    /**
     * getComposer
     *
     * @return Composer
     */
    public function getComposer();
}
