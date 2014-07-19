<?php
/**
 * AbstractTest.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   ${NAMESPACE}
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Test\Magento\Composer\Installer;

use Bragento\Magento\Composer\Installer\Util\Filesystem;

/**
 * Class AbstractTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   ${NAMESPACE}
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ROOT_DIR = 'root';

    const BUILD_ROOT = 'build';

    const TEST_FILES_DIR = 'files';

    /**
     * _fs
     *
     * @var Filesystem
     */
    private $_fs;

    protected function setUp()
    {
        parent::setUp();
        $this->getFs()->emptyDir($this->getTestDir('build'));
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        if (null === $this->_fs) {
            $this->_fs = new Filesystem();
        }

        return $this->_fs;
    }


    /**
     * getVfsDir
     *
     * @param $url
     *
     * @return string
     */
    protected function getTestDir($url)
    {
        return TEST_BASE_DIR .
        DIRECTORY_SEPARATOR .
        self::TEST_ROOT_DIR .
        DIRECTORY_SEPARATOR .
        $url;
    }

    /**
     * getBuildDir
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    protected function getBuildDir()
    {
        return $this->getFs()->getDir($this->getTestDir('build'));

    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->getFs()->emptyDir($this->getTestDir('build'));
    }


}
