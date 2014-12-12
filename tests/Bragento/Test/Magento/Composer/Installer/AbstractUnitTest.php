<?php
/**
 * AbstractUnitTest.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Test\Magento\Composer\Installer;

/**
 * Class AbstractUnitTest
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
abstract class AbstractUnitTest extends AbstractTest
{
    abstract protected function getTestObject();
} 
