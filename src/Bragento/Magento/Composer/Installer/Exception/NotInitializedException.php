<?php
 /**
 * NotInitializedException.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Exception
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Exception;
use Exception;


/**
 * Class NotInitializedException
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Exception
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class NotInitializedException extends \Exception
{
    /**
     * @param mixed     $object
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($object, $code = 0, Exception $previous = null)
    {
        $message = sprintf('%s not initialized', get_class($object));
        parent::__construct($message, $code, $previous);
    }

} 
