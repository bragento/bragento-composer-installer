<?php
/**
 * RequiredConfigKeyNotDefinedException.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Project
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Project\Exception;

use Exception;


/**
 * Class RequiredConfigKeyNotDefinedException
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Project
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class ConfigKeyNotDefinedException extends \Exception
{
    public function __construct($key, $code = 0, Exception $previous = null)
    {
        $message = sprintf('required config key %s not defined', $key);
        parent::__construct($message, $code, $previous);
    }

} 
