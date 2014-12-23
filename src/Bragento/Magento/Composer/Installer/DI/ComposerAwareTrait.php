<?php
/**
 * ComposerAwareTrait.php
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

trait ComposerAwareTrait
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * setComposer
     *
     * @param Composer $composer
     *
     * @return void
     */
    public function setComposer(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * getComposer
     *
     * @return Composer
     */
    public function getComposer()
    {
        return $this->composer;
    }
}
