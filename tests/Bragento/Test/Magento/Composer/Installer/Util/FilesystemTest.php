<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 10/25/14
 * Time: 3:37 PM
 */

namespace Bragento\Test\Magento\Composer\Installer\Util;

class FilesystemTest extends FilesystemDataProvider
{
    protected $_originalCwd;

    /**
     * testJoinFilePath
     *
     * @param $path
     * @param $name
     * @param $expected
     *
     * @return void
     *
     * @dataProvider joinFilePathsProvider
     */
    public function testJoinFilePath($path, $name, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getTestObject()->joinFileUris($path, $name)
        );
    }

    /**
     * getObject
     *
     * @return \Bragento\Magento\Composer\Installer\Util\Filesystem
     */
    protected function getTestObject()
    {
        return $this->getFs();
    }

    /**
     * @dataProvider provideSymlinkTestFiles
     */
    public function testCreateSymlinks($src, $dest)
    {
        $this->_originalCwd = getcwd();
        chdir($this->getBuildDir());

        $this->createTestFile($src);
        $this->getTestObject()->symlink($src, $dest);

        $this->assertEquals(
            $this->getAbsPath($src),
            $this->getAbsPath($dest)
        );

        chdir($this->_originalCwd);
    }
} 
