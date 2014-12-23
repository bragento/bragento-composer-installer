<?php
/**
 * DeployStrategyNotFoundException.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;

/**
 * Class DeployStrategyNotFoundException
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class DeployStrategyNotFoundException extends \Bragento\Magento\Composer\Installer\Exception
{
    protected $messagePrefix = 'Deploy Strategy not found: ';
}
