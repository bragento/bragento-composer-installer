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
    private $filesystem;

    protected function setUp()
    {
        parent::setUp();
        $this->getFilesystem()->emptyDirectory($this->getBuildDir());
    }

    protected function toBuildDir()
    {
        chdir($this->getBuildDir());
    }

    /**
     * getBuildDir
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    protected function getBuildDir()
    {
        return $this->getFilesystem()->getDir($this->getTestDir(self::BUILD_ROOT));

    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if (null === $this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
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
     * @param $path
     */
    protected function createTestFile($path)
    {
        if (count($this->getFilesystem()->getPathParts($path)) > 1) {
            $this->getFilesystem()->mkdir(dirname($path), 0755, true);
        }

        touch($path);
    }

    /**
     * getAbsPath
     *
     * @param $path
     *
     * @return string
     */
    protected function getAbsPath($path)
    {
        return realpath($path);
    }

    protected function tearDown()
    {
        chdir($this->getOriginalCwd());
        $this->getFilesystem()->emptyDirectory($this->getBuildDir());
        parent::tearDown();
    }

    protected function getOriginalCwd()
    {
        return dirname(realpath(__FILE__));
    }


}
