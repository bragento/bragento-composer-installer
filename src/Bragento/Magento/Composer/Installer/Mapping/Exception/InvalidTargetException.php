<?php
/**
 * InvalidTargetException.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping\Exception
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Mapping\Exception;

use Exception;


/**
 * Class InvalidTargetException
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping\Exception
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class InvalidTargetException extends \Exception
{
    public function __construct(
        $targetType,
        $code = 0,
        Exception $previous = null
    ) {
        $message = sprintf('invalid target type: %s', $targetType);
        parent::__construct($message, $code, $previous);
    }


} 
