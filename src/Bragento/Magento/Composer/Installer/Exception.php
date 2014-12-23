<?php
/**
 * Exception.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer;

use Underscore\Types\Arrays;

/**
 * Class Exception
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Exception extends \Exception
{
    protected $messagePrefix = '';
    protected $messageSuffix = '';

    public function __construct(
        $message = "",
        $code = 0,
        \Exception $previous = null
    ) {
        Arrays::implode([
            $this->messagePrefix,
            $message,
            $this->messageSuffix
        ]);
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
