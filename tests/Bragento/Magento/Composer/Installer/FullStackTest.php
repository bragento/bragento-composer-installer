<?php
/**
 * FullStackTest.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Test\Magento\Composer\Installer;

require_once 'tests/Bragento/Magento/Composer/Installer/TestIO.php';
require_once 'tests/Bragento/Magento/Composer/Installer/TestApplication.php';

use Bragento\Magento\Composer\Installer\Project\Config;
use Composer\Composer;
use Composer\Installer;
use Composer\IO\IOInterface;


/**
 * Class FullStackTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Test\Magento\Composer\Installer
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class FullStackTest extends AbstractTest
{
    const COMPOSER_CONFIG_DIR = 'files/composer/configs/';
    const CHECKS_DIR = 'checks/';

    const MODE_UPDATE = 'update';
    const MODE_INSTALL = 'install';

    const CHECK_TYPE_FILES_EXIST = 'files_exist';

    const BUILD_DIR = 'build/';

    /**
     * _testForMagento
     *
     * files to test for magento core installation
     *
     * @var array
     */
    protected $_testForMagento;

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
            'var/test',
            'media/test',
            'app/etc/local.xml',
            '.gitignore',
            'randomtestfile'
        );

    /**
     * _io
     *
     * @var IOInterface
     */
    protected $_io;

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
        $this->_testForMagento = $this->getChecks(
            self::MODE_INSTALL,
            self::CHECK_TYPE_FILES_EXIST,
            'magentocore.json'
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        chdir($this->_origWorkingDir);
    }

    /**
     * provideComposerConfigFileNames
     *
     * @return array
     */
    public function provideConfigFileNames()
    {
        return array(
            array('magentocore.json'),
            array('somemodules.json'),
            array('changedmageroot.json'),
            array('magealllatest.json')
        );
    }

    /**
     * testComposerInstall
     *
     * @param $configFileName
     *
     * @return void
     *
     * @dataProvider provideConfigFileNames
     * @group slow
     */
    public function testAll($configFileName)
    {
        // run install
        $this->install($configFileName);

        //check installation
        $this->checkFiles($this->_testForMagento);

        //check additional files from config file
        $this->checkFiles(
            $this->getChecks(
                self::MODE_INSTALL,
                self::CHECK_TYPE_FILES_EXIST,
                $configFileName
            )
        );

        // create files for backup test
        $this->createFiles($this->_persistentTestFiles);

        // run update
        $this->update($configFileName);

        //check installation
        $this->checkFiles($this->_testForMagento);

        //check additional files from config file
        $this->checkFiles(
            $this->getChecks(
                self::MODE_UPDATE,
                self::CHECK_TYPE_FILES_EXIST,
                $configFileName
            )
        );

        // check if files were backed up
        $this->checkFiles($this->_persistentTestFiles);
    }

    /**
     * checkFiles
     *
     * @param array $files
     *
     * @return void
     */
    protected function checkFiles(array $files)
    {
        foreach ($files as $file) {
            $this->assertFileExists(
                $this->getMagentoRootDir() .
                DIRECTORY_SEPARATOR .
                $file
            );
        }
    }

    /**
     * getMagentoRootDir
     *
     * @return string
     */
    protected function getMagentoRootDir()
    {
        return Config::getInstance()->getMagentoRootDir()->getPathname();
    }

    /**
     * createFiles
     *
     * @param array $files
     *
     * @return void
     */
    protected function createFiles(array $files)
    {
        foreach ($files as $file) {
            touch(
                $this->getMagentoRootDir() .
                DIRECTORY_SEPARATOR .
                $file
            );
        }
    }

    /**
     * getChecks
     *
     * @param $mode
     * @param $type
     * @param $configFileName
     *
     * @return array
     */
    protected function getChecks($mode, $type, $configFileName)
    {
        $checkFile = $this->getTestDir(
            self::COMPOSER_CONFIG_DIR .
            $mode . DIRECTORY_SEPARATOR .
            self::CHECKS_DIR .
            $type . DIRECTORY_SEPARATOR .
            $configFileName
        );

        if (!file_exists($checkFile)) {
            return array();
        }

        if (null === ($jsonObj = file_get_contents($checkFile))) {
            return array();
        }

        return (array)json_decode($jsonObj);
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

        Config::init($composer);

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
