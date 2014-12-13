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
        return $this->getFilesystem();
    }

    /**
     * @dataProvider provideSymlinkTestFiles
     */
    public function testCreateSymlinks($source, $destination)
    {
        $this->toBuildDir();
        $this->createTestFile($source);
        $this->getTestObject()->symlink($source, $destination);

        $this->assertEquals(
            $this->getAbsPath($source),
            $this->getAbsPath($destination)
        );
    }
}
