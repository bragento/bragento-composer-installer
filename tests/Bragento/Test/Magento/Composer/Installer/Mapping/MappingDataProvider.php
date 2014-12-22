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
                array('app/etc/Test_Module.xml'),
                array('app/etc/Test_Module.xml' => 'app/etc/Test_Module.xml')
            ),
            array(
                array(
                    'app/etc/modules' => 'app/etc/modules/',
                    'dir' => 'app/etc/'
                ),
                array(
                    'app/etc/modules/Test_Module.xml',
                    'dir/Test_Module.xml'
                ),
                array(
                    'app/etc/modules/Test_Module.xml' => 'app/etc/modules/Test_Module.xml',
                    'dir/Test_Module.xml' => 'app/etc/Test_Module.xml'
                )
            ),
            array(
                array(
                    'src/app/code/local/Vendor/Module/*' => 'app/code/local/Vendor/Module/'
                ),
                array(
                    'src/app/code/local/Vendor/Module/etc/config.xml',
                    'src/app/code/local/Vendor/Module/Model/Test.php',
                    'src/app/code/local/Vendor/Module/Block/Test.php',
                    'src/app/code/local/Vendor/Module/controllers/TestController.php'
                ),
                array(
                    'src/app/code/local/Vendor/Module/Block/Test.php' => 'app/code/local/Vendor/Module/Block/Test.php',
                    'src/app/code/local/Vendor/Module/Model/Test.php' => 'app/code/local/Vendor/Module/Model/Test.php',
                    'src/app/code/local/Vendor/Module/controllers/TestController.php' => 'app/code/local/Vendor/Module/controllers/TestController.php',
                    'src/app/code/local/Vendor/Module/etc/config.xml' => 'app/code/local/Vendor/Module/etc/config.xml'
                )
            )
        );
    }
}
