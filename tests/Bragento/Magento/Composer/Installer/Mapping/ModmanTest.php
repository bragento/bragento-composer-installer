<?php
/**
 * ModmanTest.php
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

namespace Bragento\Test\Magento\Composer\Installer\Mapping;

use Bragento\Magento\Composer\Installer\Mapping\Modman;
use Composer\Package\Package;

/**
 * Class ModmanTest
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   ${NAMESPACE}
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class ModmanTest extends AbstractMappingTest
{
    const MODMAN_TEST_FILE_DIR = 'files/mappings/modman';

    /**
     * testParseCorrectFile
     *
     * @param $filename
     * @param $mappingsCount
     *
     * @return void
     *
     * @dataProvider provideCorrectModmanFileNames
     */
    public function testParseCorrectModmanFiles($filename, $mappingsCount)
    {
        $this->copyModmanFileToBuildDir($filename);
        $mapping = new Modman(
            $this->getBuildDir(),
            new Package('test/package', '1.0.0', '1.0.0')
        );
        $this->assertEquals(
            $mappingsCount,
            count($mapping->getMappingsArray())
        );
    }

    /**
     * provideModmanFileNames
     *
     * * array(
     * *    array($filename, $mappingscount)
     * * )
     *
     * @return array
     */
    public function provideCorrectModmanFileNames()
    {
        return array(
            array('correct', 1),
            array('ignoredlines', 2)
        );
    }

    /**
     * testParseErrorModmanFile
     *
     * @return void
     *
     * @expectedException \Bragento\Magento\Composer\Installer\Mapping\Exception\MappingParseException
     */
    public function testParseErrorModmanFile()
    {
        $this->copyModmanFileToBuildDir('error');
        $modmanMapping = new Modman(
            $this->getBuildDir(),
            new Package('test/package', '1.0.0', '1.0.0')
        );
        $modmanMapping->getResolvedMappingsArray();
    }

    /**
     * getModmanFile
     *
     * @param $name
     *
     * @return mixed
     */
    protected function getModmanFile($name)
    {
        return $this->getFs()->getFile(
            $this->getTestDir(self::MODMAN_TEST_FILE_DIR),
            $name
        );
    }

    protected function copyModmanFileToBuildDir($name)
    {
        copy($this->getModmanFile($name), $this->getTestDir('build/modman'));
    }
} 
