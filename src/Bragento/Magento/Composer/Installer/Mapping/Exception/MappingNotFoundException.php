<?php
/**
 * MappingNotFoundException.php
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

use Composer\Package\PackageInterface;
use Exception;


/**
 * Class MappingNotFoundException
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Mapping\Exception
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class MappingNotFoundException extends MappingException
{
    public function __construct(
        PackageInterface $package,
        $code = 0,
        Exception $previous = null
    ) {
        $message = sprintf(
            'mapping for package %s not found',
            $package->getName()
        );
        parent::__construct($message, $code, $previous);
    }

} 
