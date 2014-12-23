<?php
/**
 * Modman.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser;

use Composer\Package\Package;

/**
 * Class Modman
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Mapping\Parser
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Modman implements Parsable
{
    const MODMAN_FILENAME = 'modman';

    /**
     * parse
     *
     * @param Package $package
     *
     * @return array
     */
    public function parse(Package $package)
    {
        // TODO: Implement parse() method.
    }
}
