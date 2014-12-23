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

use Exception;
use Underscore\Types\Arrays;

/**
 * Class DeployStrategyNotFoundException
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class DeployStrategyNotFoundException extends \Exception
{
    public function __construct(
        $deployStrategy,
        $code = 0,
        Exception $previous = null
    ) {
        $message = Arrays::implode([
            'Deploy Strategy not found: ',
            $deployStrategy
        ]);
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
