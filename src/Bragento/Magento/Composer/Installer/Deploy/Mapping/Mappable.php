<?php
/**
 * Mappable.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Mapping;

interface Mappable
{
    /**
     * setMapping
     *
     * @param array $mapping
     *
     * @return void
     */
    public function setMapping(array $mapping);
}
