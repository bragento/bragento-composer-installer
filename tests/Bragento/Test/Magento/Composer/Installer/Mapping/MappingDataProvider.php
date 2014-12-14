<?php
 /**
 * AbstractMappingDataProvider.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Test\Magento\Composer\Installer\Mapping;

use Bragento\Test\Magento\Composer\Installer\AbstractTest;

/**
 * Class AbstractMappingDataProvider
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Test\Magento\Composer\Installer\Mapping
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
abstract class MappingDataProvider extends AbstractTest
{
    public function testMappingsDataProvider()
    {
        return array(
            array(
                array('app/etc/' => 'app/etc/'),
                array(),
                array('app/etc' => 'app/etc')
            ),
            array(
                array('app/etc/modules' => 'app/etc/'),
                array(),
                array('app/etc/modules' => 'app/etc/modules')
            ),
            array(
                array(
                    'src/app/code/local/Vendor/Module/*' => 'app/code/local/Vendor/Module/'
                ),
                array(
                    'src/app/code/local/Vendor/Module/etc/config.xml',
                    'src/app/code/local/Vendor/Module/Model/Test',
                    'src/app/code/local/Vendor/Module/Block/Test',
                    'src/app/code/local/Vendor/Module/controllers/TestController'
                ),
                array(
                    'src/app/code/local/Vendor/Module/Block' => 'app/code/local/Vendor/Module/Block',
                    'src/app/code/local/Vendor/Module/Model' => 'app/code/local/Vendor/Module/Model',
                    'src/app/code/local/Vendor/Module/controllers' => 'app/code/local/Vendor/Module/controllers',
                    'src/app/code/local/Vendor/Module/etc' => 'app/code/local/Vendor/Module/etc'
                )
            )
        );
    }
}
