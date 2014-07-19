<?php
/**
 * FullStackTest.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Test\Magento\Composer\Installer;

require_once 'tests/Bragento/Magento/Composer/Installer/TestIO.php';
require_once 'tests/Bragento/Magento/Composer/Installer/TestApplication.php';

use Composer\Composer;
use Composer\Installer;
use Composer\IO\IOInterface;


/**
 * Class FullStackTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class FullStackTest extends AbstractTest
{
    const COMPOSER_CONFIG_DIR = 'files/composer/configs/';
    const MODE_UPDATE = 'update';
    const MODE_INSTALL = 'install';

    protected $_testForMagento
        = array(
            'app/Mage.php',
            'LICENSE.txt',
            'index.php',
            'app/design/frontend/base/default/template/catalog/product/view.phtml'
        );

    /**
     * _persistentTestFiles
     *
     * files and directories that should persist
     * over core update
     *
     * @var array
     */
    protected $_persistentTestFiles
        = array(
            'build/magento/var/test',
            'build/magento/media/test',
            'build/magento/app/etc/local.xml',
            'build/magento/.gitignore',
            'build/magento/randomtestfile'
        );

    /**
     * _io
     *
     * @var IOInterface
     */
    protected $_io;

    /**
     * _composer
     *
     * @var Composer
     */
    protected $_composer;

    /**
     * origWorkingDir
     *
     * @var string
     */
    protected $_origWorkingDir;

    protected function setUp()
    {
        parent::setUp();
        $this->_io = new TestIO();
        $this->_origWorkingDir = getcwd();
        $workingDir = $this->getTestDir('build');
        chdir($workingDir);
    }

    protected function tearDown()
    {
        parent::tearDown();
        chdir($this->_origWorkingDir);
    }


    /**
     * testComposerInstall
     *
     * @param $configFileName
     *
     * @return void
     *
     * @dataProvider provideInstallConfigFileNames
     */
    public function testAll($configFileName)
    {
        // run install
        $this->install($configFileName);

        // create files for backup test
        foreach ($this->_persistentTestFiles as $file) {
            touch($this->getTestDir($file));
        }

        // run update
        $this->update($configFileName);

        // test for magento core installation
        foreach ($this->_testForMagento as $file) {
            $this->assertFileExists(
                $this->getTestDir(sprintf('build/magento/%s', $file))
            );
        }

        //test if persistent files backup worked
        foreach ($this->_persistentTestFiles as $file) {
            $this->assertFileExists(
                $this->getTestDir($file)
            );
        }
    }

    /**
     * provideComposerConfigFileNames
     *
     * @return array
     */
    public function provideInstallConfigFileNames()
    {
        return array(
            array('magentocore.json'),
            array('somemodules.json')
        );
    }

    /**
     * initComposer
     *
     * @param $configFileName
     * @param $mode
     *
     * @return \Composer\Composer
     */
    protected function getComposer($configFileName, $mode)
    {
        $this->copyComposerConfigFileToBuildDir($configFileName, $mode);
        $app = new TestApplication();
        $app->setIo($this->_io);
        $composer = $app->getComposer();
        //$composer->getPluginManager()->addPlugin(new Plugin());

        return $composer;
    }

    /**
     * getComposerConfig
     *
     * @param        $name
     * @param string $mode
     *
     * @return mixed
     */
    protected function getComposerConfigFile(
        $name,
        $mode = self::MODE_INSTALL
    ) {
        return $this->getFs()->getFile(
            $this->getTestDir(self::COMPOSER_CONFIG_DIR . $mode),
            $name
        );
    }

    /**
     * copyComposerConfigFileToBuildDir
     *
     * @param        $name
     * @param string $mode
     *
     * @return void
     */
    protected function copyComposerConfigFileToBuildDir(
        $name,
        $mode = self::MODE_INSTALL
    ) {
        copy(
            $this->getComposerConfigFile($name, $mode),
            $this->getTestDir('build/composer.json')
        );
    }

    /**
     * install
     *
     * @param $configFileName
     *
     * @return void
     */
    protected function install($configFileName)
    {
        Installer::create(
            $this->_io,
            $this->getComposer(
                $configFileName,
                self::MODE_INSTALL
            )
        )->run();
    }

    /**
     * update
     *
     * @param $configFileName
     *
     * @return void
     */
    protected function update($configFileName)
    {
        Installer::create(
            $this->_io,
            $this->getComposer(
                $configFileName,
                self::MODE_UPDATE
            )
        )->setUpdate(true)->run();
    }

} 
