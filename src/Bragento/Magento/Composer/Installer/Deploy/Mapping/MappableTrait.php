<?php
/**
 * MappableTrait.php
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

/**
 * Trait MappableTrait
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Mapping
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
trait MappableTrait
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * setMapping
     *
     * @param array $mapping
     *
     * @return void
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * getMapping
     *
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }
}
