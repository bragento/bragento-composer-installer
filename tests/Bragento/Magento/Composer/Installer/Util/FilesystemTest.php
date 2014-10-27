<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 10/25/14
 * Time: 3:37 PM
 */

namespace Bragento\Test\Magento\Composer\Installer\Util;


use Bragento\Test\Magento\Composer\Installer\AbstractTest;

class FilesystemTest extends AbstractTest
{
    protected $_originalCwd;

    /**
     * provideSymlinkTestFiles
     *
     * @return array
     */
    public function provideSymlinkTestFiles()
    {
        return array(
            array('test1dir/test1file', 'test1linkdir/test1link'),
            array('test2file', 'test2linkdir/test2link'),
            array('test3dir/test3file', 'test3linkfile')
        );
    }


    /**
     * @dataProvider provideSymlinkTestFiles
     */
    public function testCreateSymlinks($from, $to)
    {
        $this->_originalCwd = getcwd();
        chdir($this->getBuildDir());

        $this->createFile($from);
        $this->getFs()->symlink($from, $to);

        $this->assertEquals(
            $this->getAbsPath($from),
            $this->getAbsPath($to)
        );

        chdir($this->_originalCwd);
    }

    /**
     * @param $path
     */
    protected function createFile($path)
    {
        if (($dir = dirname($path)) && dirname($path) !== '.') {
            mkdir($dir, 0777, true);
        }
        $origCwd = getcwd();
        chdir(dirname($path));
        touch(basename($path));
        chdir($origCwd);
    }

    protected function getAbsPath($path)
    {
        $cwd = getcwd();
        chdir(realpath(dirname($path)));
        $absPath = realpath(basename($path));
        chdir($cwd);
        return $absPath;
    }
} 
